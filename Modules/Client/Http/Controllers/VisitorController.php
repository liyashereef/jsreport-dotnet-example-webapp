<?php

namespace Modules\Client\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Illuminate\Support\Str;
use Modules\Client\Http\Requests\VisitorRequest;

//Model List
use Modules\Admin\Models\VisitorLogTypeLookup;
use Modules\Admin\Models\VisitorStatusLookups;
//Repo List
use Modules\Client\Repositories\VisitorRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\VisitorLog\Events\VisitorNotify;

class VisitorController extends Controller
{

    protected $visitorRepository;

    public function __construct(
        VisitorRepository $visitorRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocation,
        CustomerRepository $customerReporsitory,
        VisitorStatusLookups $visitorStatusLookups,
        HelperService $helperService
        ) {
        $this->repository = $visitorRepository;
        $this->customerEmployeeAllocation = $customerEmployeeAllocation;
        $this->customerReporsitory = $customerReporsitory;
        $this->visitorStatusLookups = $visitorStatusLookups;
        $this->helperService = $helperService;
    }

    public function index(){

         //get allocted customers
         $customer_details_arr = $this->getAllocatedCustomers();
         $project_list = $customer_details_arr->pluck('customer_name_and_number', 'id')->toArray();
         //get visitor Type List
         $visitorTypeList = VisitorLogTypeLookup::select('id','type')->get()->pluck('type', 'id')->toArray();
         //get visitor status List
         $visitorStatusList = $this->visitorStatusLookups->select('id','name')->get()->pluck('name', 'id')->toArray();
         return view('client::visitor-list',compact('project_list','visitorTypeList','visitorStatusList'));
    }

    public function getAllocatedCustomers(){
        $allocated_customers_arr = $this->customerEmployeeAllocation->getAllocatedCustomers(Auth::user());
        $customer_details_arr = $this->customerReporsitory->getCustomers($allocated_customers_arr);
        return $customer_details_arr;

    }

    public function getList(Request $request){
        $client_id = $request->get('client_id');
        $customer_details_arr = $this->getAllocatedCustomers();
        $inputs['customer_id'] = $customer_details_arr->pluck('id');
        return datatables()->of($this->repository->getAllMyVisitors($inputs, $client_id))->addIndexColumn()->toJson();
    }
    public function store(VisitorRequest $request){

        $inputs = $request->all();

        try {
            //On update uid set as empty.
            if(!$request->filled('id')){
                $inputs['uid'] = Str::uuid()->toString();
            }
            \DB::beginTransaction();
             $this->repository->storeFromWeb($inputs);
            \DB::commit();
            VisitorNotify::dispatch($inputs['customerId']);
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function getById($id){
        return $this->repository->getById($id);
    }

    public function destroy($id){
        try {
            \DB::beginTransaction();
             $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

}
