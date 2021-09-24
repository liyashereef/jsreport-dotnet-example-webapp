<?php

namespace Modules\Admin\Http\Controllers\IdsServices;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use App\Services\HelperService;
use Illuminate\Support\Facades\DB;

use Modules\Admin\Models\IdsOffice;
use Modules\Admin\Http\Requests\IdsOfficeRequest;
use Modules\Admin\Http\Requests\IdsOfficeTimingsRequest;
use Modules\Admin\Repositories\IdsOfficeRepository;
use Modules\Admin\Repositories\IdsOfficeSlotsRepositories;
use Modules\IdsScheduling\Repositories\IdsEntriesRepositories;
use Modules\Admin\Repositories\IdsLocationAllocationRepository;
use Modules\Admin\Repositories\IdsOfficeTimingsRepository;
use Modules\Admin\Repositories\IdsOfficeSlotsBlocksRepositories;

class IdsOfficeController extends Controller
{

    public function __construct(
        IdsOfficeRepository $IdsOfficeRepository,
        HelperService $helperService,
        IdsOfficeSlotsRepositories $idsOfficeSlotsRepositories,
        IdsEntriesRepositories $idsEntriesRepositories,
        IdsLocationAllocationRepository $idsLocationAllocationRepository,
        IdsOfficeTimingsRepository $idsOfficeTimingsRepository,
        IdsOfficeSlotsBlocksRepositories $idsOfficeSlotsBlocksRepository
    ) {
        $this->repository = $IdsOfficeRepository;
        $this->idsOfficeSlotsRepositories = $idsOfficeSlotsRepositories;
        $this->idsEntriesRepositories = $idsEntriesRepositories;
        $this->idsLocationAllocationRepository = $idsLocationAllocationRepository;
        $this->idsOfficeTimingsRepository = $idsOfficeTimingsRepository;
        $this->idsOfficeSlotsBlocksRepository = $idsOfficeSlotsBlocksRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::ids-scheduling.office');
    }

    /**
     * list all data.
     * @return Response
     */
    public function getAll()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(IdsOfficeRequest $request)
    {
        try {
            DB::beginTransaction();
            $inputs = $request->all();

            $start_at = Carbon::parse($request->input('start_time'));
            $end_at   = Carbon::parse($request->input('end_time'));
            $mins    = $end_at->diffInMinutes($start_at, true);
            if($mins >= $request->input('intervals')){
                $result = $this->repository->store($inputs);
                if($inputs['id'] == null){
                    $inputs['ids_office_id'] = $result->id;
                    $inputs['expiry_date'] = null;
                    $this->officeTimingsMgt($inputs);
                }
            }else{
                $return['success'] = false;
                $return['message'] = 'Interval should not be greater than start time & end time duration';
                return response()->json($return);
            }

            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $inputs['ids_office_id'] = $id;
            $booking = $this->idsEntriesRepositories->getBookings($inputs);
            if ($booking >= 1) {
                $return = array('warning' => true, 'message' => "Office slot booking exists");
                return response()->json($return);
            } else {
                $this->repository->destroy($id);
                $this->idsOfficeSlotsRepositories->destroyByOfficeId($id);
                $this->idsLocationAllocationRepository->unallocateByOfficeId($id);
            }

            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function storeIdsTimings(IdsOfficeTimingsRequest $request)
    {
        try {
            DB::beginTransaction();
            $result = [];
            $return['message'] = '';
            $inputs = $request->all();
            $start_at = Carbon::parse($request->input('start_time'));
            $end_at   = Carbon::parse($request->input('end_time'));
            $mins    = $end_at->diffInMinutes($start_at, true);

            if($mins >= $request->input('intervals')){
                $return = $this->officeTimingsMgt($inputs);
            }else{
                $return['success'] = false;
                $return['message'] = 'Interval should not be greater than start time & end time duration';
            }

            DB::commit();
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function officeTimingsMgt($inputs){
        $return['message'] = '';
        $duplicateTimings = $this->idsOfficeTimingsRepository->getOfficeTimeEntries($inputs);
        // dd($inputs, $duplicateTimings,sizeof($duplicateTimings));
        if (sizeof($duplicateTimings)>=1) {

            $return['success'] = false;
            $return['result'] = null;
            $return['message'] = 'Slots timing/date overlapping.';

        }else{
            $inputs['created_by'] = \Auth::id();
            $result = $this->idsOfficeTimingsRepository->store($inputs);

            //---START---Office Slot storing---------------
            if ($result) {
                $data['office_id'] = $inputs['ids_office_id'];
                $data['start_time'] = $inputs['start_time'];
                $data['end_time'] = $inputs['end_time'];
                $data['intervals'] = $inputs['intervals'];
                $data['ids_office_timing_id'] =$result->id;
                $this->storeSlot($data);
            }
            //---END---Office Slot storing-----------------

            $return['success'] = true;
            $return['result'] = $result;
        }
        return $return;
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function storeSlot($inputs)
    {
        $start_time = strtotime($inputs['start_time']);
        $end_time = strtotime($inputs['end_time']);
        $intervals = $inputs['intervals'];
        $increment_time = $start_time;
        $intervels = '+' . $intervals . ' minutes';

        $params['ids_office_id'] =  $inputs['office_id'];
        $params['ids_office_timing_id'] =  $inputs['ids_office_timing_id'];
        $params['id'] =  null;

        while ($end_time > $increment_time) {
            $params['start_time'] = date("H:i", $increment_time);
            $display_name_first = date("h:i A", $increment_time);

            $increment_time =  date("H:i", strtotime($intervels, $increment_time));
            $params['end_time'] = $increment_time;
            $params['display_name'] = $display_name_first . ' - ' . date("h:i A", strtotime($increment_time));

            $increment_time = strtotime($increment_time);
            //slot end_time must be below of office end time
            if ($end_time >= $increment_time) {
                $this->idsOfficeSlotsRepositories->store($params);
            }
        }
    }

    public function removeIdsTimings($id)
    {
        try {
            DB::beginTransaction();
            $inputs['ids_office_timing_id'] = $id;
            $inputs['slot_booked_date'] = date('Y-m-d');
            $inputs['count'] = true;
            $booking = $this->idsEntriesRepositories->getTimingBookedCount($inputs);
            if ($booking >= 1) {
                $return = array('warning' => true, 'message' => "Office slot booking exists");
                return response()->json($return);
            } else {
                $result =  $this->idsOfficeTimingsRepository->removeTimings($id);
                if ($result) {
                    $slotIds = $this->idsOfficeSlotsRepositories->getAllIdByTimingId($id);
                    $this->idsOfficeSlotsBlocksRepository->destroyAllOnSlotTimeRemovel($slotIds);
                    $this->idsOfficeSlotsRepositories->destroyByOfficeTimingId($id);
                }
            }
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }


    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function updateIdsTimings(Request $request)
    {
        try {
            DB::beginTransaction();
            $result = [];
            $inputs = $request->all();

            $bookingInputs['ids_office_timing_id'] = $request->input('ids_timing_id');
            $bookingInputs['slot_booked_date'] = $request->input('expiry_date');
            $booking = $this->idsEntriesRepositories->getTimingBookedCount($bookingInputs);
            if (!empty($booking)) {
                $return = array('success' => false, 'message' => "Office slot booking exists upto ".\Carbon::parse($booking->slot_booked_date)->format('M d Y'));
                return response()->json($return);
            }else{
                $result = $this->idsOfficeTimingsRepository->updateEndDate($inputs);
            }

            DB::commit();
            $return['success'] = true;
            $return['result'] = $result;
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
