<?php

namespace Modules\Facility\Repositories;

use Modules\Facility\Models\FacilityServiceLockdown;

class FacilityServiceLockdownRepository 
{
    protected $model;

    public function __construct(){
        $this->model = new FacilityServiceLockdown();
    }

    public function getList($inputs){ 
        return $this->model
        ->when(!empty($inputs) && isset($inputs['model_type']), function ($que) use($inputs){
            return $que->where('model_type',$inputs['model_type']);
        })
        ->when(!empty($inputs) && isset($inputs['model_id']), function ($que) use($inputs){
            return $que->where('model_id',$inputs['model_id']);
        })
        ->when(!empty($inputs) && isset($inputs['end_date']), function ($que) use($inputs){
            return $que->whereDate('start_date','<=',$inputs['end_date']);
        })
        ->when(!empty($inputs) && isset($inputs['start_date']), function ($que) use($inputs){
            return $que->whereDate('end_date','>=',$inputs['start_date']);
        })
        ->orWhere(function($query) use($inputs){
            return $query->whereNull('start_date')
            ->whereNull('end_date')
            ->where('model_type',$inputs['model_type'])
            ->where('model_id',$inputs['model_id']);
        })
        ->orderBy('start_date')
        ->select('id','model_type','model_id','start_date','start_time','end_date','end_time')
        ->get();
    }
    


}