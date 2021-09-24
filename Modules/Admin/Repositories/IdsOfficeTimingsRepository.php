<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IdsOfficeTimings;

class IdsOfficeTimingsRepository
{
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\IdsOfficeSlots $idsOfficeSlots
     */
    public function __construct(IdsOfficeTimings $idsOfficeTimings)
    {
        $this->model = $idsOfficeTimings;
    }

    public function store($inputs)
    {
        return $this->model->create($inputs);
    }


    public function updateEndDate($inputs)
    {
        return $this->model
            ->where('id', $inputs['ids_timing_id'])
            ->update([
                'expiry_date' => $inputs['expiry_date'],
                'updated_by' => \Auth::id()
            ]);
    }

    public function getOfficeTimeEntries($inputs)
    {
        // "(
        //     ((start_date >= ?) and isnull(expiry_date)) or
        //     ((start_date <= ?) and isnull(expiry_date)) or
        //     ((? between start_date  and expiry_date) and !isnull(expiry_date)) or
        //     ((? between start_date  and expiry_date) and !isnull(expiry_date)) or
        //     ((start_date >=?) and (expiry_date <=?) and !isnull(expiry_date)) or
        //     ((?<= expiry_date))
        //     and  isnull(deleted_at)
        // )",
        // [$startDate,$startDate,$startDate,$expiryDate,$startDate,$expiryDate,$startDate]

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
            "ids_office_id" => $inputs['ids_office_id']
        ])
            ->get());

        if($expiryDate != null){
            $result_2 = collect($this->model
                ->whereDate('start_date', '>=', $startDate)
                ->whereDate('expiry_date', '<=', $expiryDate)
                ->where('ids_office_id', $inputs['ids_office_id'])
                ->get());
        }else{
            $result_2 = collect($this->model
                ->whereDate('start_date', '>=', $startDate)
                // ->whereDate('expiry_date', '<=', $startDate)
                ->where('ids_office_id', $inputs['ids_office_id'])
                ->get());
        }

        $result = $result_1->merge($result_2);
        return $result;

    }

    public function removeTimings($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function removeTimingSlot($inputs)
    {
        return $this->model
            ->where('id', $inputs['id'])
            ->whereHas('IdsOfficeSlots.IdsEntries', function ($query) {
                return $query->where('slot_booked_date', '>=', date('Y-m-d'));
            })
            ->get();
    }

    public function getActiveTimes($inputs){
        return $this->model
            ->where('ids_office_id', $inputs['ids_office_id'])
            ->where('start_date', '<=', $inputs['end_date'])
            ->where('expiry_date', '>=', $inputs['start_date'])
            ->orWhereNull('expiry_date')
            ->where('ids_office_id', $inputs['ids_office_id'])

            ->with(['IdsOfficeSlots'=>function($que){
                $que->select('id',
                    'ids_office_timing_id',
                    'display_name',
                    'ids_office_id',
                    'start_time',
                    'end_time',
                    'active');
            },
                'IdsOfficeSlots.IdsOfficeSlotBlock' => function ($query) use ($inputs) {
                    // $query->whereIn('slot_block_date', $inputs['date']);
                    // $query->whereNull('deleted_at');
                    // $query->select('id', 'ids_office_slot_id', 'slot_block_date');
                }])
            ->select('id','ids_office_id','start_time','end_time','intervals',
            'start_date','expiry_date','lunch_start_time','lunch_end_time')
            ->get();
    }

}
