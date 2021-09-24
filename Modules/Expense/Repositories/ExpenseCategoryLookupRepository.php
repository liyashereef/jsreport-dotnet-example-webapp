<?php

namespace Modules\Expense\Repositories;

use Modules\Expense\Models\ExpenseCategoryLookup;

class ExpenseCategoryLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new PositionLookupLookupRepository instance.
     */
    public function __construct(ExpenseCategoryLookup $expenseCategoryLookupModel)
    {
        $this->model = $expenseCategoryLookupModel;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'name', 'short_name', 'created_at', 'updated_at'])
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get Position lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    }

    /**
     * Display details of single Security Clearance
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created Security Clearance in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        if ($data['is_category_taxable'] == 0) {
            $data['tax_id'] = NULL;
        }
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified Security Clearance from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
