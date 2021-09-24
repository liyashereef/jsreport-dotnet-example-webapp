<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\UniformSchedulingOfficesRequests;
use Modules\Admin\Http\Requests\UniformSchedulingOfficeTimingsRequest;
use Modules\Admin\Repositories\UniformSchedulingOfficesRepository;
use Modules\Admin\Repositories\UniformSchedulingOfficeTimingsRepository;
use Modules\UniformScheduling\Repositories\UniformSchedulingEntriesRepository;
use Modules\Admin\Models\Days;
class UniformSchedulingOfficesController extends Controller
{

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Repositories\UniformSchedulingOfficesRepository $uniformSchedulingOfficesRepository
     * @param  Modules\Admin\Repositories\UniformSchedulingOfficeTimingsRepository $uniformSchedulingOfficeTimingsRepository
     * @param  Modules\UniformScheduling\Repositories\UniformSchedulingEntriesRepository $uniformSchedulingEntriesRepository
     */
    public function __construct(
        UniformSchedulingOfficesRepository $uniformSchedulingOfficesRepository,
        UniformSchedulingOfficeTimingsRepository $uniformSchedulingOfficeTimingsRepository,
        UniformSchedulingEntriesRepository $uniformSchedulingEntriesRepository,
        HelperService $helperService)
    {
        $this->repository = $uniformSchedulingOfficesRepository;
        $this->uniformSchedulingOfficeTimingsRepository = $uniformSchedulingOfficeTimingsRepository;
        $this->uniformSchedulingEntriesRepository = $uniformSchedulingEntriesRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $days = Days::all()->pluck('name', 'id')->toArray();
        return view('admin::uniform-scheduling.offices',compact('days'));
    }

    public function getAll(){
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }


    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(UniformSchedulingOfficesRequests $request)
    {
        try {
            \DB::beginTransaction();
            $inputs = $request->all();
            $return = [];
            $result = $this->repository->store($inputs);
            if($result){
                $return['success'] = true;
                $return['message'] = 'Office created successfuly.';
            }
            \DB::commit();
            return response()->json($return);
            // return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function getById($id){
       return $this->repository->getById($id);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function storeTimings(UniformSchedulingOfficeTimingsRequest $request)
    {
        try {
            \DB::beginTransaction();
            $result = [];
            $return['message'] = '';
            $inputs = $request->all();
            $inputs['created_by'] = \Auth::id();
            $start_at = \Carbon::parse($request->input('start_time'));
            $end_at   = \Carbon::parse($request->input('end_time'));
            $mins    = $end_at->diffInMinutes($start_at, true);

            if($mins >= $request->input('intervals')){
                    $return['message'] = '';
                    $duplicateTimings = $this->uniformSchedulingOfficeTimingsRepository->getOfficeTimeEntries($inputs);
                    // dd($inputs, $duplicateTimings,sizeof($duplicateTimings));
                    if (sizeof($duplicateTimings)>=1) {
                        $return['success'] = false;
                        $return['result'] = null;
                        $return['message'] = 'Slots timing/date overlapping.';
                    }else{
                        $result = $this->uniformSchedulingOfficeTimingsRepository->store($inputs);
                        $return['success'] = true;
                        $return['result'] = $result;
                        $return['message'] = 'Timing successfully created';
                    }
            }else{
                $return['success'] = false;
                $return['message'] = 'Interval should not be greater than start time & end time duration';
            }

            \DB::commit();
            return response()->json($return);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function removeTimings($id)
    {
        try {
            \DB::beginTransaction();
            $inputs['uniform_scheduling_office_timing_id'] = $id;
            $inputs['booked_date'] = date('Y-m-d');
            $inputs['count'] = true;
            $booking = $this->uniformSchedulingEntriesRepository->getTimingBookedCountOrLast($inputs);
            $booking = 0;
            if ($booking >= 1) {
                $return = array('warning' => true, 'message' => "Office slot booking exists");
                return response()->json($return);
            } else {
                $result =  $this->uniformSchedulingOfficeTimingsRepository->removeTimings($id);
                if ($result) {
                    // TODO:: Destroy all blocked entry.
                    // $slotIds = $this->idsOfficeSlotsRepositories->getAllIdByTimingId($id);
                    // $this->idsOfficeSlotsBlocksRepository->destroyAllOnSlotTimeRemovel($slotIds);
                    // $this->idsOfficeSlotsRepositories->destroyByOfficeTimingId($id);
                }
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e->getMessage()));
        }
    }


    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function updateTimings(Request $request)
    {
        try {
            \DB::beginTransaction();
            $result = [];
            $inputs = $request->all();

            $bookingInputs['uniform_scheduling_office_timing_id'] = $request->input('ids_timing_id');
            $bookingInputs['booked_date'] = $request->input('expiry_date');
            $booking = $this->uniformSchedulingEntriesRepository->getTimingBookedCountOrLast($bookingInputs);
            if (!empty($booking)) {
                $return = array('success' => false, 'message' => "Office slot booking exists upto ".\Carbon::parse($booking->booked_date)->format('M d Y'));
                return response()->json($return);
            }else{
                $result = $this->uniformSchedulingOfficeTimingsRepository->updateEndDate($inputs);
            }

            \DB::commit();
            $return['success'] = true;
            $return['result'] = $result;
            return response()->json($return);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
