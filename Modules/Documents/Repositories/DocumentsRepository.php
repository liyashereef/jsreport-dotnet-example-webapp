<?php

namespace Modules\Documents\Repositories;

use Auth;
use DB;
use Carbon\Carbon;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Models\Customer;
use Modules\Client\Repositories\ClientRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Models\DocumentCategory;
use Modules\Admin\Models\DocumentType;
use Modules\Admin\Models\User;
use Modules\Admin\Models\DocumentNameDetail;
use Modules\Admin\Models\SecurityClearanceLookup;
use Modules\Admin\Models\CertificateMaster;
use Modules\Documents\Models\Document;
use App\Repositories\AttachmentRepository;
use Modules\Admin\Models\SecurityClearanceUser;
use Modules\Admin\Models\OtherCategoryLookup;
use Modules\Admin\Models\OtherCategoryName;
use Modules\Admin\Models\UserCertificate;
use Modules\Admin\Repositories\SecurityClearanceLookupRepository;
use Modules\Admin\Repositories\UserCertificateLookupRepository;
use Modules\Admin\Repositories\UserRepository;
use App\Services\HelperService;
use Spatie\Permission\Models\Role;
use Modules\Admin\Models\DocumentAccessPermission;
use Modules\Hranalytics\Models\CandidateTransitionAttachment;
use Modules\Recruitment\Models\RecCandidateDocuments;
use Modules\Recruitment\Models\RecCandidateAttachment;

class DocumentsRepository
{
    protected $customerModel, $DocumentcategoryModel, $DocumenttypeModel, $userModel, $attachmentRepository, $customerRepository, $otherCategorylookup, $otherCategoryname, $securityclearanceModel;

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */

    public function __construct(
        Customer $customerModel, ClientRepository $clientRepository, DocumentCategory $DocumentcategoryModel,
        DocumentType $DocumenttypeModel, User $userModel, DocumentNameDetail $documentNamesModel, SecurityClearanceLookup $securityclearanceModel,
        CertificateMaster $certificateMasterModel, Document $documentModel, AttachmentRepository $attachmentRepository,
        CustomerRepository $customerRepository, EmployeeAllocationRepository $employeeAllocationrepository,
        OtherCategoryLookup $otherCategorylookup, OtherCategoryName $otherCategoryname, HelperService $helperService,
        UserRepository $userRepository
    )
    {
        $this->model = $documentModel;
        $this->customerModel = $customerModel;
        $this->clientRepository = $clientRepository;
        $this->DocumentcategoryModel = $DocumentcategoryModel;
        $this->DocumenttypeModel = $DocumenttypeModel;
        $this->usermodel = $userModel;
        $this->documentnamemodel = $documentNamesModel;
        $this->securityclearncemodel = $securityclearanceModel;
        $this->certificatesmodel = $certificateMasterModel;
        $this->attachmentRepository = $attachmentRepository;
        $this->customerRepository = $customerRepository;
        $this->employeeAllocationRepository = $employeeAllocationrepository;
        $this->otherCategorylookup = $otherCategorylookup;
        $this->otherCategoryname = $otherCategoryname;
        $this->userRepository = $userRepository;
        $this->helperService = $helperService;
    }

    /**
     *  Function to get all the User records
     *
     * @param empty
     * @return  array
     *
     */

    public function getUserList($employeeno,$employeename,$statusId){

        $user_list = array();
        if (\Auth::user()->can('view_employee_document') || \Auth::user()->can('add_employee_document') || \Auth::user()->hasAnyPermission(['admin', 'super_admin'])) {
            $query = $this->userRepository->getUserLookup(null,['admin','super_admin'],null,true,null,true)->with(['candidate_transition','candidate_transition.attachment'])
            ->when($employeename != null, function ($q) use ($employeename) {
                return $q->where('id', $employeename);
            });
            if ($statusId != ':checked') {
                $query->where('active', $statusId);
            }
            $user_list = $query->get();

        } else {
            $employees = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id);
            $query = $this->usermodel->with(['candidate_transition','candidate_transition.attachment'])
            ->when($employeename != null, function ($q) use ($employeename) {
                return $q->where('id', $employeename);
            });
            if ($statusId != ':checked') {
                $query->where('active', $statusId);
            }
            $user_list = $query->whereIn('id',$employees)->get();
        }
        return $this->prepareDataForEmployees($user_list);
    }

    public function prepareDataForEmployees($user_list)
    {
        $datatable_rows = array();
        $employees = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id);
        $emp = $employees->toArray();
        foreach ($user_list as $key => $each_list) {
            $each_row["type_id"] = EMPLOYEE;
            $each_row["id"] = $each_list->id;
            $each_row["employee_details"] = data_get($each_list, 'name_with_emp_no');
            $each_row["username"] = $each_list->username;
            $each_row["phonenumber"] = $each_list->employee_profile->phone;
            $each_row["email"] = $each_list->email;
            if(!empty($each_list->candidate_transition)){
                $each_row['attachment_id'] = $each_list->candidate_transition->attachment->attachment_id;
            }
            if (in_array($each_list->id, $emp)) {
                $each_row["allocated_flag"] = 1;
            } else {
                $each_row["allocated_flag"] = 0;
            }

            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    /**
     * Get all Documents
     * @param $projectName
     * @param $projectNo
     * @param string $list_status
     * @return array
     */

    public function getAll($projectName, $projectNo, $list_status)
    {
        $user = Auth::user();
        $customerList = array();
        $customerList = $this->customerModel
            ->select(['project_number', 'id', 'client_name', 'contact_person_name', 'contact_person_email_id', 'contact_person_phone','active'])
            ->when($projectName != null, function ($q) use ($projectName) {
                return $q->where('id', $projectName);
            });
            if ( $list_status != ':checked') {
                $customerList->where('active', $list_status);
            }
        if ((\Auth::user()->can('view_client_document') || \Auth::user()->can('add_client_document')) || $user->hasAnyPermission(['admin', 'super_admin'])) {
            $customerList = $customerList->orderBy('project_number', 'asc')->get();
        } else {
            $customer_ids = $this->customerRepository->getAllAllocatedCustomerId([Auth::user()->id]);
            $customerList = $customerList->whereIn('id', $customer_ids)
                ->orderBy('project_number', 'asc')->get();
        }
        return $this->prepareDataForClientDocument($customerList);

    }


    public function prepareDataForClientDocument($customerList)
    {

        $datatable_rows = array();
        $customer_ids = $this->customerRepository->getAllAllocatedCustomerId([Auth::user()->id]);
        //dd($customer_ids);
        foreach ($customerList as $key => $each_list) {

            $each_row["type_id"] = CLIENT;
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["project_number"] = isset($each_list->project_number) ? $each_list->project_number : "--";
            $each_row["client_name"] = isset($each_list->client_name) ? $each_list->client_name : "--";
            $each_row["contact_person_name"] = isset($each_list->contact_person_name) ? $each_list->contact_person_name : "--";
            $each_row["contact_person_phone"] = isset($each_list->contact_person_phone) ? $each_list->contact_person_phone : "--";
            $each_row["contact_person_email_id"] = isset($each_list->contact_person_email_id) ? $each_list->contact_person_email_id : "--";
            if (in_array($each_list->id, $customer_ids)) {
                $each_row["allocated_flag"] = 1;
            } else {
                $each_row["allocated_flag"] = 0;
            }
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;

    }

    /**
     * Function to store and update Documents
     * @param  $request
     * @param  $module
     * @return array
     */

    public function store($request, $module)
    {

        $logged_in_user = \Auth::id();
        $cat_id = $request->document_category_id;
        $type_id = $request->document_type_id;
        $answer_type = $this->getCategoryModels($cat_id, $type_id);
        $request->request->add(['answer_type' => $answer_type]);
        $documentsStore = $this->model->updateOrCreate(array('id' => $request->get('id')), $request->all());
        $documentId = $documentsStore->id;
        $document_attachments = $request->document_attachment;
        if (!empty($document_attachments)) {
            foreach ($document_attachments as $key => $document_attachments) {
                $file = $this->attachmentRepository->saveAttachmentFile($module, $request, 'document_attachment.' . $key);
                $attachment_id = $file['file_id'];
                $data = ['attachment_id' => $attachment_id, 'created_by' => $logged_in_user];
                $storeAttachment = Document::updateOrCreate(array('id' => $documentId), $data);
            }
        }

        return $documentsStore;
    }

    /**
     *  Function to get all the client records
     *
     * @param empty
     * @return  array
     *
     */

    public function getClientlist($id)
    {

        $ClientList = array();
        $ClientList = $this->customerModel->select([
            'id', 'project_number', 'client_name', 'created_at'])->where('id', '=', $id)->orderBy('project_number', 'asc')->first();
        $documentCategory = $this->DocumentcategoryModel->select(['id', 'document_type_id', 'document_category'])->orderBy('document_category', 'asc')->where('document_type_id', CLIENT)->get();
        $data = array();
        $data['document_category'] = array();
        $data['client_id'] = $ClientList['id'];
        $data['project_number'] = $ClientList['project_number'];
        $data['client_name'] = $ClientList['client_name'];
        $data["uploaded_date"] = Carbon::now()->toFormattedDateString();
        $data["uploaded_time"] = Carbon::now()->format('H : i A');
        foreach ($documentCategory as $key => $each_list) {

            $data["document_category"][$each_list->id] = isset($each_list->document_category) ? ($each_list->document_category) : "";

        }

        //    dd($data);
        return $data;

    }

    /**
     *  Function to get all the employee records
     *
     * @param empty
     * @return  array
     *
     */


    public function getEmployeelist($id)
    {

        $employeeList = $this->usermodel->select(['id', 'first_name', 'last_name', 'created_at'])->where('id', '=', $id)->with(['trashedEmployee', 'employee_profile'])->first();
        $documentCategory = $this->DocumentcategoryModel->select(['id', 'document_type_id', 'document_category'])->orderBy('document_category', 'asc')->where('document_type_id', EMPLOYEE)->get();
        $data = array();
        $data['user_id'] = $employeeList['id'];
        $data['employee_details'] = data_get($employeeList, 'name_with_emp_no');
        $data["uploaded_date"] = Carbon::now()->toFormattedDateString();
        $data["uploaded_time"] = Carbon::now()->format('H : i A');
        foreach ($documentCategory as $key => $each_list) {
            $data["document_category"][$each_list->id] = $each_list->document_category;
        }
        return $data;
    }

    /**
     *  Function to get Employee Name and Number in Header of Summary page
     *
     * @param $id
     * @return  array
     *
     */

    public function getEmployeedetails($id)
    {

        $employeeList = $this->usermodel->select(['id', 'first_name', 'last_name'])->where('id', '=', $id)->with(['trashedEmployee', 'employee_profile'])->first();
        $data = array();
        // $data['employee_details'] = data_get($employeeList, 'name_with_emp_no');
        //$data['employee_details'] = $employeeList->trashedEmployee['employee_no']. ' - '  . $employeeList['first_name'].' '.$employeeList['last_name']  ;
        $data['employee_details'] = $employeeList['first_name'].' '.$employeeList['last_name'].' - '. $employeeList->trashedEmployee['employee_no'];

        return $data;

    }

    /**
     *  Function to get Project Name and Number in Header Summary page
     *
     * @param $id
     * @return  array
     *
     */


    public function getClientdetails($id)
    {

        $clientList = $this->customerModel->select(['id', 'project_number', 'client_name'])->where('id', '=', $id)->first();
        $data = array();
        //$data['client_details'] = $clientList['project_number'] . ' - '  . $clientList['client_name'] ;
        $data['client_details'] = $clientList['client_name'].' - '.$clientList['project_number'];
        return $data;

    }


    /**
     *  Function to get all the client records
     *
     * @param empty
     * @return  array
     *
     */

    public function getClientdocumentList($id, $checked)
    {
        $user = Auth::user();

        $doumentname_ids = $this->UserAccessedDocumentIds();
        if ((\Auth::user()->can('view_client_document') || \Auth::user()->can('add_client_document') || \Auth::user()->can('add_allocated_client_document')|| $user->hasAnyPermission(['admin', 'super_admin'])) || \Auth::user()->can('view_allocated_client_document')) {
            $query = $this->model
                ->select(['id', 'is_archived', 'created_at', 'attachment_id', 'customer_id', 'document_category_id', 'document_name_id', 'document_description', 'created_by'])
                ->orderBy('is_archived', 'asc')
                ->orderBy('id', 'desc')
                ->Where('customer_id', $id)
                ->where(function ($query) use ($doumentname_ids) {
                    $query->wherein('document_name_id', $doumentname_ids);
                    //$query->orwhere('created_by','!=', \Auth::user()->id);
                })
                ->where('document_type_id', '=', CLIENT);
            if ($checked != ':checked') {
                $query->where('is_archived', $checked);
            }
            $documentList = $query->with('documentCategory', 'documentName', 'projectDetails', 'attachment')
                ->get();

        } else {
            $documentList = array();
        }
        return $this->prepareDataForClientDocumentList($documentList);
    }

    /**
     *  Function to get all the client records
     *
     * @param empty
     * @return  array
     *
     */
    public function prepareDataForClientDocumentList($documentList)
    {
        $datatable_rows = array();
        foreach ($documentList as $key => $each_list) {
            $each_row["id"] = $each_list->id;
            $each_row["attachments"] = $each_list->attachment;
            $each_row["document_category"] = isset($each_list->documentCategory['document_category']) ? $each_list->documentCategory['document_category'] : "--";
            $each_row["document_name"] = isset($each_list->documentName['name']) ? $each_list->documentName['name'] : "--";
            $each_row["document_description"] = isset($each_list->document_description) ? $each_list->document_description : "--";
            $each_row["employee_details"] = $each_list->projectDetails['client_name'] . ' ' . '(' . $each_list->projectDetails['project_number'] . ')';
            $each_row["uploaded_date"] = isset($each_list->created_at) ? $each_list->created_at->toFormattedDateString() : "--";//isset($each_list->created_at) ? $this->helperService->convertDateFormat($each_list->created_at): "--";
            $each_row["uploaded_by"] = isset($each_list->createdBy) ? $each_list->createdBy->first_name . ' ' . $each_list->createdBy->last_name : "--";
            $each_row["is_archived"] = $each_list->is_archived == 1 ? "Archived" : "Current";
            array_push($datatable_rows, $each_row);

        }

        return $datatable_rows;

    }

    /**
     *  Function to get all the employee Document Summaryrecords
     *
     * @param empty
     * @return  array
     *
     */

    public function getEmployeedocumentList($id, $checked)
    {
        $user = Auth::user();
        $doumentname_ids = $this->UserAccessedDocumentIds();
        if ((\Auth::user()->can('add_employee_document') || \Auth::user()->can('view_employee_document') || \Auth::user()->can('add_allocated_employee_document') || \Auth::user()->can('view_allocated_employee_document')) || $user->hasAnyPermission(['admin', 'super_admin'])) {
            $query = $this->model->
            select(['id', 'user_id', 'is_archived','answer_type', 'created_at', 'document_name_id', 'attachment_id', 'document_category_id', 'document_description', 'created_by'])
                ->orderBy('is_archived', 'asc')
                ->orderBy('id', 'desc')
                ->Where('user_id', $id)
                ->where(function ($query) use ($doumentname_ids) {
                    $query->wherein('document_name_id', $doumentname_ids);
                    // $query->orwhere('created_by', \Auth::user()->id);
                })
                ->where('document_type_id', '=', EMPLOYEE);
            if ($checked != ':checked') {
                $query->where('is_archived', $checked);
            }
            $documentList = $query->with('securityClearance', 'certificateMaster', 'documentCategory', 'documentName', 'UserDetails.employee', 'attachment')->get();

        } else {
            $documentList = array();
        }
        return $this->prepareDataForEmployeeDocumentList($documentList);
    }

    public function prepareDataForEmployeeDocumentList($documentList)
    {
        $datatable_rows = array();
        foreach ($documentList as $key => $each_list) {
            $each_row["id"] = $each_list->id;
            $each_row["attachments"] = $each_list->attachment;
            $each_row["answer_type"] = $each_list->answer_type;
            if($each_list->answer_type == 'RecDocument'){
               $file  = RecCandidateDocuments::select('file_name')->where('id',$each_list->attachment_id)->first();
               $each_row["recruitment_document"] =  \Storage::disk('s3-recruitment')->temporaryUrl($file->file_name, Carbon::now()->addMinutes(30));
            }else if($each_list->answer_type == 'RecAttachment'){
                $file  = RecCandidateAttachment::select('attachment_file_name')->where('id',$each_list->attachment_id)->first();
                $each_row["recruitment_document"] =  \Storage::disk('s3-recruitment')->temporaryUrl($file->attachment_file_name, Carbon::now()->addMinutes(30));
            }
            $each_row["employee_details"] = $each_list->UserDetails['first_name'] . ' ' . $each_list->UserDetails['last_name'] . ' ' . '(' . $each_list->UserDetails->employee['employee_no'] . ')';
            $each_row["document_category"] = isset($each_list->documentCategory['document_category']) ? $each_list->documentCategory['document_category'] : "--";

            if ($each_list->document_category_id == 1) {
                $each_row["document_name"] = isset($each_list->securityClearance['security_clearance']) ? $each_list->securityClearance['security_clearance'] : "--";
            } else if ($each_list->document_category_id == 2) {
                $each_row["document_name"] = isset($each_list->certificateMaster['certificate_name']) ? $each_list->certificateMaster['certificate_name'] : "--";

            } else {
                $each_row["document_name"] = isset($each_list->documentName['name']) ? $each_list->documentName['name'] : "--";

            }
            $each_row["document_description"] = isset($each_list->document_description) ? $each_list->document_description : "--";
            $each_row["uploaded_date"] = isset($each_list->created_at) ? $each_list->created_at->toFormattedDateString() : "--";//isset($each_list->created_at)?$this->helperService->convertDateFormat($each_list->created_at):"--";
            $each_row["uploaded_by"] = isset($each_list->createdBy) ? $each_list->createdBy->first_name . ' ' . $each_list->createdBy->last_name : "--";
            $each_row["is_archived"] = $each_list->is_archived == 1 ? "Archived" : "Current";
            array_push($datatable_rows, $each_row);

        }
        return $datatable_rows;

    }

    /**
     * Function to get  details for other document Summary page
     * @param $request
     * @return array
     */
    public function getOtherdocumentList($id, $checked)
    {
        $doumentname_ids = $this->UserAccessedDocumentIds();
        $query = $this->model->
        select(['id', 'document_type_id', 'other_category_lookup_id', 'document_name_id', 'document_description', 'created_at', 'created_by', 'is_archived', 'attachment_id'])
            ->orderBy('is_archived', 'asc')
            ->where(function ($query) use ($doumentname_ids) {
                $query->wherein('document_name_id', $doumentname_ids);
                //$query->orwhere('created_by','!=', \Auth::user()->id);
            })
            ->orderBy('id', 'desc')->where('other_category_name_id', $id);
        if ($checked != ':checked') {
            $query->where('is_archived', $checked);
        }

        $OtherdocumentList = $query->with(['documentType', 'documentName', 'createdBy'])->get();

        return $this->prepareDataForOtherDocumentList($OtherdocumentList);

    }

    public function prepareDataForOtherDocumentList($OtherdocumentList)
    {
        $datatable_rows = array();
        foreach ($OtherdocumentList as $key => $each_list) {
            $each_row["id"] = isset($each_list['id']) ? $each_list['id'] : "--";
            $each_row["document_category"] = isset($each_list->documentType['document_type']) ? $each_list->documentType['document_type'] : "--";
            $each_row["other_category_lookup"] = isset($each_list->other_category_lookup_id) ? $each_list->other_category_lookup_id : "--";
            $each_row["document_name"] = isset($each_list->documentName['name']) ? $each_list->documentName['name'] : "--";
            $each_row["document_description"] = isset($each_list['document_description']) ? $each_list['document_description'] : "--";
            $each_row["uploaded_date"] = isset($each_list->created_at) ? $each_list->created_at->toFormattedDateString() : "--";//isset($each_list->created_at)?$this->helperService->convertDateFormat($each_list->created_at):"--";
            $each_row["uploaded_by"] = isset($each_list->createdBy) ? $each_list->createdBy->first_name . ' ' . $each_list->createdBy->last_name : "--";
            $each_row["is_archived"] = $each_list->is_archived == 0 ? "Current" : "Archived";
            $each_row["attachments"] = $each_list->attachment;
            array_push($datatable_rows, $each_row);
        }

        return $datatable_rows;
    }


    /**
     *  Function to get all the employee Document name list
     *
     * @param $id ,$typeID
     * @return  array
     *
     */

    public function getDocumentNames($id, $typeID)
    {
        if ($typeID == EMPLOYEE) {
            if ($id == 1) {
                return $this->securityclearncemodel->orderBy('security_clearance', 'asc')->get();
            } else if ($id == 2) {
                return $this->certificatesmodel->orderBy('certificate_name', 'asc')->get();
            } else {
                $doc_name_ids = $this->UserAccessedDocumentIds();
                return $this->documentnamemodel->wherein('id', $doc_name_ids)->orderBy('name', 'asc')->where('document_category_id', $id)->get();
            }
        } else {
            $doc_name_ids = $this->UserAccessedDocumentIds();
            return $this->documentnamemodel->wherein('id', $doc_name_ids)->orderBy('name', 'asc')->where('document_category_id', $id)->get();

        }

    }

    /**
     *  Function to get all the employee Document valid date
     *
     * @param $id ,$catID,$userID
     * @return  array
     *
     */

    public function getValidDate($id, $catID, $userID)
    {
        if ($catID == 1) {


        } else {

            return $this->documentnamemodel->orderBy('id')->where('document_category_id', $id)->get();

        }

    }


    /**
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */
    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.documents'), $request->document_type_id);
    }

    /**
     * Function to Download attachments
     * @param $file_id
     * @return array
     */

    public static function getAttachmentPathArrFromFile($file_id)
    {
        $attachment = Document::where('attachment_id', $file_id)->first();
        if (isset($attachment)) {
            $document_type_id = $attachment->document_type_id;
        }
        return array(config('globals.documents'), $document_type_id);
    }

    public static function getTransitionAttachmentPathArrFromFile($file_id)
    {
        $candidateEmployeesList = CandidateTransitionAttachment::where('attachment_id', $file_id)->with(['transition'])->first();
        if (isset($candidateEmployeesList)) {
            $candidateID = $candidateEmployeesList->transition->candidate_id;
        }
        return array(config('globals.candidate_employee'), $candidateID);

    }

    /**
     * Function to get answertype details
     * @param $id
     * @return array
     */

    public function getCategoryModels($id, $typeid)
    {
        if ($typeid == OTHER) {
            $modelName = 'Modules\Admin\Models\other_category_lookups';
        } else {
            if ($id == 1) {
                $modelName = 'Modules\Admin\Models\SecurityClearanceLookup';
            } else if ($id == 2) {
                $modelName = 'Modules\Admin\Models\CertificateMaster';
            } else {
                $modelName = 'Modules\Admin\Models\DocumentNameDetail';
            }
        }
        return $modelName;

    }

    /**
     * Function to update current to archived
     * @param $request
     * @return array
     */
    public function updateArchived($request)
    {
        $iscurrent = [];
        $documnetnamedetails = [];
        $conditionEmp = (request('document_type_id') == EMPLOYEE);
        $conditionClient = (request('document_type_id') == CLIENT);
        $conditionOther = (request('document_type_id') == OTHER);
        $doc_cat_id = $request->document_category_id;

        $documnetnamedetails = $this->documentnamemodel->where('document_type_id', $request->document_type_id)
            //is_auto_archive = 1 enabled; is_auto_archive = 0 disabled
            ->where('is_auto_archive', 1)
            ->where(function ($query) use ($request) {
                $query->where('document_category_id', $request->document_category_id)
                    ->orWhere('document_category_id', $request->other_category_lookup_id);
            })
            ->where('id', $request->document_name_id)
            ->first();

        if (isset($documnetnamedetails) && !empty($documnetnamedetails)) {

            $iscurrent = $this->model->select(['id'])->where('document_type_id', $request->document_type_id)
                ->where('document_category_id', $request->document_category_id)
                ->where('document_name_id', $request->document_name_id)
                ->where('is_archived', 0)
                ->latest('created_at')
                ->when($conditionEmp, function ($query) use ($request) {
                    return $query->where('user_id', $request->user_id);
                })
                ->when($conditionClient, function ($query) use ($request) {
                    return $query->where('customer_id', $request->customer_id);
                })
                ->first();
            if ($iscurrent != null) {
                return $this->model->where('id', $iscurrent->id)->where('is_archived', '=', 0)->update(array('is_archived' => 1));
            }

        } else if ($doc_cat_id == 1 || $doc_cat_id == 2) {

            // 1 = security clearness categorty id  2 = certificates category id
            $iscurrent = $this->model->select(['id'])->where('document_type_id', $request->document_type_id)
                ->where('document_category_id', $request->document_category_id)
                ->where('document_name_id', $request->document_name_id)
                ->where('is_archived', 0)
                ->latest('created_at')
                ->when($conditionEmp, function ($query) use ($request) {
                    return $query->where('user_id', $request->user_id);
                })
                ->when($conditionClient, function ($query) use ($request) {
                    return $query->where('customer_id', $request->customer_id);
                })
                ->first();
            if ($iscurrent != null) {
                return $this->model->where('id', $iscurrent->id)->where('is_archived', '=', 0)->update(array('is_archived' => 1));
            }

        } else {
            return false;
        }
    }

    /**
     * Function to get Other Document Details
     * @param $request
     * @return array
     */

    public function getOtherlist($typeid, $id)
    {

        $data = array();
        $data['otherlist'] = $this->otherCategoryname->select(['id', 'name', 'other_category_lookup_id'])->with(['otherCategory'])->where('id', $id)->first();
        $subcategorynames = $data['otherlist'];
        $doc_name_ids = $this->UserAccessedDocumentIds();
        $data['documentnames'] = $this->documentnamemodel->wherein('id', $doc_name_ids)->orderBy('name')->where('other_category_name_id', $subcategorynames->id)->pluck('name', 'id')->toArray();
        $data['is_valid'] = $this->documentnamemodel->select(['id', 'is_valid'])->where('other_category_name_id', $subcategorynames->id)->first();
        $data["uploaded_date"] = Carbon::now()->toFormattedDateString();
        $data["uploaded_time"] = Carbon::now()->format('H : i A');
        return $data;


    }

    /**
     * Function to get  details for other document page
     * @param $request
     * @return array
     */

    public function otherVendorAll($typeid, $id)
    {
        $categoryList = [];
        $documentCount = [];
        $userDetails = [];

        $categoryList = $this->otherCategoryname->select([
            'name', 'id', 'document_type_id', 'updated_at', 'other_category_lookup_id'])
            ->orderBy('name', 'asc')->where('other_category_lookup_id', $id)->where('document_type_id', $typeid)->get();
        return $this->prepareDataForVendorDocument($categoryList);

    }


    public function prepareDataForVendorDocument($categoryList)
    {

        $datatable_rows = array();

        foreach ($categoryList as $key => $each_list) {

            $totaldoccount = $this->getDocumentCount($each_list->id);
            $each_row["documentCount"] = count($totaldoccount);
            $lastupdatedate = $this->getlastUpdateddate($each_list->id);
            if ($each_row['documentCount'] == 0) {
                $each_row["updated_at"] = "--";
            } else {
                $each_row["updated_at"] = $lastupdatedate->updated_at->toFormattedDateString();//$this->helperService->convertDateFormat($lastupdatedate->updated_at);
            }
            $each_row["categ_id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["name"] = isset($each_list->name) ? $each_list->name : "--";

            array_push($datatable_rows, $each_row);


        }

        return $datatable_rows;

    }

    public function getDocumentCount($id)
    {

        $documentCount = $this->model->select([
            'id'])
            ->orderBy('id', 'asc')->where('other_category_name_id', $id)->get();
        return $documentCount;
    }

    public function getlastUpdateddate($id)
    {
        $last_updated = $this->model->select([
            'id', 'updated_at', 'is_archived'])
            ->orderBy('id', 'asc')
            ->where('other_category_name_id', $id)
            ->where('is_archived', '=', 0)->first();
        return $last_updated;

    }

    public function employeeLookUps()
    {

            $user_list = array();
        if (\Auth::user()->can('view_employee_document') || \Auth::user()->can('add_employee_document')) {
            $user_list = $this->userRepository->getUserLookup(null,['admin','super_admin'],null,true,null,true)
                ->orderBy('first_name', 'asc')
            ->get();
        } else {
            $employees = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id);
            $user_list = $this->usermodel
            ->whereIn('id',$employees)
            ->get();

        }

        return $user_list;

        }

    public function clienLookUps(){
        $customerList = array();
        $user = Auth::user();
        if ((\Auth::user()->can('view_client_document') || \Auth::user()->can('add_client_document')) || $user->hasAnyPermission(['admin', 'super_admin'])) {
            $customerList = $this->customerModel
                ->orderBy('client_name', 'asc')->get();
        } else {
            $customer_ids = $this->customerRepository->getAllAllocatedCustomerId([Auth::user()->id]);
            $customerList = $this->customerModel
                ->whereIn('id', $customer_ids)
                ->orderBy('client_name', 'asc')->get();
        }
        return $customerList;

    }

    public function UserAccessedDocumentIds()
    {
        $ids = [];
        $doesntHaveAccessIds = [];
        $certificateIds = [];
        $securityClearanceIds = [];

        $role_id = \Auth::user()->roles[0]->id;
        $checked_permissions = Role::findById($role_id)->permissions;
        foreach ($checked_permissions as $permission) {
            $permission_array[] = $permission->id;
        }
        $ids = DocumentAccessPermission::whereHas('AuthorisedAccessName', function ($query) use ($permission_array) {
            $query->wherein('permission_id', $permission_array);
        })
            ->pluck('document_name_id')->toArray();

        $doesntHaveAccessIds = DocumentNameDetail::doesnthave('AuthorizedAccessDetails')->pluck('id')->toArray();
        $certificateIds = CertificateMaster::all()->pluck('id')->toArray();
        $securityClearanceIds = SecurityClearanceLookup::all()->pluck('id')->toArray();

        $return = array_merge($ids, $doesntHaveAccessIds, $certificateIds, $securityClearanceIds);

        return $return;
    }

    public function archiveDocuments($request)
    {
        $document_ids = json_decode($request->get('document_ids'));
        if (!empty($document_ids)) {
            foreach ($document_ids as $id) {
                Document::where('id', $id)->update(['is_archived' => 1]);
            }
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    /**
     * Remove the specified Document from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }
}
