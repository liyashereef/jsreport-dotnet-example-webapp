<?php

namespace Modules\Expense\Repositories;

use Modules\Expense\Models\ExpenseTaxMaster;
use Carbon\Carbon;
class TaxMasterRepository
{
    protected $taxMaster;

    public function __construct(ExpenseTaxMaster $taxMaster)
    {
        $this->taxMaster = $taxMaster;
    }


    public function getAll()
    {

        $active_data = $this->taxMaster
        ->has('taxMasterLog')
        ->with('taxMasterLog')
        ->get()
        ->toArray();

        $inactive_data = $this->taxMaster
        ->doesnthave('taxMasterLog')
        ->get()
        ->toArray();

        $data = array_merge($active_data,$inactive_data);
        return $data;
    }

    /**
     * Get single WorkType details
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->taxMaster->find($id);

    }

    /**
     * Store a newly created WorkType in storage.
     *
     * @param  $request
     * @return object
     */
    public function save($data)
    {
        return $this->taxMaster->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Get  Tax list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        $data = $this->taxMaster->whereHas('taxMasterLog', function ($query){
            $query->where('status', 0);
        })->orderBy('name', 'asc')->pluck('name', 'id');
        return $data;
        //return $this->taxMaster->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    }

}
