<?php

namespace Modules\Timetracker\Http\Controllers\API\v1;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Vehicle\Repositories\VehicleListRepository;
use Modules\Vehicle\Repositories\VehicleTripRepository;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public $successStatus = 200;
    public function __construct( HelperService $helper_service,
     VehicleListRepository $vehicleListRepository,
     VehicleTripRepository $vehicleTripRepository)
    {
        $this->repository = $vehicleListRepository;
        $this->vehicleTripRepository = $vehicleTripRepository;
        $this->helper_service = $helper_service;
    }

    public function index()
    {

    }

    /**
     * get all vehicle details.
     * @return Response
     */

    public function getAllVehicles()
    {
        try {
            DB::beginTransaction();
            $vehicles =$this->repository->getVehicles();
            if(!empty($vehicles)){
                $content['vehicles'] = $vehicles;
                $content['message'] = 'ok';
            }else{
                $content['vehicles'] = null;
                $content['message'] = 'No vehicles available';
            }
            $content['success'] = true;
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

        //To submit vehicle trips 
        public function submitVehicleTrips(Request $request) {
            try {
                DB::beginTransaction();
                $submitExpense = $this->vehicleTripRepository->saveVehicleTrips($request);
                $content['success'] = true;
                $content['message'] = 'ok';
                $content['code'] = $this->successStatus;
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $content['success'] = false;
                    $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
                $content['code'] = 406;
            }
            return response()->json(['content' => $content], $content['code']);
        }

}
