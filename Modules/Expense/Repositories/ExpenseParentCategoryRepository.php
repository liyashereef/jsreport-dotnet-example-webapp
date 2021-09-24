<?php

namespace Modules\Expense\Repositories;

use Modules\Expense\Models\ExpenseParentCategory;

class ExpenseParentCategoryRepository
{
    public function __construct(ExpenseParentCategory $expenseParentcategoryModel)
    {
        $this->model = $expenseParentcategoryModel;
    }
 /**
     * Get all Values 
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'parent_category_name', 'short_name', 'created_at', 'updated_at'])
            ->orderBy('parent_category_name', 'asc')
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

}