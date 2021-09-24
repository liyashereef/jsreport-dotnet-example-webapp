<?php

namespace Modules\Vehicle\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Vehicle\Repositories\VehicleAnalysisRepository;


class VehicleAnalysisController extends Controller
{
    public function __construct(
        VehicleAnalysisRepository $vehicleAnalysisRepository,
        HelperService $helperService
        ) {
            $this->vehicleAnalysisRepository = $vehicleAnalysisRepository;
            $this->helperService = $helperService;
        }

        /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $vendorsLookups =  $this->vehicleAnalysisRepository->getVendorLookups();
        return view('vehicle::vehicle.vehicle-analysis', compact('vendorsLookups'));
    }

    /**
     * Get all maintenance list
     * @return Response
     */
    public function getList()
    {
        $vendorId = request()->data['vendorId'];
        $fromDate =  request()->data['frdate'];
        $toDate =  request()->data['tdate'];
        $TotalRowDetails =  $this->vehicleAnalysisRepository->prepareDataForTotalRow($vendorId, $fromDate, $toDate);
        $vendors =  $this->vehicleAnalysisRepository->getAll($vendorId, $fromDate, $toDate);
        $vendorsDetails = array_merge(array($TotalRowDetails), $vendors);
        $regions = $this->vehicleAnalysisRepository->getRegionList();
        $addFirstCell = array_merge(array(["regions" => ""]), $regions);
        $regionDetails = array_merge($addFirstCell, array( ["regions" => "Total"]));
        $data = ['regionDetails'=>  $regionDetails, 'vendors'=> $vendorsDetails];
        return response()->json($data);
    }


}
