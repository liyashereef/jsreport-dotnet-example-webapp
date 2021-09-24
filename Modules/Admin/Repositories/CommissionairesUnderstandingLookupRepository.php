<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CommissionairesUnderstandingLookup;

class CommissionairesUnderstandingLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CommissionairesUnderstandingLookupRepository instance.
     *
     * @param  \App\Models\CommissionairesUnderstandingLookup $commissionairesUnderstandingLookup
     */
    public function __construct(CommissionairesUnderstandingLookup $commissionairesUnderstandingLookup)
    {
        $this->model = $commissionairesUnderstandingLookup;
    }

    /**
     * Get commissionaires understanding lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'commissionaires_understandings', 'short_name', 'order_sequence', 'created_at', 'updated_at'])->get();
    }

    /**
     * Get commissionaires understanding lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('order_sequence', 'asc')->pluck('commissionaires_understandings', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
