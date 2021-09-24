<?php

namespace Modules\Vehicle\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Vehicle\Repositories\VehicleMaintenanceTypeRepository;
use Modules\Vehicle\Repositories\VehicleListRepository;
use Modules\Vehicle\Repositories\VehiclePendingMaintenanceRepository;
use Modules\Vehicle\Repositories\VehicleVendorLookupRepository;
use Modules\Vehicle\Http\Requests\VehiclePendingMaintenanceRequest;

class VehiclePendingMaintenanceController extends Controller
{

    public function __construct(
        VehiclePendingMaintenanceRepository $pendingMaintenanceRepository,
        VehicleMaintenanceTypeRepository $vehicleMaintenanceTypeRepository,
        VehicleListRepository $vehicleListRepository,
        VehicleVendorLookupRepository $vehicleVendorLookupRepository,
        HelperService $helperService
        ) {
            $this->pendingMaintenanceRepository=$pendingMaintenanceRepository;
            $this->helperService = $helperService;
            $this->vehicleMaintenanceTypeRepository = $vehicleMaintenanceTypeRepository;
            $this->vehicleListRepository = $vehicleListRepository;
            $this->vehicleVendorLookupRepository = $vehicleVendorLookupRepository;
        }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
       $vehicle_type = $this->vehicleMaintenanceTypeRepository->getAll()->pluck('name','id')->toArray();
       $vehicleslist= $this->vehicleListRepository->getAll()->where('is_initiated',1)->where('active',1);
       $vehicles =$this->vehicleListRepository->getVehicleListwithNameAndModel($vehicleslist);
       $vendorList = $this->vehicleVendorLookupRepository->getList();
       return view('vehicle::vehicle.vehicle-pending-maintenance',compact('vehicle_type','vehicles','vendorList'));
    }

    /**
     * Get all maintenance list
     * @return Response
     */
    public function getList($all=false)
    {
        $data = $this->pendingMaintenanceRepository->getAll($all);
        $data_arr=$this->pendingMaintenanceRepository->getDataArray($data);
        return datatables()->of($data_arr)->addIndexColumn()->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  VehiclePendingMaintenanceRequest $request
     * @return Response
     */
    public function store(VehiclePendingMaintenanceRequest $request)
    {
        try {
            \DB::beginTransaction();
            $input = $request->all();
            $response = $this->maintenanceRepository->store($input);

            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Get single vehicle pending details
     *
     * @param $id
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->pendingMaintenanceRepository->get($id));
    }

        /**
     * Get single vehicle pending details
     *
     * @param $id
     * @return json
     */
    public function getSubtotal($total,$tax)
    {
        $num = $total * 100;
        $den = $tax + 100;
        $subtotal = $num / $den ;
        $taxamount =  $total - $subtotal;
        return response()->json(['subtotal' => $subtotal,'taxamount' => $taxamount]);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('vehicle::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('vehicle::edit');
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
    public function destroy()
    {
    }


}
