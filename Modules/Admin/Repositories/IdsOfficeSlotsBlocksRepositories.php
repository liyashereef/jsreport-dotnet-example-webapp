<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IdsOfficeSlotBlock;
use Modules\Admin\Models\IdsOffice;
use Modules\Admin\Models\IdsOfficeSlots;
class IdsOfficeSlotsBlocksRepositories
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
     * @param  Modules\Admin\Models\IdsOfficeSlots $idsOfficeSlots
     */
    public function __construct(IdsOfficeSlotBlock $idsOfficeSlotBlock)
    {
        $this->model = $idsOfficeSlotBlock;
    }

    public function store($inputs){
        return $this->model->create($inputs);
    }

    /**
     * Block slots of all office by running laravel command.
     * Command: `php artisan ids:officeslotblock {start_date} {end_date}`.
     * File Path: Modules/IdsScheduling/Console/BlockIDSOfficeSlots.php
     *
     * @param sundays[]  saturday[]
     * @return
     */
    public function storeOnCommand($inputs){

        // $officeIds = IdsOffice::pluck('id')->all();
        $insertData = [];
        // foreach($officeIds as $officeId){
        //     foreach($inputs['sundays'] as $key=>$sunday){

        //         $insertData[$key]['slot_block_date'] = $sunday;
        //         $insertData[$key]['ids_office_id'] = $officeId;
        //         $insertData[$key]['created_by'] = 1;
        //         $insertData[$key]['created_at'] = \Carbon::now();
        //         $insertData[$key]['updated_at'] = \Carbon::now();
        //         $insertData[$key]['ids_office_slot_id'] = null;
        //         $alreadyBlocked = $this->checkAlreadyBlocked($insertData[$key]);
        //         if($alreadyBlocked>0){
        //             unset($insertData[$key]);
        //         }

        //     }
        //     $sundayCount = sizeof($inputs['sundays']);
        //     foreach($inputs['saturdays'] as $saturday){
        //         $insertData[$sundayCount]['slot_block_date'] = $saturday;
        //         $insertData[$sundayCount]['ids_office_id'] = $officeId;
        //         $insertData[$sundayCount]['created_by'] = 1;
        //         $insertData[$sundayCount]['created_at'] = \Carbon::now();
        //         $insertData[$sundayCount]['updated_at'] = \Carbon::now();
        //         $insertData[$sundayCount]['ids_office_slot_id'] = null;
        //         $alreadyBlocked = $this->checkAlreadyBlocked($insertData[$sundayCount]);
        //         if($alreadyBlocked>0){
        //             unset($insertData[$sundayCount]);
        //         }

        //         $sundayCount ++;
        //     }
        //     $this->model->insert($insertData);
        // }
        $officeIds = [4];
        $officeSlots = IdsOfficeSlots::whereIn('ids_office_id',$officeIds)
        ->where('ids_office_timing_id',13)
        ->where('start_time','>=','12:30:00')->where('start_time','<','13:30:00')
        ->select('id','ids_office_timing_id','display_name','ids_office_id','start_time','end_time')
        ->get();



        // dd($officeSlots,$inputs['weekdays']);
        $blockEntry = [];
        foreach($officeSlots as $officeSlot){
            foreach($inputs['weekdays'] as $slotKey=>$days){
                $blockEntry[$slotKey]['slot_block_date'] = $days;
                $blockEntry[$slotKey]['ids_office_id'] = $officeSlot->ids_office_id;
                $blockEntry[$slotKey]['ids_office_slot_id'] = $officeSlot->id;
                $blockEntry[$slotKey]['created_by'] = 1;
                $blockEntry[$slotKey]['created_at'] = \Carbon::now();
                $blockEntry[$slotKey]['updated_at'] = \Carbon::now();

                $alreadyBlocked = $this->checkAlreadyBlocked($blockEntry[$slotKey]);
                if($alreadyBlocked>0){
                    unset($blockEntry[$slotKey]);
                }
            }
            $this->model->insert($blockEntry);
        }

        $fridayBlockingSlots = IdsOfficeSlots::whereIn('ids_office_id',$officeIds)
        ->where('ids_office_timing_id',13)
        ->where('start_time','>=','12:00:00')->where('start_time','<','16:30:00')
        ->select('id','ids_office_timing_id','display_name','ids_office_id','start_time','end_time')
        ->get();

        $fridaysEntry = [];
        foreach($fridayBlockingSlots as $slot){
            foreach($inputs['fridays'] as $key=>$days){
                $fridaysEntry[$key]['slot_block_date'] = $days;
                $fridaysEntry[$key]['ids_office_id'] = $slot->ids_office_id;
                $fridaysEntry[$key]['ids_office_slot_id'] = $slot->id;
                $fridaysEntry[$key]['created_by'] = 1;
                $fridaysEntry[$key]['created_at'] = \Carbon::now();
                $fridaysEntry[$key]['updated_at'] = \Carbon::now();

                $alreadyBlocked = $this->checkAlreadyBlocked($fridaysEntry[$key]);
                if($alreadyBlocked>0){
                    unset($fridaysEntry[$key]);
                }
            }
            $this->model->insert($fridaysEntry);
        }

    }

    public function getAllOfficeBlockedSlot($officeId){
        return $this->model
        ->where('ids_office_id',$officeId)
        ->orderBy('slot_block_date','DESC')
        ->with('IdsOfficeSlots')
        ->get();
    }

    public function getAllBlockedSlots($inputs){
        return $this->model
        ->when(isset($inputs['ids_office_id']), function ($q) use($inputs) {
            return $q->where('ids_office_id',$inputs['ids_office_id']);
        })
        ->when(isset($inputs['start_date']) && isset($inputs['end_date']), function ($q) use($inputs) {
            return $q->whereBetween('slot_block_date', [$inputs['start_date'], $inputs['end_date']]);
        })
        ->orderBy('slot_block_date','DESC')
        ->with('IdsOfficeSlots')
        ->get();
    }

    public function checkAlreadyBlocked($data){
        return $this->model
        ->where('ids_office_id',$data['ids_office_id'])
        ->where('slot_block_date',$data['slot_block_date'])
        ->where(function ($q) use($data) {
            $q->where('ids_office_slot_id',$data['ids_office_slot_id'])
            ->orWhere('ids_office_slot_id', '=',null);
        })
        ->count();
    }

    public function getOfficeBlockedSlot($data){
        return $this->model
        ->where('ids_office_id',$data['ids_office_id'])
        ->whereIn('slot_block_date',$data['date'])
        ->orderBy('slot_block_date')
        ->get();
    }

    public function getAllSlotBlockedByDate($data){
        return $this->model
        ->where('ids_office_id',$data['ids_office_id'])
        ->whereIn('slot_block_date',$data['date'])
        ->whereNull('ids_office_slot_id')
        ->orderBy('slot_block_date')
        ->get();
    }

    public function getAllSlotBlockedByDateAndTimeId($data){
        return $this->model
        ->where('ids_office_id',$data['ids_office_id'])
        ->whereIn('slot_block_date',$data['date'])
        ->whereNull('ids_office_slot_id')
        ->orderBy('slot_block_date')
        ->whereHas('IdsOffice.IdsOfficeTimings',function($query) use($data){
            return $query->whereIn('id',$data['ids_office_timing_id']);
        })
        ->select('id','day_id','slot_block_date','ids_office_id','ids_service_id',
        'ids_office_slot_id','ids_blocking_request_id','active')
        ->get();
    }

    public function destroy($id){
        return $this->model->where('id',$id)->delete();
    }

    public function destroyByIdArray($inputs){
        return $this->model->whereIn('id',$inputs['ids_office_ids'])->delete();
    }

    public function destroyAllOnSlotTimeRemovel($ids_office_slot_ids){
        return $this->model->whereIn('ids_office_slot_id',$ids_office_slot_ids)->delete();
    }


}
