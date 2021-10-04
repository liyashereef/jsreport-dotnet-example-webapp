<?php

namespace Modules\Documents\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\ReasonForSubmissionRepository;
use Modules\Client\Repositories\ClientRepository;
use Modules\Hranalytics\Repositories\EmployeeWhistleblowerRepository;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\Employee;
use Modules\Documents\Http\Requests\DocumentRequest;
use Modules\Documents\Repositories\DocumentsRepository;
use Modules\Admin\Models\OtherCategoryLookup;

class DocumentsController extends Controller
{
    /**
     * Repository instance.
     * @var \Modules\Documents\Repositories\DocumentsRepository
     * @var \App\Services\HelperService
     */

    protected $documentRepository, $helperService,$otherCategoryname;

    /**
     * Create Repository instance.
     * @param  \Modules\Admin\Repositories\CustomerRepository $customerRepository
     * @param  \Modules\Client\Repositories\ClientRepository $clientRepository
     * @param  \Modules\Admin\Repositories\Employee $timeOffLogRepository
     * @param  \App\Services\HelperService $helperService $employee
     * @return void
     */

    public function __construct(DocumentsRepository $documentRepository,CustomerRepository $customerRepository,ClientRepository $clientRepository,Customer $customer,Employee $employee,EmployeeWhistleblowerRepository $employeeWhistleblowerRepository,HelperService $helperService,OtherCategoryLookup $OtherCategorylookup)
    {
        $this->customerRepository = $customerRepository;
        $this->documentRepository = $documentRepository;
        $this->employeeWhistleblowerRepository = $employeeWhistleblowerRepository;
        $this->employee = $employee;
        $this->customer = $customer;
        $this->clientRepository = $clientRepository;
        $this->helperService = $helperService;
        $this->OtherCategorylookup = $OtherCategorylookup;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */

    public function index(){

        $user_list = $this->documentRepository->employeeLookUps();
        return view('documents::add-employee-document',compact('user_list'));
    }

    /**
     * Get list of users
     *
     * @return datatable object
     */

    public function getUserSummaryList($statusId){
            $employeename = request('employeename');
            $employeeno = request('employeeno');
            return datatables()->of($this->documentRepository->getUserList($employeeno,$employeename,$statusId))->addIndexColumn()->toJson();
    }

    /**
     * Load the client resource listing Page
     *
     * @return \Illuminate\Http\Response
     */

    public function clientDocument(){

          $customer_list = $this->documentRepository->clienLookUps();
          return view('documents::add-client-document',compact('customer_list'));
    }

    /**
     * Get list of clients
     *
     * @param |null $list_status
     *      ACTIVE
     *      INACTIVE
     * @return datatable object
     * @throws \Exception
     */

    public function getList($list_status){
        $projectName = request('projectname');
        $projectNo = request('projectno');
        return datatables()->of(
            $this->documentRepository->getAll($projectName,$projectNo,$list_status)
        )->addIndexColumn()->toJson();
    }


    /**
     * Store a newly created resource in storage.
     * @param  DocumentRequest $request, $module
     * @return Response
     */

    public function store(DocumentRequest $request, $module){
        try {
            \DB::beginTransaction();
            //current = 0; archived =1
            $updateArchivedlist   = $this->documentRepository->updateArchived($request);
            $documentdetailsStore = $this->documentRepository->store($request, $module);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse($request->id));
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Display details of single resource
     *
     * @param [type] $id
     * @return json
     */

    public function getNameList($id,Request $request){

        $typeID = $request->input('typeid');
        return response()->json($this->documentRepository ->getDocumentNames($id,$typeID));
    }

    /**
     * Display details of single resource
     *
     * @param [type] $id
     * @return json
     */




    /**
     * Load the add client Page
     * @param [type] $typeid
     * @param [type] $id
     * @return \Illuminate\Http\Response
     */


    public function addClientDocument($typeid,$id){

        $employee_list = array();
        $client_list = array();
        $other_list = array();
        $employee_list = [];
        $client_list = [];

        if($typeid == EMPLOYEE){
            $employee_list = $this->documentRepository->getEmployeelist($id);

        }else if($typeid == CLIENT){

            $client_list = $this->documentRepository->getClientlist($id);
        }
        else {
            $other_list = $this->documentRepository->getOtherlist($typeid,$id);
        }
        return view('documents::add-documents',compact('other_list','client_list','employee_list','typeid'));
    }

     /**
     * Load the document listing Page
     *
     * @return \Illuminate\Http\Response
     */

    public function viewDocument($typeid,$id)
    {
        $employee_list = [];
        $other_list = [];
        if($typeid == EMPLOYEE){
            $employee_list = $this->documentRepository->getEmployeedetails($id);
         }
        else if($typeid == CLIENT){
            $employee_list = $this->documentRepository->getClientdetails($id);

        }else{
            $other_list = $this->documentRepository->getOtherlist($typeid,$id);

        }
         return view('documents::view-client-document',compact('typeid','id','employee_list','other_list'));

    }
    /**
     * Display details of Client and employee document summary
     *
     * @param $id,$typeid,$checked
     * @return json
     */

    public function viewDocumentlist($typeid,$id,$checked){
        //return $this->documentRepository->getClientdocumentList($id,$checked);

        if($typeid == CLIENT){
        return datatables()->of($this->documentRepository->getClientdocumentList($id,$checked))->toJson();
        }
         else if($typeid == EMPLOYEE){
        return datatables()->of($this->documentRepository->getEmployeeDocumentList($id,$checked))->toJson();
        }
        else {
            return datatables()->of($this->documentRepository->getOtherDocumentList($id,$checked))->toJson();
        }
    }

    public function otherVendor($id= null){

        $result = $this->OtherCategorylookup->select('id','category_name','document_type_id')->with('otherCategoryname')->where('id',$id)->get();
        return view('documents::add-vendor-document', compact('result'));
    }
  public function otherVendorlist($typeid,$id){

    return datatables()->of($this->documentRepository ->otherVendorAll($typeid,$id))->addIndexColumn()->toJson();
}


    public function archive(Request $request)
    {
        return $this->documentRepository->archiveDocuments($request);

    }

    /**
     * Remove the specified document from storage.
     * @return Response
     */

    public function destroy($id){
        try {
            \DB::beginTransaction();
             $this->documentRepository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }


}
