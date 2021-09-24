<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\HolidayPaymentRequest;
use Modules\Admin\Repositories\HolidayPaymentRepository;
use Modules\Admin\Repositories\HolidayRepository;


//use App\;

class ContractsHolidayPaymentController extends Controller
{
   
    public function __construct(HolidayPaymentRepository $holidayPaymentRepository, HolidayRepository $holidayRepository,HelperService $helperService)
    {
        $this->repository = $holidayPaymentRepository;
        $this->holidayRepository = $holidayRepository;
        $this->helperService = $helperService;
    }



    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $holidayList = $this->holidayRepository->getList();
;
        return view("admin::contracts.holidayPayment", compact('holidayList'));
    }

    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('admin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(HolidayPaymentRequest $request)
    {
        try {
            \DB::beginTransaction();
            $category = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('admin::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $category_delete = $this->repository->delete($id);
            \DB::commit();
            if ($category_delete == false) {
                return response()->json($this->helperService->returnFalseResponse());
            } else {
                return response()->json($this->helperService->returnTrueResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function getBusinessSegmentList(Request $request){
        return view("admin::contracts.businessSegment");
    }
}
