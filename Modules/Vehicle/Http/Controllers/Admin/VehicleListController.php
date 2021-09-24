<?php

namespace Modules\Vehicle\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Vehicle\Repositories\VehicleListRepository;
use Modules\Vehicle\Http\Requests\VehicleListRequest;
use Modules\Admin\Repositories\RegionLookupRepository;
use Modules\Vehicle\Repositories\VehiclePendingMaintenanceRepository;

class VehicleListController extends Controller
{
    public function __construct(
        VehicleListRepository $vehicleListRepository,
        HelperService $helperService,
        RegionLookupRepository $regionLookupRepository,
        VehiclePendingMaintenanceRepository $vehiclePendingMaintenanceRepository
        ) {
            $this->repository = $vehicleListRepository;
            $this->helperService = $helperService;
            $this->regionLookupRepository = $regionLookupRepository;
            $this->vehiclePendingMaintenanceRepository = $vehiclePendingMaintenanceRepository;
        }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $lookups['regionLookup']=$this->regionLookupRepository->getList();
        return view('vehicle::vehicle-list',compact('lookups'));
    }

    public function getList()
    {
        $data = $this->repository->getAll();
        $data_arr = $this->repository->prepareArray($data);
        return datatables()->of($data_arr)->addIndexColumn()->toJson();
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('expense::create');
    }
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }
    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(VehicleListRequest $request)
    {
        try {
            \DB::beginTransaction();
            $vehicleList = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('expense::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('expense::edit');
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
     * @return Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $vehicleListDelete = $this->repository->delete($id);
            $this->vehiclePendingMaintenanceRepository->deleteAllPendingMaintenance($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
