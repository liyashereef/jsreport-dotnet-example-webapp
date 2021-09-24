<?php

namespace Modules\Hranalytics\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\CustomerRequest;
use Modules\Admin\Models\Customer;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\IndustrySectorLookupRepository;
use Modules\Admin\Repositories\RegionLookupRepository;
use Modules\Admin\Repositories\SecurityClearanceLookupRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Hranalytics\Repositories\CustomerStcDetailsRepository;

class ShortTermContractsController extends Controller
{

    /**
     * The Repository instance.
     *
     * @var \App\Repositories\RegionLookupRepository
     * @var \App\Repositories\IndustrySectorLookupRepository
     * @var \App\Repositories\SecurityClearanceLookupRepository
     * @var \App\Repositories\CustomerRepository
     */
    protected $regionLookupRepository, $industrySectorLookupRepository, $securityClearanceLookupRepository, $customerRepository, $stcDetailsRepository, $userRepository;

    /**
     * Create new Repository instance.
     *
     * @param  \App\Repositories\RegionLookupRepository $regionLookupRepository
     * @param  \App\Repositories\IndustrySectorLookupRepository $industrySectorLookupRepository
     * @param  \App\Repositories\SecurityClearanceLookupRepository $securityClearanceLookupRepository
     *@param  \App\Repositories\CustomerRepository $customerRepository
     * @return void
     */
    public function __construct(CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository, RegionLookupRepository $regionLookupRepository, IndustrySectorLookupRepository $industrySectorLookupRepository, SecurityClearanceLookupRepository $securityClearanceLookupRepository, CustomerRepository $customerRepository, CustomerStcDetailsRepository $stcDetailsRepository, HelperService $helperService, UserRepository $userRepository)
    {
        $this->regionLookupRepository = $regionLookupRepository;
        $this->industrySectorLookupRepository = $industrySectorLookupRepository;
        $this->securityClearanceLookupRepository = $securityClearanceLookupRepository;
        $this->customerRepository = $customerRepository;
        $this->stcDetailsRepository = $stcDetailsRepository;
        $this->helperService = $helperService;
        $this->userRepository = $userRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }

    /**
     * Display a listing of the short term contracts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_details_arr = $this->customerRepository->getCustomerStc();
        return view('hranalytics::short-term-contracts.index',compact('customer_details_arr'));
    }

    /**
     * Show the form for creating a new short term contract.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lookups = $this->lookups();
        return view('hranalytics::short-term-contracts.create', compact('lookups'));
    }

    /**
     *get short term contracts in storage.
     *
     * @param  null
     * @return \Illuminate\Http\Response
     */
    public function getList(Request $request)
    {
        $client_id = $request->get('client_id')?:null;
        return datatables()->of($this->customerRepository->getCustomerStc($client_id))->addIndexColumn()->toJson();

    }

    /**
     * Store a newly created short term contract in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        try {
            \DB::beginTransaction();
            $stc_data = $this->customerRepository->storeCustomerStc($request);
            $last_inserted_id = $stc_data->id;
            $customer_stc_details = $this->stcDetailsRepository->storeStcDetails($request, $last_inserted_id);
            \DB::commit();
            $result = ($stc_data->wasRecentlyCreated) ? 'STC client has been created successfully' : 'STC client details has been updated successfully';
            return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Show the form for editing the specified short term contract.
     *
     * @param  Request $request
     * @param  int  $id
     * @return view
     */
    public function edit($id)
    {
        $customer = $this->customerRepository->getSingleCustomer($id);
        $customer_stc_details = $this->stcDetailsRepository->getSingleCustomerStcDetails($id);
        $lookups = $this->lookups();
        if (!is_numeric($customer->requester_name) && ($customer->requester_position != null) && ($customer->requester_empno != null)) {
            foreach ($lookups['requesterLookup'] as $key => $requester) {
                if (strpos($requester, $customer->requester_empno) !== false) {
                    $requester_name = $key;
                }
            }

        } else {
            $requester_name = $customer->requester_name;
        }

        return view('hranalytics::short-term-contracts.edit', compact('lookups', 'customer', 'customer_stc_details', 'requester_name'));
    }

    /**
     * Remove the specified short term contract from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            \DB::beginTransaction();
            $customer_id = $request->get('id');
            $stc_customer_delete = $this->customerRepository->destroyCustomer($customer_id);
            $customer_stc_details_delete = $this->stcDetailsRepository->destroyCustomerStcDetails($customer_id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Prepare array lookups.
     *
     * @param  null
     * @return array
     */
    public function lookups()
    {
        $lookups['industrySectorLookup'] = $this->industrySectorLookupRepository->getList();
        $lookups['regionLookup'] = $this->regionLookupRepository->getList();
        $lookups['securityClearanceLookup'] = $this->securityClearanceLookupRepository->getList();
        $lookups['requesterLookup'] = $this->userRepository->getUserLookup(null, ['super_admin', 'admin']);
        return $lookups;
    }
}
