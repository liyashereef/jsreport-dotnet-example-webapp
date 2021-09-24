<?php

namespace Modules\IdsScheduling\Repositories;

use Modules\IdsScheduling\Models\IdsTransactionHistory;
use Modules\Admin\Repositories\IdsPaymentMethodsRepository;

class IdsTransactionRepository
{
    protected $model;
    protected $idsPaymentMethodsRepository;

    public function __construct(
        IdsTransactionHistory $model,
        IdsPaymentMethodsRepository $idsPaymentMethodsRepository
    ){
        $this->model = $model;
        $this->idsPaymentMethodsRepository = $idsPaymentMethodsRepository;
    }

    // Transaction history store.
    public function store($inputs){
        return $this->model->create($inputs);
    }

    // Transaction history update.
    public function update($inputs){
        return $this->model->where('id',$inputs['id'])
        ->update(['amount'=>$inputs['amount']]);
    }

    // Transaction history update on Refunds.
    public function createOrUpdateRefund($inputs){
        $refund = $this->model->where('transaction_type','Refund')
        ->where('entry_id',$inputs['entry_id'])->first();
        if($refund){
            $input['id'] = $refund->id;
            $input['amount'] = $inputs['amount'];
            return $this->update($input);
        }else{
            return $this->store($inputs);
        }
    }

    // Transaction history update on Refunds.
    public function createOrUpdateReceive($inputs){
        $refund = $this->model->where('transaction_type','Received')
        ->where('entry_id',$inputs['entry_id'])
        ->whereNull('ids_online_payment_id')
        ->first();
        if($refund){
            $input['id'] = $refund->id;
            $input['amount'] = $inputs['amount'];
            return $this->update($input);
        }else{
            return $this->store($inputs);
        }
    }



    // Transaction history.
    public function updateHistory($inputs){
        // if(isset($inputs['transaction_type']) && $inputs['transaction_type'] == 'Refund'){
        //     return $this->createOrUpdateRefund($inputs);
        // }else{
        //     return $this->createOrUpdateReceive($inputs);
        // }
        $this->deleteWithOutOnlinePayment($inputs['entry_id']);
        return $this->store($inputs);
    }

    public function deleteWithOutOnlinePayment($entryId){
        return $this->model->where('entry_id',$entryId)
        ->whereNull('ids_online_payment_id')
        ->delete();
    }

    public function updateEntryId($inputs){
        return $this->model
        ->where('entry_id',$inputs['old_ids_entry_id'])
        ->update([ 'entry_id'=>$inputs['ids_entry_id'] ]);
    }

    public function deleteRefund($entryId){
        return $this->model->where('entry_id',$entryId)
        ->where('transaction_type','Refund')
        ->delete();
    }

    public function getLastEntry($entryId){
        return $this->model->where('entry_id',$entryId)->orderBy('id','DESC')->first();
    }

}
