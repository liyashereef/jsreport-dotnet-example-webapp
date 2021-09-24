<?php

namespace Modules\Vehicle\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Vehicle\Repositories\VehicleMaintenanceTypeRepository;
use Modules\Vehicle\Repositories\VehicleListRepository;
use Modules\Vehicle\Repositories\VehicleMaintenanceRepository;
use Modules\Vehicle\Http\Requests\VehicleMaintenanceRequest;

class VehicleMaintenanceController extends Controller
{

    public function __construct(
        VehicleMaintenanceRepository $maintenanceRepository,
        VehicleMaintenanceTypeRepository $vehicleMaintenanceTypeRepository,
        VehicleListRepository $vehicleListRepository,
        HelperService $helperService
        ) {
            $this->maintenanceRepository=$maintenanceRepository;
            $this->helperService = $helperService;
            $this->vehicleMaintenanceTypeRepository = $vehicleMaintenanceTypeRepository;
            $this->vehicleListRepository = $vehicleListRepository;
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
       return view('vehicle::vehicle.vehicle-maintenance',compact('vehicle_type','vehicles'));
    }

    /**
     * Get all maintenance list
     * @return Response
     */
    public function getList()
    {
        $data = $this->maintenanceRepository->getAll();
        return datatables()
        ->of($data)
        ->setTransformer(function ($item) {
            return [
                'id' =>  $item->id,
                'vehicle_number' => $item->vehicle->number,
                'vehicle_model' => $item->vehicle->model,
                'vehicle_make' => $item->vehicle->make,
                'region_name' => $item->vehicle->regionDetails->region_name,
                'odometer_reading' => $item->vehicle->odometer_reading,
                'maintenance_type_name' => $item->maintenanceType->name,
                'service_date' => $item->service_date,
                'service_kilometre' => $item->service_kilometre,
                'total_charges' => $item->total_charges,
                'subtotal' => $item->subtotal,
                'tax_amount' => $item->tax_amount,
                'vehicle_vendor' => isset($item->vendor->vehicle_vendor) ? $item->vendor->vehicle_vendor : '--',
                'attachments' => $item->attachments,
                'notes' => $item->notes,
            ];
        })
        ->addIndexColumn()
        ->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  VehicleMaintenanceRequest $request
     * @return Response
     */
    public function store(VehicleMaintenanceRequest $request)
    {
        try {
            \DB::beginTransaction();
           // $input = $request->all();
            $employeeTimeOffLog = $this->maintenanceRepository->store($request);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
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
