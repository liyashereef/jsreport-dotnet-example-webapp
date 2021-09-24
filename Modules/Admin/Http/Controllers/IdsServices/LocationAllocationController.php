<?php

namespace Modules\Admin\Http\Controllers\IdsServices;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\IdsOfficeRepository;
use Modules\Admin\Repositories\IdsLocationAllocationRepository;

class LocationAllocationController extends Controller
{

    /**
     * The Repository instance.
     * @var \App\Services\HelperService
     * @var \Modules\Admin\Repositories\IdsOfficeRepository;
     */
    protected $idsOfficeRepository, $helperService, $idsLocationAllocationRepository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\IdsOfficeRepository $idsOfficeRepository
     * @param  \App\Services\HelperService $helperService
     * @return void
     */
    public function __construct(
        IdsOfficeRepository $idsOfficeRepository,
        IdsLocationAllocationRepository $idsLocationAllocationRepository,
        HelperService $helperService
    )
    {
        $this->idsOfficeRepository = $idsOfficeRepository;
        $this->idsLocationAllocationRepository = $idsLocationAllocationRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return view
     */
    public function index()
    {
        return view('admin::ids-scheduling.location-allocation', 
        ['location_list' => $this->idsOfficeRepository->getNameAndId()]);
    }

      /**
     * Get allocated and unallocated List
     * @param  Request $request
     * @return json
     */
    public function getAllocationList($ids_location_id = null)
    {
        return datatables()->of($this->idsLocationAllocationRepository->allocationList($ids_location_id))->toJson();
    }

    /**
     * Store the allocated resource.
     * @param  Request $request
     * @return json
     */
    public function allocate(Request $request)
    {
        try {
            \DB::beginTransaction();
            $employee_id_list = json_decode($request->get('employee_ids'));
            $ids_office_id = $request->get('ids_office_id');
            $allocation = $this->idsLocationAllocationRepository->allocateEmployee($employee_id_list, $ids_office_id, $request);
            \DB::commit();
            if ($allocation) {
                return response()->json($this->helperService->returnTrueResponse());
            } else {
                return response()->json($this->helperService->returnFalseResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

    /**
     * Remove the allocated resource
     * @param  Request $request
     * @return json
     */
    public function unallocate(Request $request)
    {
        try {
            \DB::beginTransaction();
            $ids_office_id = (int) $request->get('ids_office_id');
            $employee_id = $request->get('employee_id');
            $unallocated = $this->idsLocationAllocationRepository->unallocateEmployee($ids_office_id, $employee_id);
            \DB::commit();
            if ($unallocated) {
                return response()->json($this->helperService->returnTrueResponse());
            } else {
                return response()->json($this->helperService->returnFalseResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

}
