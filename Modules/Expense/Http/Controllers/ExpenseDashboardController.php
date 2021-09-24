<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Expense\Repositories\ExpenseClaimRepository;
use Modules\Expense\Repositories\ExpenseGlRepository;
use Modules\Expense\Repositories\ExpenseCategoryLookupRepository;
use Modules\Expense\Repositories\CostCenterRepository;
use Modules\Expense\Repositories\ExpensePaymentModeRepository;

use Carbon\Carbon;
use Modules\Expense\Models\ExpenseGlCode;
use Modules\Expense\Models\ExpenseCostCenterLookup;
use Modules\Expense\Models\ExpenseClaim;
use Modules\Expense\Models\ExpensePaymentMode;
use Modules\Expense\Models\ExpenseSettingsFinanceControllers;
use Modules\Expense\Models\ExpenseAllowableForUser;
use DB;
use Illuminate\Support\Facades\Auth;

class ExpenseDashboardController extends Controller
{

    protected $expenseClaimRepository;
    public function __construct(
        ExpenseClaimRepository $expenseClaimRepository,
        ExpenseGlCode $expenseGlcode,
        ExpenseCostCenterLookup $expenseCostcenterLookup,
        ExpenseClaim $expenseClaim,
        ExpensePaymentMode $paymentMode,
        ExpenseSettingsFinanceControllers $expenseSettingsFinanceControllers,
        ExpenseGlRepository $expenseGlRepository,
        ExpenseCategoryLookupRepository $expenseCategoryRepository,
        CostCenterRepository $costCenterRepository,
        ExpensePaymentModeRepository $expensePaymentModeRepository
    ) {
        $this->expenseClaimRepository = $expenseClaimRepository;
        $this->expenseGlcode = $expenseGlcode;
        $this->expenseCostcenterLookup = $expenseCostcenterLookup;
        $this->expenseClaim = $expenseClaim;
        $this->paymentMode = $paymentMode;
        $this->expenseSettingsFinanceControllers = $expenseSettingsFinanceControllers;
        $this->expenseGlRepository = $expenseGlRepository;
        $this->expenseCategoryRepository = $expenseCategoryRepository;
        $this->costCenterRepository = $costCenterRepository;
        $this->expensePaymentModeRepository = $expensePaymentModeRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        if (!isset($request->viewmyexpense)) {
            $viewMyExpense = 0;
        } else {
            $viewMyExpense = $request->viewmyexpense;
        }

        $current = Carbon::now();
        if ($current->day > 15) {
            $startdate = $current->firstOfMonth()->addDays(14)->toDateString();
            $enddate   =  $current->firstOfMonth()->addDays(13)->addMonth()->toDateString();
        } else {
            $startdate = $current->firstOfMonth()->addDays(14)->addMonth(-1)->toDateString();
            $enddate   = $current->firstOfMonth()->addMonth(1)->addDays(13)->toDateString();
        }
        $expense_counts = $this->expenseClaimRepository->getExpenseCounts($viewMyExpense, $startdate, $enddate);
        $items = $this->expenseGlcode->pluck('gl_code', 'id')->toArray();
        $items_cost_center = $this->expenseCostcenterLookup->pluck('center_number', 'id')->toArray();
        asort($items_cost_center);
        $items_payment_mode = $this->paymentMode->pluck('mode_of_payment', 'id')->toArray();
        $finance_controller_status = $this->expenseSettingsFinanceControllers
        ->where('financial_controller', \Auth::id())->count();
        $user_list = $this->expenseClaimRepository->employeeLookUps();
        return view(
            'expense::expense_dashboard',
            compact(
                'expense_counts',
                'startdate',
                'enddate',
                'items',
                'items_cost_center',
                'items_payment_mode',
                'finance_controller_status',
                'viewMyExpense',
                'user_list'
            )
        );
    }

    /**
     * Display all expense claims.
     *
     */
    public function getList(Request $request)
    {
        $enddate = $request->enddate;
        $startdate = $request->startdate;
        $status = empty($request->status)?'':$request->status;
        $employee = empty($request->employee)?'':$request->employee;
        $viewmyexpense = $request->viewmyexpense;
        $expense_list = $this->expenseClaimRepository->getAll($viewmyexpense, $startdate, $enddate, $status, $employee);
        return datatables()->of($this->expenseClaimRepository
        ->prepareDataForExpenseClaim($expense_list))
        ->addIndexColumn()
        ->toJson();
    }

    /**
     * Show the form fourl
     * @return Responseurl
     */
    public function create()
    {
        return view('expense::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('expense::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $result     = $this->expenseClaimRepository->getSingle($id);
        $expense_id = $id;
        $gl_code    = $this->expenseGlRepository->getList();
        $category   = $this->expenseCategoryRepository->getList();
        $cost_center = $this->costCenterRepository->getList();
        $payment_mode = $this->expensePaymentModeRepository->getList();
        $finance_controller_status = $this->expenseSettingsFinanceControllers
        ->where('financial_controller', \Auth::id())->count();
        $approver_status = ExpenseAllowableForUser::where('user_id', $result->created_by)
        ->where('reporting_to_id', \Auth::id())->count();
        return view(
            'expense::view',
            compact(
                'result',
                'gl_code',
                'category',
                'cost_center',
                'payment_mode',
                'finance_controller_status',
                'approver_status',
                'expense_id'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $this->expenseClaimRepository->updateExpenseClaim($request);
            DB::commit();
            return response()->json(array('success' => true));
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }

    public function getCounts($viewmyexpense, $startdate, $enddate, $employee = null)
    {
        $employee = empty($employee)?'':$employee;
        return $this->expenseClaimRepository->getExpenseCounts($viewmyexpense, $startdate, $enddate, $employee);
    }

    public function expenseApprovalReminderMail()
    {
        try {
            DB::beginTransaction();
            $this->expenseClaimRepository->expenseApprovalReminderMail();
            DB::commit();
            return response()->json(array('success' => true));
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false));
        }
    }
}
