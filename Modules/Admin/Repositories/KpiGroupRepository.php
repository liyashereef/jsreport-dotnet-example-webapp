<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\KpiGroup;
use Modules\Admin\Models\KpiGroupCustomerAllocation;

class KpiGroupRepository
{
    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\KpiGrouop $model
     */
    public function __construct(KpiGroup $model)
    {
        $this->model = $model;
    }

    /**
     * Get  service list
     *
     * @param empty
     * @return array
     */

    public function getAll()
    {
        return $this->model->with('parent')->get();
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  $request
     * @return object
     */

    public function store($inputs)
    {
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function destroy($id)
    {
        return $this->model->find($id)->delete();
    }

    /**
     * Get root nodes
     * (group has no parent nodes)
     */
    public function getAllRootNodes()
    {
        return $this->model->doesnthave('parent')->get();
    }

    /**
     * Get leaf nodes
     * (nodes without children & has parent)
     */
    public function getAllLeafNodes()
    {
        //Get all group ids
        // $nodeIds = KpiGroup::all()->pluck('id')->toArray();
        $nodeIds = array_unique(KpiGroup::whereNotNull('parent_id')->get()
            ->pluck('parent_id')->toArray());

        //Get groups of non parent role
        return KpiGroup::whereNotIn('id', $nodeIds)
            ->select('id', 'name', 'parent_id')
            ->where('is_active', '=', true)
            ->with(['kpiGroupCustomers' => function ($que) {
                return $que->select('id', 'customer_id', 'kpi_group_id');
            }, 'kpiGroupCustomers.kpiMasterCustomerAllocation' => function ($que) {
                return $que->select('id', 'customer_id', 'kpi_master_id');
            }])
            ->orderBy('name', 'ASC')
            ->get();
    }


    /**
     * Get perent nodes
     * (nodes without children & has parent)
     */
    public function getAllParentNodes()
    {
        return KpiGroup::whereDoesntHave('kpiGroupCustomers')
        ->orderBy('name', 'ASC')
        ->get();
    }

    /**
     * Get child groups of a group
     * @param $groupId
     *  If groupId null get all base parents. otherwise get child of given parent
     * @return object
     */
    public function getChildNodesOfaGroup($groupId)
    {
        return $this->model->where('parent_id', '=', $groupId)->get();
    }

    /**
     * Get all parent nested childs
     * @param $groupId
     *  If groupId null get all base parents. otherwise get child of given parent
     * @return object
     */
    public function getParentAndChildWithCustomers($inputs)
    {

        return $this->model
            ->when(!empty($inputs) && $inputs['activeGroup'] != null, function ($query) use ($inputs) {
                return $query->where('parent_id', (int)$inputs['activeGroup']);
            })->when(!empty($inputs) && $inputs['activeGroup'] == null, function ($query) use ($inputs) {
                return $query->whereNull('parent_id');
            })
            ->with([
                'family',
                'kpiGroupCustomers' => function ($que) use ($inputs) {
                    return $que->select('id', 'customer_id', 'kpi_group_id');
                },
                'kpiGroupCustomers.kpiMasterCustomerAllocation' => function ($que) use ($inputs) {
                    return $que->select('id', 'customer_id', 'kpi_master_id');
                }
            ])
            ->where('is_active', '=', true)
            ->select('id', 'name', 'parent_id')
            ->get();
    }

    public function checkForAllocation($id)
    {
        $res =  $this->model->where('parent_id', '=', $id)->get();
        return  $res->isNotEmpty();
    }

    public function checkForCustomerAllocation($id)
    {
        $res =  KpiGroupCustomerAllocation::where('kpi_group_id','=',$id)->get();
        return  $res->isNotEmpty();
    }
}
