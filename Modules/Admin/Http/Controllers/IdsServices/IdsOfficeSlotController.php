<?php

namespace Modules\Admin\Http\Controllers\IdsServices;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Illuminate\Support\Carbon;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\SlotBlockingRequest;

use Modules\Admin\Models\IdsOffice;

use Modules\Admin\Http\Requests\IdsOfficeRequest;

use Modules\Admin\Repositories\IdsOfficeRepository;
use Modules\Admin\Repositories\IdsOfficeSlotsRepositories;
use Modules\Admin\Repositories\IdsOfficeSlotsBlocksRepositories;
use Modules\IdsScheduling\Repositories\IdsEntriesRepositories;

class IdsOfficeSlotController extends Controller
{

    public function __construct(
        IdsOfficeRepository $IdsOfficeRepository,
        HelperService $helperService,
        IdsOfficeSlotsRepositories $idsOfficeSlotsRepositories,
        IdsOfficeSlotsBlocksRepositories $idsOfficeSlotsBlocksRepositories,
        IdsEntriesRepositories $idsEntriesRepositories
        )
    {
        $this->idsOfficeRepository = $IdsOfficeRepository;
        $this->repository = $idsOfficeSlotsRepositories;
        $this->idsOfficeSlotsBlocksRepositories = $idsOfficeSlotsBlocksRepositories;
        $this->idsEntriesRepositories = $idsEntriesRepositories;
        $this->helperService = $helperService;
    }

    public function getOfficeSlot($officeId){
        $office = $this->idsOfficeRepository->getById($officeId);
        $slotArray =  $this->repository->getByOfficeId($officeId)->pluck('display_name','id')->toArray();
       return view('admin::ids-scheduling.office-slot',compact('officeId','office','slotArray'));
    }

    public function getAllByOfficeId($officeId){
        return datatables()->of($this->repository->getByOfficeId($officeId))->addIndexColumn()->toJson();  
    }

    public function slotBlocking(SlotBlockingRequest $request){
        try {
            \DB::beginTransaction();
//-- slot blocking-----
                $data['slot_block_date']= $request->input('slot_block_date');
                $data['ids_office_id']= $request->input('ids_office_id');
                $data['created_by']= \Auth::id();

                foreach($request->input('slot_ids') as $slot_id){
                    $data['ids_office_slot_id']= $slot_id;
                    $exists = $this->idsOfficeSlotsBlocksRepositories->checkAlreadyBlocked($data);
                    if($exists == 0){
                        $result = $this->idsOfficeSlotsBlocksRepositories->store($data);
                    } 
                }

            \DB::commit();
         return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        } 
    }

    public function getBlockedSlotPage($officeId){
        $office = $this->idsOfficeRepository->getById($officeId);
        $slotArray =  $this->repository->getByOfficeId($officeId)->pluck('display_name','id')->toArray();
        return view('admin::ids-scheduling.blocked-slot',compact('officeId','office','slotArray'));    
    }

    public function getAllOfficeBlockedSlot($officeId){
        return datatables()->of($this->idsOfficeSlotsBlocksRepositories->getAllOfficeBlockedSlot($officeId))->addIndexColumn()->toJson();  
    }

    public function getAllBlockedSlots(Request $request){
        return $this->idsOfficeSlotsBlocksRepositories->getAllBlockedSlots($request->all());  
    }

    public function slotsBlockingDestroy($id){
        try {
            \DB::beginTransaction();
            $this->idsOfficeSlotsBlocksRepositories->destroy($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function destroyAllByIds(Request $request){
       
        try {
            \DB::beginTransaction();
             $this->idsOfficeSlotsBlocksRepositories->destroyByIdArray($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
    



}
