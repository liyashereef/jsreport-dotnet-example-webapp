<?php

namespace Modules\Vehicle\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Vehicle\Repositories\VehicleTripRepository;

class VehicleTripController extends Controller
{

    public function __construct(
        VehicleTripRepository $vehicleTripRepository,
        HelperService $helperService
        ) {
            $this->vehicleTripRepository=$vehicleTripRepository;
            $this->helperService = $helperService;
        }

    /**
     * Get cumilative kilometre driven
     * @return Response
     */
    public function index()
    {
        return view('vehicle::vehicle.vehicle-cumilative-km');
    }

     /**
     * Get all cumilative km list
     * @return Response
     */
    public function getCumilativeKilometreList()
    {
        $data = $this->vehicleTripRepository->getAll();
        $data_arr=$this->vehicleTripRepository->getDataArray($data);
        return datatables()->of($data_arr)->addIndexColumn()->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();
            $input = $request->all();
            $employeeTimeOffLog = $this->maintenanceRepository->store($input);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Get single trip details
     *
     * @param $id
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->vehicleTripRepository->getSingle($id));
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
    public function updateOdometer($shift_id)
    {
        $this->vehicleTripRepository->updateOdometerAndTrip($shift_id);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {

    }


    
}
