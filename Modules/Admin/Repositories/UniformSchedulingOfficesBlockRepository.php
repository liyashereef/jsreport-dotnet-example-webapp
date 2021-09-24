<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\UniformSchedulingOfficeSlotBlocks;

class UniformSchedulingOfficesBlockRepository
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
     * @param  Modules\Admin\Models\UniformSchedulingOfficeSlotBlocks $uniformSchedulingOfficeSlotBlocks
     */
    public function __construct(UniformSchedulingOfficeSlotBlocks $uniformSchedulingOfficeSlotBlocks)
    {
        $this->model = $uniformSchedulingOfficeSlotBlocks;
    }

    /**
     * Get offfice list
     *
     * @param empty
     * @return array
     */

    public function getAllByOffice($officeId){
       return $this->model
       ->where('uniform_scheduling_office_id',$officeId)
       ->get();
    }

    /**
     * Get offfice list
     *
     * @param empty
     * @return array
     */
    public function store($inputs){
        return $this->model->create($inputs);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id){
        return $this->model->destroy($id);
    }

    public function getList($inputs){
        return $this->model
        ->when(!empty($inputs) && isset($inputs['end_date']), function ($que) use($inputs){
            return $que->whereDate('start_date','<=',$inputs['end_date']);
        })
        ->when(!empty($inputs) && isset($inputs['start_date']), function ($que) use($inputs){
            return $que->whereDate('end_date','>=',$inputs['start_date']);
        })
        ->orWhere(function($query) use($inputs){
            return $query->whereNull('end_date')
            ->whereDate('start_date','>=',$inputs['start_date']);
        })
        ->orderBy('start_date')
        ->select('id','start_date','start_time','end_date','end_time')
        ->get();
    }

    public function getBlockEntries($inputs){ //dd($inputs);
        $result1 = collect($this->model
        ->where(function($query) use($inputs){
            return $query->whereDate('start_date','<=',$inputs['end_date'])
            ->whereDate('end_date','>=',$inputs['start_date'])
            ->where('uniform_scheduling_office_id',$inputs['uniform_scheduling_office_id']);
        })
        ->get());
        $result2 = collect($this->model->where(function($query) use($inputs){
            return $query->whereNull('end_date')
            ->whereDate('start_date','<=',$inputs['end_date'])
            ->where('uniform_scheduling_office_id',$inputs['uniform_scheduling_office_id']);
        }) ->get());

        return  $result1->merge($result2);
    }

    public function checkAlreadyBlocked($inputs){ //dd($inputs);
        $result1 = collect($this->model
        ->where(function($query) use($inputs){
            return $query->whereDate('start_date','<=',$inputs['booked_date'])
            ->whereDate('end_date','>=',$inputs['booked_date'])
            ->where('start_time','<=',$inputs['start_time'])
            ->where('end_time','>=',$inputs['end_time'])
            ->where('uniform_scheduling_office_id',$inputs['uniform_scheduling_office_id']);
        })
        ->get());
        $result2 = collect($this->model->where(function($query) use($inputs){
            return $query->whereNull('end_date')
            ->whereDate('start_date','<=',$inputs['booked_date'])
            ->where('start_time','<=',$inputs['start_time'])
            ->where('end_time','>=',$inputs['end_time'])
            ->where('uniform_scheduling_office_id',$inputs['uniform_scheduling_office_id']);
        }) ->get());

        return  $result1->merge($result2);
    }

    public function updateEndDate($inputs){
        return $this->model->where('id',$inputs['id'])
        ->update(['end_date' => $inputs['end_date'],'updated_by' => \Auth::id()]);
    }

}
