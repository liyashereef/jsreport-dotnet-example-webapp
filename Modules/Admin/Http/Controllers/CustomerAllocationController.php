<?php

namespace Modules\Admin\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;

class CustomerAllocationController extends Controller
{

    /**
     * The Repository instance.
     * @var \App\Services\HelperService
     * @var \Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
     */
    protected $customerEmployeeAllocationRepository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
     * @param  \App\Services\HelperService $helperService
     * @return void
     */
    public function __construct(CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository, HelperService $helperService)
    {
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return view
     */
    public function index()
    {
        return view('admin::customer.allocation', ['customer_list' => $this->customerEmployeeAllocationRepository->getCustomersList()]);
    }

    /**
     * Get allocated and unallocated List
     * @param  Request $request
     * @return json
     */
    public function getAllocationList($customer_id = null)
    {
        return datatables()->of($this->customerEmployeeAllocationRepository->allocationList($customer_id))->toJson();
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
            $customer_id = $request->get('customer_id');
            $allocation = $this->customerEmployeeAllocationRepository->allocateEmployee($employee_id_list, $customer_id, $request);
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
            $selected_customer_id = (int) $request->get('customer_id');
            $employee_id = $request->get('employee_id');
            $unallocated = $this->customerEmployeeAllocationRepository->unallocateEmployee($selected_customer_id, $employee_id);
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
