<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Admin\Models\Customer;

use Modules\Admin\Http\Requests\CustomerTermsAndConditionRequest;
use Modules\Admin\Repositories\CustomerTermsAndConditionRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;

class CustomerTermsAndConditionController extends Controller
{
    protected $termsAndConditionRepository, $helperService, $customerEmployeeAllocationRepository;

    public function __construct(CustomerTermsAndConditionRepository $termsAndConditionRepository, 
    HelperService $helperService,
    CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    ){
        $this->termsAndConditionRepository = $termsAndConditionRepository;
        $this->helperService = $helperService;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::client.terms-and-condition.index');
    }
    
    public function getList(){
        return datatables()->of($this->termsAndConditionRepository->getAllCustomerTermsAndConditions())->addIndexColumn()->toJson();
    }

    public function single($id){
        return $this->termsAndConditionRepository->getById($id);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $customers = [];
        $termsAndCondition = [];
        $id = null;

        $existsCustomerIds = data_get($this->termsAndConditionRepository->getAllByType(VISITOR_LOG_TYPE)->toArray(),'*.customer_id');
        //Fetching allocated customerIds
        $customerIds = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
        
        $customers = array_diff($customerIds,$existsCustomerIds);

        //Fetching Customers
        $customers = Customer::whereIn('id',$customers)
        ->whereNull('deleted_at')
        ->orderBy('client_name')
        ->select('id','project_number as projectNumber','client_name as name')
        ->get();
      
        return view('admin::client.terms-and-condition.form',compact('customers','termsAndCondition','id'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(CustomerTermsAndConditionRequest $request)
    {
        $inputs = $request->all();
        if($request->filled('_token')){
            unset($inputs['_token']);
        }
        if($request->filled('id')){ 
            
            if($request->filled('editors')){
                unset($inputs['editors']);
            }
            unset($inputs['id']);

            $result = $this->termsAndConditionRepository->update($request->input('id'),$inputs);
            $msg = 'updated';
        }else{
            $result = $this->termsAndConditionRepository->store($inputs);
            $msg = 'created';
        }
        
        if($result){
            $message='Terms and condition has been successfully '.$msg;
          }else{
            $message= 'Try again';
            $msg = 'error';
          }
          return response()->json(array('success' => true, 'message' => $message,'id'=>'','modalTitle'=>ucfirst($msg)));
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $termsAndCondition = $this->termsAndConditionRepository->getById($id);
              
        //Fetching Customers
        $customers = Customer::where('id',$termsAndCondition->customer_id)
        ->whereNull('deleted_at')
        ->orderBy('client_name')
        ->select('id','project_number as projectNumber','client_name as name')
        ->get();
      
        return view('admin::client.terms-and-condition.form',compact('customers','termsAndCondition','id'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        $result = $this->termsAndConditionRepository->delete($id);
        if($result){
            $message='Terms and condition has been successfully removed';
          }else{
            $message= 'Error. Try again';
          }
          return response()->json(array('success' => true, 'message' => $message,'id'=>''));   
    }
}
