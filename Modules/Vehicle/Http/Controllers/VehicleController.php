<?php

namespace Modules\Vehicle\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Vehicle\Repositories\VehicleListRepository;
use Modules\Vehicle\Repositories\VehicleRepository;
use Modules\Vehicle\Repositories\VehicleMaintenanceTypeRepository;
use Modules\Vehicle\Http\Requests\VehicleRequest;

class VehicleController extends Controller
{

    public function __construct(
        VehicleListRepository $vehicleListRepository,
        VehicleMaintenanceTypeRepository $maintenanceTypeRepository,
        VehicleRepository $vehicleRepository,
        HelperService $helperService
        ) {
            $this->vehicleListRepository = $vehicleListRepository;
            $this->maintenanceTypeRepository=$maintenanceTypeRepository;
            $this->repository=$vehicleRepository;
            $this->helperService = $helperService;
        }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
      $vehicleslist= $this->vehicleListRepository->getAll()->where('is_initiated',0)->where('active',1);
      $vehicles =$this->vehicleListRepository->getVehicleListwithNameAndModel($vehicleslist);
       $type= $this->maintenanceTypeRepository->getAll()->pluck('name','id')->toArray();
       if(\Auth::user()->hasPermissionTo('edit_initiated_vehicle'))
       $is_initiated=['Initiate Vehicles','List Initiated Vehicles'];
       else
       $is_initiated=['Initiate Vehicles'];

        return view('vehicle::vehicle.index',compact('vehicles','type','is_initiated'));
    }


    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(VehicleRequest $request)
    {
         try {
            \DB::beginTransaction();
            $data = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            dd($e);
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
        
    }

    /**
     * Return the specified type(km/date) based on type selection.
     * @return Response
     */
    public function getTypeDetails($id)
    {
        return response()->json($this->maintenanceTypeRepository->get($id));
    }
    

    /**
     * Return the Vehicle name based on condition whether they are initiated or not.
     * @return Response
     */
    public function getVehicleName($is_initiated_flag)
    {
     $vehicleslist= $this->vehicleListRepository->getAll()->where('is_initiated',$is_initiated_flag)->where('active',1);
     $vehicles =$this->vehicleListRepository->getVehicleListwithNameAndModel($vehicleslist);
      return response()->json(['vehicles'=> $vehicles]);
    }
    

     /**
     * Edit vehicle initiation.
     * @return Response
     */
    public function editVehicleInitiate($id)
    {
       $vehicles= $this->repository->get($id)->toArray();
       return response()->json(['vehicle'=> $vehicles]);
    }

    /**
     * Edit vehicle initiation.
     * @return Response
     */
    public function getInitiatedServiceType($id)
    {
       $initiated_vehicle_type= $this->repository->getInitiatedType($id);
       return response()->json(['initaited_type'=> $initiated_vehicle_type,'sucess'=>true]);
    }
}
