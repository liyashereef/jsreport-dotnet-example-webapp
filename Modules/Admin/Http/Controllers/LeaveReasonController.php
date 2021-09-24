<?php
namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use DB;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\LeaveReasonRequest;
use Modules\Admin\Repositories\LeaveReasonRepository;

class LeaveReasonController extends Controller
{

    /**
     * The Repository instance.
     *
     * @var \App\Services\HelperService
     * @var \Modules\Admin\Repositories\HolidayRepository;
     */
    protected $helperService, $leaveReasonRepository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\HolidayRepository $holidayRepository;
     * @return void
     */
    public function __construct(HelperService $helperService, LeaveReasonRepository $leaveReasonRepository)
    {
        $this->helperService = $helperService;
        $this->leaveReasonRepository = $leaveReasonRepository;
    }

    /**
     * Display a listing of the Holidays.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::masters.leave-reason');
    }

    /**
     * List Holidays in datatable.
     *
     * @return Json
     */
    public function getList()
    {
        return datatables()->of($this->leaveReasonRepository->getAll())->addIndexColumn()->toJson();

    }

    /**
     * Store a newly created holiday in storage.
     *
     * @param  Modules\Admin\Http\Requests\HolidayRequest $request
     * @return Json
     */
    public function store(LeaveReasonRequest $request)
    {
        try {
            DB::beginTransaction();
            $reason = $this->leaveReasonRepository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

    /**
     * Show the form for editing the specified holiday.
     *
     * @param  $id
     * @return Json
     */
    public function getSingle($id)
    {
        return response()->json($this->leaveReasonRepository->get($id));
    }

    /**
     * Remove the specified holiday from storage.
     *
     * @param  $id
     * @return Json
     */
    public function destroy($id)
    {

        try {
            DB::beginTransaction();
            $leave_reason_delete = $this->leaveReasonRepository->deleteLeaveReason($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }

}
