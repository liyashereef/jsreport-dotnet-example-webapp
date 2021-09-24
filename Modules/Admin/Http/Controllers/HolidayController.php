<?php
namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use DB;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\HolidayRequest;
use Modules\Admin\Http\Requests\StatHolidayRequest;
use Modules\Admin\Models\Holiday;
use Modules\Admin\Models\StatHolidays;
use Modules\Admin\Repositories\HolidayRepository;

class HolidayController extends Controller
{

    /**
     * The Repository instance.
     *
     * @var \App\Services\HelperService
     * @var \Modules\Admin\Repositories\HolidayRepository;
     */
    protected $helperService, $holidayRepository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\HolidayRepository $holidayRepository;
     * @return void
     */
    public function __construct(HelperService $helperService, HolidayRepository $holidayRepository)
    {
        $this->helperService = $helperService;
        $this->holidayRepository = $holidayRepository;
    }

    /**
     * Display a listing of the Holidays.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statholidays = StatHolidays::all();
        return view('admin::masters.holiday',compact('statholidays'));
    }

    /**
     * List Holidays in datatable.
     *
     * @return Json
     */
    public function getList()
    {
        return datatables()->of($this->holidayRepository->getAll())->addIndexColumn()->toJson();

    }

    /**
     * List Stat Holidays in datatable.
     *
     * @return Json
     */
    public function getStatList()
    {
        return datatables()->of($this->holidayRepository->getAllStatHolidays())->addIndexColumn()->toJson();

    }

    /**
     * Store a newly created holiday in storage.
     *
     * @param  Modules\Admin\Http\Requests\HolidayRequest $request
     * @return Json
     */
    public function store(HolidayRequest $request)
    {
        try {
            DB::beginTransaction();
            $role = $this->holidayRepository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

    /**
     * Store a newly created holiday in storage.
     *
     * @param  Modules\Admin\Http\Requests\HolidayRequest $request
     * @return Json
     */
    public function statstore(StatHolidayRequest $request)
    {
        try {
            DB::beginTransaction();
            $role = $this->holidayRepository->statsave($request->all());
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
        return response()->json($this->holidayRepository->get($id));
    }

    public function getStatSingle($id)
    {
        return response()->json(StatHolidays::find($id));
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
            $holiday_delete = $this->holidayRepository->deleteHoliday($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }

    /**
     * Remove the specified holiday from storage.
     *
     * @param  $id
     * @return Json
     */
    public function statdestroy($id)
    {

        try {
            DB::beginTransaction();
            $holiday_delete = $this->holidayRepository->deleteStatHoliday($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }

}
