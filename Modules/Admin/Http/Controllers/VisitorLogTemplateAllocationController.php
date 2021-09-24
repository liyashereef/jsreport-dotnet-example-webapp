<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Repositories\CandidateExperienceLookupRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerTemplateAllocationRepository;

class VisitorLogTemplateAllocationController extends Controller
{
    protected $repository, $helperService, $customerEmployeeAllocationRepository, $customerTemplateAllocationRepository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CandidateExperienceLookupRepository $candidateExperienceLookupRepository
     * @return void
     */
    public function __construct(CandidateExperienceLookupRepository $candidateExperienceLookupRepository, CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository, HelperService $helperService, CustomerTemplateAllocationRepository $customerTemplateAllocationRepository)
    {
        $this->repository = $candidateExperienceLookupRepository;
        $this->helperService = $helperService;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->customerTemplateAllocationRepository = $customerTemplateAllocationRepository;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::client.template-allocation', ['customer_list' => $this->customerEmployeeAllocationRepository->getCustomersList()]);

    }

    /**
     * Get allocated and unallocated List
     * @param  Request $request
     * @return json
     */
    public function getAllocationList($customer_id = null)
    {
        return datatables()->of($this->customerTemplateAllocationRepository->allocationList($customer_id))->toJson();
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
            $template_id_list = json_decode($request->get('template_ids'));
            $customer_id = $request->get('customer_id');
            $allocation = $this->customerTemplateAllocationRepository->allocateEmployee($template_id_list, $customer_id, $request);
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
            $template_id = $request->get('template_id');
            $unallocated = $this->customerTemplateAllocationRepository->unallocateTemplate($selected_customer_id, $template_id);
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
