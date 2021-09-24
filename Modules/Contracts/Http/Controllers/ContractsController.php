<?php

namespace Modules\Contracts\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Repositories\AttachmentRepository;
use Modules\Contracts\Repositories\ContractsRepository;
use Modules\Contracts\Models\ContractsHolidayPaymentAgreement;
use Modules\Contracts\Models\ContractsAmendment;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Models\User;
use App\Models\Attachment;
use Modules\Contracts\Http\Requests\Uploadcmufform;
use Modules\Contracts\Http\Requests\Attachmainfilerequest;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Contracts\Models\ContractWrittenTemplateParty;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Contracts\Jobs\ContractExpiryReminder;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Modules\Contracts\Models\ClientContactInformation;

class ContractsController extends Controller
{
    protected $contractsrepository, $customerrepository, $helperService, $userrepository, $attachmentRepository, $customeremployeeallocationrepository;
    /**
     * Create Repository instance.
     * @param  \Modules\Contracts\Repositories\ContractsRepository $contractsrepository
     * @param  \App\Services\HelperService $helperService $helperService
     * @return void
     */

    public function __construct(CustomerEmployeeAllocationRepository $customeremployeeallocationrepository, CustomerRepository $customerrepository, ContractsRepository $contractsrepository, AttachmentRepository $attachmentRepository, UserRepository $userrepository, HelperService $helperService)
    {
        $this->contractsrepository = $contractsrepository;
        $this->userrepository = $userrepository;
        $this->helperService = $helperService;
        $this->attachmentRepository = $attachmentRepository;
        $this->customeremployeeallocationrepository = $customeremployeeallocationrepository;
        $this->customerrepository = $customerrepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('contracts::index');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function uploadform()
    {
        $lookUps = $this->contractsrepository->getLookups();

        return view('contracts::index', compact('lookUps'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('contracts::create');
    }

    public function attachfile(Request $request)
    {

        $upload_file = $request->get('upload_file');
        $filecontent = $request->file($upload_file);
        try {

            if ($upload_file == "cmuf_contract_document") {
                $messages = array(
                    $filecontent . '.required' => 'You cant leave Email field empty',

                );
                $validator = Validator::make(
                    [
                        'file'      => $filecontent,
                        'extension' => strtolower($filecontent->getClientOriginalExtension()),
                    ],
                    [
                        'file'          => 'required',
                        'extension'      => 'required|in:doc,docx,pdf,xls,xlsx,ods,ppt,pptx',

                    ],
                    [
                        'file'      => 'max:10000',
                    ]
                );


                if ($validator->fails()) {
                    $errormessage = [$upload_file => ["Accepts only doc,docx,pdf,xls,xlsx,ods,ppt,pptx"]];
                    return response()->json([
                        'success' => 'false',
                        'errors'  => $errormessage,
                    ], 400);
                } else {
                    $file = $this->attachmentRepository->saveAttachmentFile("contracts", $request, $upload_file);
                    echo ($file["file_id"]);
                }
            } else if ($upload_file == "rfc_pricing_template") {

                $validator = Validator::make(
                    [
                        'file'      => $filecontent,
                        'extension' => strtolower($filecontent->getClientOriginalExtension()),
                    ],
                    [
                        'file'          => 'required',
                        'extension'      => 'required|in:doc,docx,pdf,xls,xlsx,ods,ppt,pptx',
                    ]
                );

                if ($validator->fails()) {
                    $errormessage = [$upload_file => ["Accepts only doc,docx,pdf,xls,xlsx,ods,ppt,pptx"]];
                    return response()->json([
                        'success' => 'false',
                        'errors'  => $errormessage,
                    ], 400);
                } else {
                    $file = $this->attachmentRepository->saveAttachmentFile("contracts", $request, $upload_file);
                    echo ($file["file_id"]);
                }
            } else if ($upload_file == "po_upload") {
                $validator = Validator::make(
                    [
                        'file'      => $filecontent,
                        'extension' => strtolower($filecontent->getClientOriginalExtension()),
                    ],
                    [
                        'file'          => 'required',
                        'extension'      => 'required|in:doc,docx,pdf,xls,xlsx,ods,ppt,pptx',
                    ]
                );
                if ($validator->fails()) {
                    $errormessage = [$upload_file => ["Accepts only doc,docx,pdf,xls,xlsx,ods,ppt,pptx"]];
                    return response()->json([
                        'success' => 'false',
                        'errors'  => $errormessage,
                    ], 400);
                } else {
                    $file = $this->attachmentRepository->saveAttachmentFile("contracts", $request, $upload_file);
                    $this->attachmentRepository->unsetFilePersistant($file["file_id"]);
                    echo ($file["file_id"]);
                }
            } else if ($request->get("upload_amend") == "amendments") {

                $validator = Validator::make(
                    [
                        'file'      => $filecontent,
                        'extension' => strtolower($filecontent->getClientOriginalExtension()),
                    ],
                    [
                        'file'          => 'required',
                        'extension'      => 'required|in:doc,docx,pdf,xls,xlsx,ods,ppt,pptx',
                    ]
                );
                if ($validator->fails()) {
                    $errormessage = [$upload_file => ["Accepts only doc,docx,pdf,xls,xlsx,ods,ppt,pptx"]];
                    return response()->json([
                        'success' => 'false',
                        'errors'  => $errormessage,
                    ], 400);
                } else {
                    $file = $this->attachmentRepository->saveAttachmentFile("contracts", $request, $upload_file);
                    echo ($file["file_id"]);
                    $this->attachmentRepository->unsetFilePersistant($file["file_id"]);
                }
            }



            //echo $upload_file;



        } catch (\Throwable $th) {
            throw $th;
            return response()->json([
                'success' => 'false',
                'errors'  => $th->getMessage(),
            ], 400);
        }
    }
    /**
     * Get Add more client view.
     * @return Response
     */
    public function addmoreclientview(Request $request)
    {
        $clientcontactcount = $request->get('clientcontactcount');
        $lookUps = $this->contractsrepository->getLookups();
        return view("contracts::partials.clientcontactinformation", compact('lookUps', 'clientcontactcount'));
    }

    public function removeFile(Request $request)
    {
        $file_id = $request->get("attachmentid");
        $module = "contracts";

        $this->attachmentRepository->removeFile($file_id, $module);
    }

    public function addmoreamendmentview(Request $request)
    {

        $amendmentcount = $request->get("amendmentcount");
        return view("contracts::partials.amendments", compact('amendmentcount'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Uploadcmufform $request)
    {
        try {
            DB::beginTransaction();
            $cusrules = [];
            $messages = [];
            $clientcontactcount = $request->get('clientcontactcount');
            if ($clientcontactcount > 0) {
                for ($i = 1; $i < $clientcontactcount; $i++) {
                    $cusrules["primary_contact_" . $i] = 'required';
                    $messages["primary_contact_" . $i . ".required"] = "Client contact is mandatory";

                    $cusrules["contact_name_" . $i] = 'required';
                    $messages["contact_name_" . $i . ".required"] = "Contact name is mandatory.";

                    $cusrules["contact_jobtitle_" . $i] = 'required';
                    $messages["contact_jobtitle_" . $i . ".required"] = "Position is mandatory.";

                    $cusrules["contact_emailaddress_" . $i] = 'required';
                    $messages["contact_emailaddress_" . $i . ".required"] = "Email address is mandatory";

                    $cusrules["contact_phoneno_" . $i] = 'required';
                    $messages["contact_phoneno_" . $i . ".required"] = "Phone no is mandatory";

                    $cusrules["contact_cellno_" . $i] = 'required';
                    $messages["contact_cellno_" . $i . ".required"] = "Cell number is mandatory";
                }
                $validator = Validator::make($request->all(), $cusrules, $messages);
                if ($validator->passes()) {
                    $this->contractsrepository->saveContractform($request);
                } else {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
            } else {
                $this->contractsrepository->saveContractform($request);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            //$message =["message"=>"The given data was invalid.","errors"=>["cmuf_contract_document"=>["RFP Document required"]]];
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'cmuf_contract_document' => ['System Busy , Please try after sometime . Sorry for the inconvenience caused ...'],
            ]);
            throw $error;
        }
    }

    public function addAmendments(Request $request)
    {
        $amendmentdata = [];
        $amendmentdata["contract_id"] = $request->get("contract_id");
        $amendmentdata["amendment_description"] = $request->get("amendmentdescr");;
        $amendmentdata["amendment_attachment_id"] = $request->get("attachment_id");
        $contractamendment = new ContractsAmendment;
        $contractamendment->savecontractamendment($amendmentdata);
    }

    public function getAmendmentlist(Request $request)
    {
        $contractid = $request->get("contract_id");
        $contractamendments = ContractsAmendment::where('contract_id', $contractid)->get();
        return view("contracts::partials.amendment-view", compact('contractamendments', 'contractid'));
    }
    public function viewContract(Request $request)
    {
        $contractid = $request->get('lastinserted');

        $contractdataarray = $this->contractsrepository->getContractdata($contractid);
        $contractdata = $contractdataarray["contractdata"];
        $contract_attachment_id = $contractdata->contract_attachment_id;
        $createddate = date("Y-m-d", strtotime($contractdata->created_at));
        $rfc_pricing_tamplate_attachment_id = $contractdata->rfc_pricing_tamplate_attachment_id;
        $po_attachment = $contractdata->po_attachment;

        $customernumber = $contractdataarray["customername"];



        //$leaddivision = $contractdataarray["leaddivision"];
        //$salespersondivision = $contractdataarray["salespersondivision"];

        $salesuser = $contractdataarray["salesuser"];

        $contractclients = $contractdataarray["contractclients"];


        $attachment = null;
        $contractfile_name = null;
        $contracthash_name = null;

        $contractclients = $contractdataarray["contractclients"];
        $contractbillingcycle = $contractdata->getBillingFrequency->title;
        try {
            $contractonourtemplatetitle = (ContractWrittenTemplateParty::find($contractdata->contractonourtemplate))->templateparty;
        } catch (\Throwable $th) {
            $contractonourtemplatetitle = "";
        }

        $contractholidayagreement = $contractdataarray["contractholidayagreement"];
        $contractamendments = $contractdataarray["contractamendments"];
        $tothoursperweek = $contractdata->total_hours_perweek;
        $totalhoursarray = explode(".", $tothoursperweek);
        if (count($totalhoursarray) > 1) {
            $tothoursperweekminute = $totalhoursarray[1];
        } else {
            $tothoursperweekminute = 0;
        }
        if ($tothoursperweekminute > 0) {

            $decimalval = (int)(ceil(($tothoursperweekminute) * .6));
            $tothoursperweek = (explode(".", $tothoursperweek))[0] . "." . $decimalval;
        }

        return view("contracts::partials.cmuf-preview", compact(
            'contractid',
            'contract_attachment_id',
            'rfc_pricing_tamplate_attachment_id',
            'po_attachment',
            'contractdata',
            'attachment',
            'contractamendments',
            'contractholidayagreement',
            'contractbillingcycle',
            'contractfile_name',
            'contracthash_name',
            'customernumber',
            'businesssegment',
            'leaddivision',
            'salesuser',
            'contractonourtemplatetitle',
            'contractclients',
            'contractclients',
            'createddate',
            'tothoursperweek'
        ));
    }

    public function editContract(Request $request)
    {
        $contractid = $request->get('id');

        $contractdataarray = $this->contractsrepository->getContractdata($contractid);
        $contractdata = $contractdataarray["contractdata"];
        $contract_attachment_id = $contractdata->contract_attachment_id;
        $createddate = date("Y-m-d", strtotime($contractdata->created_at));
        $rfc_pricing_tamplate_attachment_id = $contractdata->rfc_pricing_tamplate_attachment_id;
        $po_attachment = $contractdata->po_attachment;

        $customernumber = $contractdataarray["customername"];
        $reasonforsubmission = $contractdataarray["reasonforsubmission"];
        $businesssegment = $contractdataarray["businesssegment"];
        $lineofbusiness = $contractdataarray["lineofbusiness"];
        $leaddivision = $contractdataarray["leaddivision"];
        $salespersondivision = $contractdataarray["salespersondivision"];

        $salesuser = $contractdataarray["salesuser"];

        $contractclients = $contractdataarray["contractclients"];


        $attachment = null;
        $contractfile_name = null;
        $contracthash_name = null;

        $contractclients = $contractdataarray["contractclients"];
        $contractbillingcycle = $contractdata->getBillingFrequency->title;

        $contractholidayagreement = $contractdataarray["contractholidayagreement"];
        $contractamendments = $contractdataarray["contractamendments"];

        return view("contracts::partials.cmuf-preview", compact(
            'contractid',
            'contract_attachment_id',
            'rfc_pricing_tamplate_attachment_id',
            'po_attachment',
            'contractdata',
            'attachment',
            'contractamendments',
            'contractholidayagreement',
            'contractbillingcycle',
            'contractfile_name',
            'contracthash_name',
            'customernumber',
            'reasonforsubmission',
            'businesssegment',
            'lineofbusiness',
            'leaddivision',
            'salesuser',
            'salespersondivision',
            'contractclients',
            'contractclients',
            'createddate'
        ));
    }

    public function editContractForm(Request $request)
    {
        $contractid = $request->id;

        $contractdataarray = $this->contractsrepository->getContractdata($contractid);
        $contractdata = $contractdataarray["contractdata"];

        $contract_attachment_id = $contractdata->contract_attachment_id;
        $Attachment = Attachment::find($contract_attachment_id);
        $createddate = date("Y-m-d", strtotime($Attachment->created_at));
        $rfc_pricing_tamplate_attachment_id = $contractdata->rfc_pricing_tamplate_attachment_id;
        $po_attachment = $contractdata->po_attachment;
        $lookUps = $this->contractsrepository->getLookups();
        $customernumber = $contractdataarray["customername"];


        $salesuser = $contractdataarray["salesuser"];
        $contractclients = $contractdataarray["contractclients"];


        $attachment = null;
        $contractfile_name = null;
        $contracthash_name = null;

        $contractclients = $contractdataarray["contractclients"];
        $contractbillingcycle = $contractdata->getBillingFrequency->title;

        $contractholidayagreement = $contractdataarray["contractholidayagreement"];
        $contractamendments = $contractdataarray["contractamendments"];

        $roles = \Auth::user()->roles->pluck('name')->toArray();
        $editpermission = 0;
        if (\Auth::user()->hasAnyPermission(["super_admin", "edit_contract"])) {
            $editpermission = 1;
        }
        try {
            $contractonourtemplatetitle = (ContractWrittenTemplateParty::find($contractdata->contractonourtemplate))->templateparty;
        } catch (\Throwable $th) {
            $contractonourtemplatetitle = "";
        }
        $tothoursperweek = $contractdata->total_hours_perweek;
        $totalhoursarray = explode(".", $tothoursperweek);
        if (count($totalhoursarray) > 1) {
            $tothoursperweekminute = $totalhoursarray[1];
        } else {
            $tothoursperweekminute = 0;
        }
        if ($tothoursperweekminute > 0) {
            $decimalval = (int)(ceil((($tothoursperweekminute)) * .6));
            $tothoursperweek = (explode(".", $tothoursperweek))[0] . "." . $decimalval;
        }


        $prepopulateddata = $this->contractsrepository->prePopulatedata($contractdata);
        $total_hours_perweekarray = explode(".", $contractdata->total_hours_perweek);
        return view("contracts::cmuf-edit", compact(
            'contractid',
            'editpermission',
            'contract_attachment_id',
            'rfc_pricing_tamplate_attachment_id',
            'po_attachment',
            'contractdata',
            'attachment',
            'contractamendments',
            'contractholidayagreement',
            'contractbillingcycle',
            'contractfile_name',
            'contracthash_name',
            'customernumber',
            'contractonourtemplatetitle',
            'prepopulateddata',
            'salesuser',
            'contractclients',
            'contractclients',
            'createddate',
            'lookUps',
            'tothoursperweek'
        ));
    }

    public function removeAmendment(Request $request)
    {
        $attachment_id = $request->attachment_id;
        $attached_file_id = $request->attached_file_id;
        $removeFile = ContractsAmendment::find($attachment_id)->delete();
        if ($removeFile > 0) {
            $module = "contracts";
            if ($attached_file_id > 0) {
                try {
                    $this->attachmentRepository->removeFile($attached_file_id, $module);
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
            $content['success'] = true;
            $content['message'] = 'Removed successfully';
            $content['code'] = 200;
        } else {
            $content['success'] = false;
            $content['message'] = 'There is some error . Please try after sometime';
            $content['code'] = 406;
        }
        return json_encode($content, true);
    }

    public function addContractclients(Request $request)
    {
        $data["primary_contact"] = $request->get('clientuserid');
        $data["contact_name"] = $request->get('clientname');
        $data["contact_jobtitle"] = $request->get('jobtitle');
        $data["contact_emailaddress"] = $request->get('emailaddress');
        $data["contact_phoneno"] = $request->get('phoneno');
        $data["contact_cellno"] = $request->get('cellno');
        $data["contact_faxno"] = $request->get('faxno');
        $data["contractid"] = $request->get('contractid');
        $data["status"] = true;
        if ($this->contractsrepository->saveClientcontact($data)) {
            $content['success'] = true;
            $content['message'] = 'Client Added successfully';
            $content['code'] = 200;
        } else {
            $content['success'] = false;
            $content['message'] = 'There is some error . Please try after sometime';
            $content['code'] = 406;
        }
        return json_encode($content, true);
    }
    public function removeContractclients(Request $request)
    {
        $contractid = $request->contractid;
        $clientid = $request->clientid;

        $count = ClientContactInformation::where('contractid', $contractid)->count();
        if ($count > 1) {
            try {
                $removeclient = ClientContactInformation::find($clientid)->delete();
                if ($removeclient) {
                    $content['success'] = true;
                    $content['message'] = 'Client removed from contract';
                    $content['code'] = 200;
                } else {
                    $content['success'] = false;
                    $content['message'] = 'Please try after sometime';
                    $content['code'] = 406;
                }
            } catch (\Throwable $th) {
                $content['success'] = false;
                $content['message'] = 'Please try after sometime';
                $content['code'] = 406;
            }
        } else {
            $content['success'] = false;
            $content['message'] = 'One client is mandatory';
            $content['code'] = 406;
        }
        return json_encode($content);
    }

    public function editContractblocks(Request $request)
    {
        $contractid = $request->contractid;
        $dbvariable = json_decode($request->dbvariable, true);
        $holidayarray = json_decode($request->holidayarray, true);
        if ($dbvariable != null) {
            $this->contractsrepository->editContractblocks($contractid, $dbvariable);
        }
        if ($holidayarray != "") {
            //$holidayid = str_replace("holiday-payment-","",$holidayarray["holidayid"]);
            //$holidayvalue = $holidayarray["holidayvalue"];
            foreach ($holidayarray as $key => $value) {
                try {
                    $holidayid = str_replace("holiday-payment-", "", $value["holidayid"]);
                    $holidayvalue = $value["holidayvalue"];
                    $contractdetails = ContractsHolidayPaymentAgreement::where(['contract_id' => $contractid, 'holiday_id' => $holidayid])->first();

                    $contractdetails->paymentstatus_id = $value["holidayvalue"];
                    $contractdetails->save();
                    //->update(['paymentstatus_id'=>$holidayvalue]);//code...
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }
    }

    public function viewAllContracts(Request $request)
    {
        $role = ['area_manager'];
        $regionalmanager = $this->userrepository->getUserLookup($role);
        $lookups['customers'] = $this->customerrepository->getCustomerList();
        $lookups['regionalmanager'] = $regionalmanager;

        $cmuflist = $this->contractsrepository->getContracts();
        return view('contracts::listcmuf', compact('cmuflist', 'lookups'));
    }

    public function getFilteredContracts(Request $request)
    {
        $cmuflist = $this->contractsrepository->getFilteredContracts($request);
        //return datatables()->of($cmuflist)->addIndexColumn()->toJson();
        return view("contracts::partials.listcmuf", compact('cmuflist'));
    }

    public function getContractslist(Request $request)
    {
        $cmuflist = $this->contractsrepository->getContracts();
        return datatables()->of($cmuflist)->addIndexColumn()->toJson();
    }

    /**
     * Return specific user details.
     * @return Response
     */

    public function downloadcontractattachment(Request $request)
    {
        $contract_id = $request->get('contract_id');
        $file_id = $request->get('file_id');
        $date = $request->get('date');
        $file_type = $request->get('filetype');

        $module = "contracts";
        try {
            $request->request->add(['file_id' => $file_id, 'module' => $module]);

            $download_details_arr = $this->attachmentRepository->downloadDetails($request);

            $filepath = str_replace("contracts_attachment", "contracts_attachment/" . $date, $download_details_arr['path']);
            return response()->download($filepath, $download_details_arr['name']);
        } catch (\Exception $e) {
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
        $filepath = $request->get("filepath");

        return response()->download(storage_path('app/' . $filepath));
    }

    public function getuserdetails(Request $request)
    {
        $userid = $request->get('userid');
        $userdetails = $this->userrepository->getUserDetails($userid);
        $email = $userdetails->email;
        $name = $userdetails->first_name . " " . $userdetails->last_name;
        $positionid = $userdetails->employee->position_id;
        $officenumber = $userdetails->employee->phone;
        if ($userdetails->employee->phone_ext != "") {
            //$officenumber.= "ext-".$userdetails->employee->phone_ext;
        }
        $cellnumber = $userdetails->employee->cell_no;
        $faxnumber = " ";

        $data["name"] = $name;
        $data["positionid"] = $positionid;
        $data["officenumber"] = $officenumber;
        $data["cellnumber"] = $cellnumber;
        $data["faxnumber"] = $faxnumber;
        $data["email"] = $email;
        return json_encode($data, true);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('contracts::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('contracts::edit');
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
    public function destroy()
    {
    }

    public function uploadformmainattachment(Request $request)
    {
        $file = $request->file('document');
        $name = $file->getClientOriginalName() . '.' . $file->getClientOriginalExtension();

        $image['filePath'] = $name;

        //$file->move(public_path().'/uploads/', $name);
        //$this->contractsrepository->store($request,"contracts");



    }
    /**
     * Display a listing of the client with the request.
     * @return Array
     */
    public function getClientdetails(Request $request)
    {
        $clientid = $request->get('clientid');
        $clientdata = $this->contractsrepository->getContractclientdetails($clientid);
        return json_encode($clientdata);
    }

    public function  contractExpiryEmailNotification()
    {
        try {
            DB::beginTransaction();
            ContractExpiryReminder::dispatch();
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
