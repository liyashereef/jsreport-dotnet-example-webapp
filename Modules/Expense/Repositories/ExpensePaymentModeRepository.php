<?php

namespace Modules\Expense\Repositories;

use Modules\Expense\Models\ExpensePaymentMode;

class ExpensePaymentModeRepository
{
    public function __construct(ExpensePaymentMode $expensePaymentmodeModel)
    {
        $this->model = $expensePaymentmodeModel;
    }

 /**
     * Get all Values 
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'mode_of_payment', 'reimbursement', 'created_at', 'updated_at'])
            ->orderBy('mode_of_payment', 'asc')
            ->get();
    }
/**
     * Store a newly created Expense Parent Category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

     /**
     * Display details of single Expense Parent Category
     *
     * @param $id
     * @return object
     */

    public function get($id)
    {
        return $this->model->find($id);
    }
    
     /**
     * Remove the specified  from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getList()
    {
        return $this->model->orderBy('mode_of_payment', 'asc')->pluck('mode_of_payment', 'id')->toArray();
    }
}
