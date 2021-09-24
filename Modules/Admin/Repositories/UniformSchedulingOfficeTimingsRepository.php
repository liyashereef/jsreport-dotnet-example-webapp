<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\UniformSchedulingOfficeTimings;

class UniformSchedulingOfficeTimingsRepository
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
     * @param  Modules\Admin\Models\UniformSchedulingOfficeTimings $uniformSchedulingOfficeTimings
     */
    public function __construct(UniformSchedulingOfficeTimings $uniformSchedulingOfficeTimings)
    {
        $this->model = $uniformSchedulingOfficeTimings;
    }

    public function store($inputs)
    {
        return $this->model->create($inputs);
    }


    public function updateEndDate($inputs)
    {
        return $this->model
            ->where('id', $inputs['timing_id'])
            ->update(['expiry_date' => $inputs['expiry_date'],'updated_by' => \Auth::id()]);
    }

    public function getOfficeTimeEntries($inputs)
    {
        $result = [];
        $result_2 = collect();
        $startDate = $inputs['start_date'];
        $expiryDate = $inputs['expiry_date'];

        $result_1 = collect($this->model->whereRaw(
                "(
                    ((start_date >= ?) and isnull(expiry_date)) or
                    ((start_date <= ?) and isnull(expiry_date)) or
                    ((? between start_date  and expiry_date) and !isnull(expiry_date)) or
                    ((? between start_date  and expiry_date) and !isnull(expiry_date)) or
                    ((start_date >=?) and (expiry_date <=?) and !isnull(expiry_date))
                    and  isnull(deleted_at)
                )",
                [$startDate,$startDate,$startDate,$expiryDate,$startDate,$expiryDate]
            )->where([
                "uniform_scheduling_office_id" => $inputs['uniform_scheduling_office_id']
            ])
            ->get());

            if($expiryDate != null){
                $result_2 = collect($this->model
                ->whereDate('start_date', '>=', $startDate)
                ->whereDate('expiry_date', '<=', $expiryDate)
                ->where('uniform_scheduling_office_id', $inputs['uniform_scheduling_office_id'])
                ->get());
            }else{
                $result_2 = collect($this->model
                ->whereDate('start_date', '>=', $startDate)
                // ->whereDate('expiry_date', '<=', $startDate)
                ->where('uniform_scheduling_office_id', $inputs['uniform_scheduling_office_id'])
                ->get());
            }

            $result = $result_1->merge($result_2);
            return $result;

    }

    public function removeTimings($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function getActiveTimes($inputs){
        return $this->model
        ->where('uniform_scheduling_office_id', $inputs['uniform_scheduling_office_id'])
        ->where('start_date', '<=', $inputs['end_date'])
        ->where('expiry_date', '>=', $inputs['start_date'])
        ->orWhereNull('expiry_date')
        ->where('uniform_scheduling_office_id', $inputs['uniform_scheduling_office_id'])

        // ->with(['IdsOfficeSlots'=>function($que){
        //     $que->select('id',
        //     'ids_office_timing_id',
        //     'display_name',
        //     'uniform_scheduling_office_id',
        //     'start_time',
        //     'end_time',
        //     'active');
        // },
        // 'IdsOfficeSlots.IdsOfficeSlotBlock' => function ($query) use ($inputs) {
        //     // $query->whereIn('slot_block_date', $inputs['date']);
        //     // $query->whereNull('deleted_at');
        //     // $query->select('id', 'ids_office_slot_id', 'slot_block_date');
        // }])
        ->select('id','uniform_scheduling_office_id','start_time','end_time','intervals','start_date','expiry_date')
        ->get();
    }

}
