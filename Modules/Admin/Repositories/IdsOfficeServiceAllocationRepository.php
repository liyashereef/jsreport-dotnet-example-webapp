<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IdsOfficeServiceAllocation;

class IdsOfficeServiceAllocationRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\IdsOffice $idsOffice
     */
    public function __construct(IdsOfficeServiceAllocation $idsOfficeServiceAllocation)
    {
        $this->model = $idsOfficeServiceAllocation;
    }

    /**
     * Get offfice list
     *
     * @param empty
     * @return array
     */

    public function checkOfficeServiceAllocation($inputs){
       return $this->model
       ->where('ids_office_id',$inputs['ids_office_id'])
       ->where('ids_service_id',$inputs['ids_service_id'])
       ->count(); 
    }


    

 
}