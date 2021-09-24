<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\UniformSchedulingOfficeSlotBlocksRequest;
use Modules\Admin\Repositories\UniformSchedulingOfficesBlockRepository;
use Modules\UniformScheduling\Repositories\UniformSchedulingEntriesRepository;
use Modules\Admin\Models\Days;

class UniformSchedulingOfficesBlockController extends Controller
{
     /**
     * Create a new Model instance.
     *
     * @param  Modules\UniformScheduling\Repositories\UniformSchedulingEntriesRepository $uniformSchedulingEntriesRepository
     * @param  Modules\Admin\Repositories\UniformSchedulingOfficesBlockRepository $uniformSchedulingOfficesBlockRepository
     */
    public function __construct(
        UniformSchedulingEntriesRepository $uniformSchedulingEntriesRepository,
        UniformSchedulingOfficesBlockRepository $uniformSchedulingOfficesBlockRepository,
        HelperService $helperService
        )
    {
        $this->uniformSchedulingEntriesRepository = $uniformSchedulingEntriesRepository;
        $this->uniformSchedulingOfficesBlockRepository = $uniformSchedulingOfficesBlockRepository;
        $this->helperService = $helperService;
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(UniformSchedulingOfficeSlotBlocksRequest $request)
    {

        try {
            \DB::beginTransaction();
            $result = [];
            $return['message'] = '';
            $return['dayName'] = '--';
            // $inputs['start_date'] = $request->input('start_date');
            // $inputs['end_date'] = $request->input('end_date');
            // $inputs['day_id'] = $request->input('day_id');
            // $inputs = $request->all();
            // $inputs['count'] = true;
            // $bookingExists = $this->uniformSchedulingEntriesRepository->getTimingBookedCountOrLast($inputs);
            // dd($inputs, $bookingExists);
            // if ($bookingExists>=1) {
            //     $return['success'] = false;
            //     $return['result'] = null;
            //     $return['message'] = 'Booking already exists.';
            // }else{

                $inputs = $request->all();
                $inputs['created_by'] = \Auth::id();
                $inputs['day_id'] = null;
                if($request->input('day_id') != 0){
                    $inputs['day_id'] = (int)$request->input('day_id');
                    $day = Days::find($inputs['day_id']);
                    $return['dayName'] = $day->name;
                }
                $result = $this->uniformSchedulingOfficesBlockRepository->store($inputs);
                $return['success'] = true;
                $return['result'] = $result;
                $return['message'] = 'Office block created successfully.
                Please reschedule blocked slots bookings';
            // }


            \DB::commit();
            return response()->json($return);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

     /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function updateBlock(Request $request)
    {
        try {
            \DB::beginTransaction();
            $result = [];
            // $inputs = $request->all();
            $bookingInputs['id'] = $request->input('block_id');
            $bookingInputs['end_date'] = $request->input('end_date');
            $result = $this->uniformSchedulingOfficesBlockRepository->updateEndDate($bookingInputs);
            \DB::commit();
            $return['success'] = true;
            $return['result'] = $result;
            return response()->json($return);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id){
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->uniformSchedulingOfficesBlockRepository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e->getMessage()));
        }
    }
}
