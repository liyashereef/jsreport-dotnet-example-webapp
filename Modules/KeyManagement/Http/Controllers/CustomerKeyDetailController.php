<?php

namespace Modules\KeyManagement\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\KeyManagement\Repositories\CustomerKeyDetailRepository;
use Modules\KeyManagement\Models\CustomerKeyDetail;
use Modules\KeyManagement\Http\Requests\CustomerKeyRequest;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\KeyManagement\Repositories\KeyLogRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;

class CustomerKeyDetailController extends Controller
{
    protected $helperService, $customerKeyDetailRepository,$customerRepository,$customerKeyDetailModel;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\KeyManagement\Repositories\CustomerKeyDetailRepository $customerKeyDetailRepository;
     * @var \Modules\KeyManagement\Repositories\KeyLogRepository $keyLogRepository;
     * @return void
     */
    public function __construct(
        HelperService $helperService,
        CustomerKeyDetailRepository $customerKeyDetailRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        KeyLogRepository $keyLogRepository,
        CustomerRepository $customerRepository,
        CustomerKeyDetail $customerKeyDetailModel
        )
    {
        $this->helperService = $helperService;
        $this->customerKeyDetailRepository = $customerKeyDetailRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->customerRepository=$customerRepository;
        $this->customerkeydetailmodel = $customerKeyDetailModel;
        $this->keyLogRepository = $keyLogRepository;


    }

    /**
     * Get Customer Listing page.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $customer_id = request()->customer_id;
        $customer_arr = $this->customerRepository->getCustomerList();
        $customerlist = array();
        foreach ($customer_arr as $key => $customer) {
            $id=$customer['id'];
            $customerlist[$id]=$customer['project_number']. ' - ' .$customer['client_name'];
        }
        if (\Auth::user()->hasAnyPermission(['view_all_customers_keys','admin', 'super_admin'])) {
            $project_list = $this->customerRepository->getProjectsDropdownList('all');
        } else if (\Auth::user()->can('view_allocated_customers_keys')) {
            $project_list = $this->customerRepository->getProjectsDropdownList('allocated');
        } else {
            $project_list = [];
        }
        return view('keymanagement::key-setting.add-key-setting', compact('customerlist','customer_id','project_list'));

    }

    /**
     * List all Customer details in datatable.
     *
     *
     * @return Json
     */

    public function getList($id)
    {

        $details = $this->customerKeyDetailRepository->getAll($id);

        return datatables()->of($details)->addIndexColumn()->toJson();

    }

    /**
     * Store  newly created  Key Detail in storage.
     *
     * @param  Modules\KeyManagement\Http\Requests\CustomerKeyRequest $request
     * @return Json
     */

    public function store(CustomerKeyRequest $request)
    {
        try {
            DB::beginTransaction();
            $result = $this->customerKeyDetailRepository->save($request);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse($request->id));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Show the specfied key detail.
     *
     * @param  $id
     * @return Json
     */

    public function getSingle($id)
    {
        return response()->json($this->customerKeyDetailRepository->get($id));
    }

    /**
     *  List all Customer details in datatable..
     *
     * @param  $id
     * @return Json
     */
    public function getCustomerList(Request $request)
    {
        $client_id = $request->get('client_id');
        return datatables()->of($this->customerKeyDetailRepository->getCustomerAll($client_id))->addIndexColumn()->toJson();
    }

    /**
     * Show the form for creating the customer key details.
     *
     */

    public function createKeyDetails($id)
    {
        return view('keymanagement::key-setting.key-details-summary', compact('id'));
    }

    /**
     * List all the key logs.
     *
     * @return Json
     */

    public function getKeyLogList(){

        $key_id = request('keyid');
        $from_date = request('frdate');
        $to_date = request('tdate');
        $client_id = request('client_id');
        return datatables()->of($this->keyLogRepository->getKeyLogList($key_id,$from_date,$to_date,$client_id))->addIndexColumn()->toJson();
    }

    /**
     * Show the form for filter Key log summary.
     *
     * @return Json
     */


    public function createKeyLog(){
        $result = $this->customerkeydetailmodel->orderBy('room_name', 'asc')->get();
        $customer_list = $this->customerKeyDetailRepository->clienLookUps();
        $each_row = [];
        foreach ($result as $key => $each_list) {
            $each_row[$each_list->id] = $each_list->room_name.' ('.$each_list->key_id.')';
        }
        return view('keymanagement::key-setting.key-log-summary', compact('each_row','customer_list'));
    }

    /**
     * Get single log details
     *
     * @param $id
     * @return json
     */

    public function getKeyLogSingle($id)
    {
        return response()->json($this->keyLogRepository->getKeyLogSingle($id));
    }

    /**
     * Remove the specified key from storage.
     * @return Response
     */

    public function destroy($id){
        try {
            \DB::beginTransaction();
             $this->customerKeyDetailRepository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

}
