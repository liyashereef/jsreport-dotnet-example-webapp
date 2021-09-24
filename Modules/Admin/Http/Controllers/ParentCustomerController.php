<?php

namespace Modules\Admin\Http\Controllers;

use App\Services\HelperService;
use DB;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\ParentCustomerRequest;
use Modules\Admin\Http\Requests\ImportRequest;
use Modules\Admin\Http\Requests\UploadRequest;
use Modules\Admin\Models\Customer;
use Modules\Admin\Repositories\ParentCustomerRepository;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ParentCustomerController extends Controller
{
    /**
     * The Repository instance.
     *
     * @var \App\Repositories\CustomerRepository
     * @var \App\Services\HelperService
     */
    protected $parentcustomerRepository, $helperService;

    /**
     * Create Repository instance.
     *
     * @param  \App\Repositories\CustomerRepository $customerRepository
     * @param  \App\Services\HelperService $helperService
     * @return void
     */
    public function __construct(ParentCustomerRepository $parentcustomerRepository, HelperService $helperService)
    {
        $this->parentcustomerRepository = $parentcustomerRepository;
        $this->helperService = $helperService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::parentcustomer.parentcustomer', ['lookups' => $this->parentcustomerRepository->getLookups()]);
    }
    /**
     * Get Customers List
     * @return json
     */
    public function getList($customer_type = PERMANENT_CUSTOMER)
    {
        return datatables()->of($this->parentcustomerRepository->getCustomerList($customer_type))->addIndexColumn()->toJson();
    }
    
    /**
     * Get single customer details
     * @param  $id
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->parentcustomerRepository->getSingleCustomer($id));
    }

    /**
     * Function to get customers name and id list based on customer type
     * @return object
     */
    public function getCustomersNameIdList($customer_type)
    {
        return $this->parentcustomerRepository->getCustomersNameList($customer_type);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ParentCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ParentCustomerRequest $request)
    {
        try {
            DB::beginTransaction();
            $customer = $this->parentcustomerRepository->storeCustomer($request->all());
            if ($customer["geo_location_lat"] == null || $customer["geo_location_long"] == null) {
                //return response()->json(['success' => false, "message" => "The given data was invalid.", "errors" => ["postal_code" => ["Given  postal code is not recognized"]]], 422);
            }
            $customer->save();
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Update a resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateLatLong(Request $request)
    {
        try {
            DB::beginTransaction();
            $update_customer = $this->parentcustomerRepository->updateCustomerLatLong($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $experinceLookup
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $customer_delete = $this->parentcustomerRepository->destroyCustomer($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Import data from excel into DB
     * @param  UploadRequest $request
     * @return redirect
     */
    public function customerImport(ImportRequest $request)
    {
        $import = $this->parentcustomerRepository->customerExcelImport($request);
        return redirect(route('customer'))->with('customer-updated', __($import));
    }

    /**
     * Function to get formatted project Details
     * @param  $request
     * @return array
     */
    public function formattedProjectDetails($customer_id)
    {
        $project_details = $this->parentcustomerRepository->getFormattedProjectDetails($customer_id);
        return $project_details;
    }
}
