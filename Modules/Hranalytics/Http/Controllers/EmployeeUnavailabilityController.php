<?php

namespace Modules\Hranalytics\Http\Controllers;

use App\Services\HelperService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Hranalytics\Http\Requests\EmployeeUnavailabilityRequest;
use Modules\Hranalytics\Repositories\EmployeeUnavailabilityRepository;
use Modules\Timetracker\Repositories\EmployeeAvailabilityRepository;

class EmployeeUnavailabilityController extends Controller
{
    protected $helperService, $employeeUnavailabilityRepository, $employeeAvailabilityRepository;

    public function __construct(HelperService $helperService, EmployeeAvailabilityRepository $employeeAvailabilityRepository, EmployeeUnavailabilityRepository $employeeUnavailabilityRepository)
    {
        $this->helperService = $helperService;
        $this->repository = $employeeUnavailabilityRepository;
        $this->employeeAvailabilityRepository = $employeeAvailabilityRepository;
    }

    /**
     * Save Unavailability.
     * @return Response
     */
    public function unavailablility(EmployeeUnavailabilityRequest $request)
    {
        try {
            DB::beginTransaction();
            $employee_id = $this->repository->saveUnavailability($request);
            DB::commit();
            $lastUpdatedData = $this->employeeAvailabilityRepository->getLastUpdatedDataByUser($request->employee_id);
            return response()->json(array('success' => true, 'employee_id' => $employee_id, 'last_updated_data' => $lastUpdatedData));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }

    /**
     * List the Unavailability.
     * @return Response
     */
    public function unavailablilityList(Request $request)
    {
        $unavailability_list = $this->repository->listUnavailability($request);
        return datatables()->of($unavailability_list)->addIndexColumn()->toJson();
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Delete Unavailability.
     * @return Response
     */
    public function deleteUnavailability($id)
    {
        try {
            DB::beginTransaction();
            $unavailability_delete = $this->repository->deleteUnavailability($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
