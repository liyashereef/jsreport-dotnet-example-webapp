<?php

namespace Modules\Vehicle\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Vehicle\Repositories\VehicleMaintenanceTypeRepository;
use Modules\Vehicle\Http\Requests\VehicleMaintenanceTypeRequest;
use Modules\Vehicle\Repositories\VehicleMaintenanceCategoryRepository;
use Modules\Vehicle\Repositories\VehicleMaintenanceRepository;
use Modules\Vehicle\Repositories\VehiclePendingMaintenanceRepository;

class VehicleMaintenanceTypeController extends Controller
{
    public function __construct(
        VehicleMaintenanceTypeRepository $vehicleMaintenanceTypeRepository,
        HelperService $helperService,
        VehicleMaintenanceCategoryRepository $vehicleMaintenanceCategoryRepository,
        VehicleMaintenanceRepository $vehicleMaintenanceRepository,
        VehiclePendingMaintenanceRepository $vehiclePendingMaintenanceRepository
        ) {
            $this->repository = $vehicleMaintenanceTypeRepository;
            $this->helperService = $helperService;
            $this->vehicleMaintenanceCategoryRepository = $vehicleMaintenanceCategoryRepository;
            $this->vehicleMaintenanceRepository=$vehicleMaintenanceRepository;
            $this->vehiclePendingMaintenanceRepository=$vehiclePendingMaintenanceRepository;
        }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $category_list = $this->vehicleMaintenanceCategoryRepository->getAll()->pluck('category_name', 'id')->toArray();
        $datavalues=$this->repository->getAllMaintenanceDatatypes()->pluck('name', 'id')->toArray();
        return view('vehicle::maintenance-type',compact('category_list','datavalues'));
    }

    public function getList()
    {
        $data = $this->repository->getAll();
        return datatables()->of($data)->addIndexColumn()->toJson();
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
    public function store(VehicleMaintenanceTypeRequest $request)
    {
        try {
            \DB::beginTransaction();
            $vehicleList = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            dd($e);
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
            $vehicleTypeDelete = $this->repository->delete($id);
            $initiateTypeDelete=$this->vehicleMaintenanceRepository->maintenaceIntervalDelete($id);
            $initiateRecordTypeDelete=$this->vehicleMaintenanceRepository->maintenaceRecordDelete($id);
            $pendingTypeDelete=$this->vehiclePendingMaintenanceRepository->deletePendingMaintenanceOfDeletedType($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
