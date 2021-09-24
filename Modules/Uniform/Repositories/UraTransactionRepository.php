<?php

namespace Modules\Uniform\Repositories;

use Illuminate\Http\Request;
use Modules\Admin\Models\User;
use Modules\Uniform\Models\UraTransaction;
use Modules\Uniform\Utils\OperationType;
use Modules\Uniform\Utils\TransactionType;

class UraTransactionRepository
{
    protected $model;
    protected $uraRateRepository;
    protected $uraOperationTypeRepository;
    protected $uraSettingsRepository;

    public function __construct(
        UraTransaction $uraTransaction,
        UraRateRepository $uraRateRepository,
        UraOperationTypeRepository $uraOperationTypeRepository,
        UraSettingsRepository $uraSettingsRepository
    ) {
        $this->model = $uraTransaction;
        $this->uraRateRepository = $uraRateRepository;
        $this->uraOperationTypeRepository = $uraOperationTypeRepository;
        $this->uraSettingsRepository = $uraSettingsRepository;
    }

    protected function withRelations()
    {
        return $this->model->with([
            'user',
            'uraRate',
            'operationType'
        ]);
    }

    public function getAll()
    {
        return  $this->withRelations()->all();
    }

    public function getRecent($hideRevoked, int $limit = 10)
    {
        $q = $this->withRelations()->orderBy('id', 'DESC');
        if ($hideRevoked != null) {
            $q->where('revoked', 0);
        }
        return $q->take($limit)->get();
    }

    public function getUserTransactions(int $userId, $hideRevoked)
    {
        $q = $this->withRelations()->where('user_id', $userId)
            ->orderBy('id', 'DESC');
        if ($hideRevoked != null) {
            $q->where('revoked', 0);
        }

        return $q->get();
    }

    public function getList(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|numeric',
            'hide_revoked' => 'nullable|boolean'
        ]);

        $uid = $request->input('user_id');
        $hideRevoked = $request->input('hide_revoked');

        //TODO::check permissions   
        if ($uid != null) {
            return $this->getUserTransactions($uid, $hideRevoked);
        } else {
            return $this->getRecent($hideRevoked);
        }
        return [];
    }

    public function getUserBalanceInfo($userId)
    {
        $result = [
            'ura_balance' => 0,
            'ura_earned' => 0,
            'ura_hours' => 0,
            'ura_current_rate' => $this->uraRateRepository->getCurrentRate()
        ];

        $user = User::find($userId);
        if ($user != null) {
            $result['ura_balance'] = $user->ura_balance;
            $result['ura_earned'] = $user->ura_earned;
            $result['ura_hours'] = $user->ura_hours;
        }

        return $result;
    }

    public function updateUraBalance($t, $revertMode = false)
    {
        $t = $t->fresh();
        $user = $t->user;
        $uraHours = is_numeric($t->hours) ?  $t->hours : 0;

        //Debit mode
        if ($t->transaction_type == TransactionType::DEBIT) {
            $user->ura_balance  -= $t->amount;
            $user->ura_hours -= $uraHours;

            //Revert mode
            if ($revertMode) {
                $user->ura_earned -= $t->amount;
            }
        }
        //Credit mode
        if ($t->transaction_type == TransactionType::CREDIT) {

            if (!$revertMode) {
                $user->ura_earned += $t->amount;
                $user->ura_hours += $uraHours;
            }
            $user->ura_balance += $t->amount;
        }
        //Save user
        $user->save();

        //Update balance in transaction
        $t->balance = $user->ura_balance;
        $t->save();

        return $user->balance;
    }


    public function store($req)
    {
        $t = $this->model->create([
            'created_by' => auth()->user()->id,
            'user_id' => $req->input('user_id'),
            'ura_operation_id' => $req->input('ura_operation_id'),
            'notes' => $req->input('notes'),
            'amount' => $req->input('amount'),
            'transaction_type' =>  $req->input('transaction_type')
        ]);

        $this->updateUraBalance($t);
    }

    //EmployeeShiftReportEntry
    public function processTimesheetApproval($entry)
    {
        $entry = $entry->fresh();

        //Revert previous transactions related to timesheet
        if ($entry->is_manual == 1) { //Manual timesheet approvel
            //TODO:get corect last updated entry
            $res = $this->model->where('employee_shift_report_entry_id', $entry->id)
                ->orderBy('id', 'DESC')
                ->get();
            if ($res->isNotEmpty()) {
                $this->timesheetReversal($res->first());
            }
        } else { //Auto timesheet approvel
            //TODO:get corect last updated entry
            $res = $this->model->where('employee_shift_payperiod_id', $entry->shift_payperiod_id)
                ->orderBy('id', 'DESC')
                ->get();
            if ($res->isNotEmpty()) {
                $this->timesheetReversal($res->first());
            }
        }

        //If the entry is deleted don't create new transaction
        if ($entry->trashed()) {
            return;
        }

        //TODO:handle no rate defined
        $uraRate = $this->uraRateRepository->getCurrentRateObject();
        $hours = $entry->hours / 60;
        $amount = $hours * $uraRate->amount;

        //Create a timesheet earnings transaction
        $uraTransaction = $this->model->create([
            'created_by' => auth()->user()->id,
            'user_id' => $entry->user_id,
            'employee_shift_payperiod_id' => $entry->shift_payperiod_id,
            'employee_shift_report_entry_id' => $entry->id,
            'transaction_type' => TransactionType::CREDIT,
            'ura_operation_id' => OperationType::TIMESHEET_EARNINGS,
            'hours' => $hours,
            'ura_rate_id' => $uraRate->id,
            'amount' => $amount
        ]);

        $this->updateUraBalance($uraTransaction);
    }

    public function timesheetReversal($oldT)
    {
        //Set old transaction as hidden mode
        $oldT->revoked = true;
        $oldT->save();

        //Set new revoked transaction
        $transaction = $this->model->create([
            'created_by' => auth()->user()->id,
            'user_id' => $oldT->user_id,
            'employee_shift_payperiod_id' => $oldT->shift_payperiod_id,
            'employee_shift_report_entry_id' => $oldT->employee_shift_report_entry_id,
            'transaction_type' => TransactionType::DEBIT,
            'ura_operation_id' => OperationType::TIMESHEET_EARNINGS_REVERT,
            'hours' => $oldT->hours,
            'ura_rate_id' => $oldT->ura_rate_id,
            'amount' => $oldT->amount,
            'revoked' => true //Set current transaction as revoked
        ]);

        $this->updateUraBalance($transaction, true);
    }

    public function canPurchaseUniform($userId, $amount)
    {
        $balanceInfo = $this->getUserBalanceInfo($userId);
        $uraBalance = $balanceInfo['ura_balance'];
        $threshold = $this->uraSettingsRepository->getByKey('uniform-purchase-threshold');

        if ($threshold == null) {
            return false;
        }

        $threshold = -1 * abs($threshold);
        $rm = $uraBalance - $amount;
        return $rm >= $threshold ? true : false;
    }

    public function processUniformPurchase($order)
    {

        // $balanceInfo = $this->getUserBalanceInfo($order->user_id);
        // $balance = $balanceInfo['ura_balance'];
        $amount = $order->price;

        // //Empty ura balance
        // if ($balance <= 0) {
        //     return $amount;
        // }

        // //Paritial ura deduction
        // if ($balance < $order->price) {
        //     $amount = $balance;
        // }

        // //Full ura deduction
        // if ($balance >= $order->price) {
        //     $amount = $order->price;
        // }

        //Create a timesheet earnings transaction
        $transaction = $this->model->create([
            'created_by' => auth()->user()->id,
            'user_id' => $order->user_id,
            'transaction_type' => TransactionType::DEBIT,
            'ura_operation_id' => OperationType::UNIFORM_PURCHASE,
            'uniform_order_id' => $order->id,
            'amount' => $amount
        ]);
        $this->updateUraBalance($transaction);
        return $amount;
    }

    public function processUniformPurchaseCancel($order)
    {
        $res = $this->model->where('uniform_order_id', $order->id)
            ->where('revoked', false)
            ->orderBy('id', 'DESC')
            ->get();

        if ($res->isNotEmpty()) {
            $ot = $res->first();
            $ot->revoked = true;
            $ot->save();

            //Set new revoked transaction
            $transaction = $this->model->create([
                'created_by' => auth()->user()->id,
                'user_id' => $ot->user_id,
                'transaction_type' => TransactionType::CREDIT,
                'ura_operation_id' => OperationType::UNIFORM_PURCHASE_CANCEL,
                'amount' => $ot->amount,
                'revoked' => true //Set current transaction as revoked
            ]);
            $this->updateUraBalance($transaction, true);
            return $transaction;
        }
        return null;
    }

    public function processUniformPurchaseReturn($order)
    {
        $res = $this->model->where('uniform_order_id', $order->id)
            ->where('revoked', false)
            ->orderBy('id', 'DESC')
            ->get();

        if ($res->isNotEmpty()) {
            $ot = $res->first();
            $ot->revoked = true;
            $ot->save();

            //Set new revoked transaction
            $transaction = $this->model->create([
                'created_by' => auth()->user()->id,
                'user_id' => $ot->user_id,
                'transaction_type' => TransactionType::CREDIT,
                'ura_operation_id' => OperationType::UNIFORM_PURCHASE_RETURN,
                'amount' => $ot->amount,
                'revoked' => true //Set current transaction as revoked
            ]);
            $this->updateUraBalance($transaction, true);
            return $transaction;
        }
        return null;
    }
}
