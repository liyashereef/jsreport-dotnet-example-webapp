<?php

namespace Modules\Contracts\Repositories;

use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\HelperService;
use App\Repositories\MailQueueRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\ReasonForSubmissionRepository;
use Modules\Admin\Repositories\BusinessLineRepository;
use Modules\Admin\Repositories\BusinessSegmentRepository;
use Modules\Admin\Repositories\DivisionLookupRepository;
use Modules\Admin\Repositories\HolidayRepository;
use Modules\Admin\Repositories\HolidayPaymentAllocationRepository;
use Modules\Admin\Repositories\ContractBillingCycleRepository;
use Modules\Admin\Repositories\ContractPaymentMethodRepository;
use Modules\Admin\Repositories\ContractDeviceAccessRepository;
use Modules\Admin\Repositories\ContractCellphoneProviderRepository;
use Modules\Admin\Models\Customer;
use Modules\Contracts\Models\Cmuf;
use Modules\Contracts\Models\ClientContactInformation;
use Modules\Contracts\Models\ContractsHolidayPaymentAgreement;
use Modules\Contracts\Models\ContractsAmendment;
use Modules\Client\Repositories\ClientRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Models\User;
use App\Repositories\AttachmentRepository;
use Modules\Admin\Repositories\ParentCustomerRepository;
use Modules\Admin\Repositories\OfficeAddressRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\ContractBillingRateChangeRepository;
use Modules\Admin\Repositories\PositionLookupRepository;
use Modules\Admin\Models\StatHolidays;
use Modules\Contracts\Models\ContractWrittenTemplateParty;

class ContractsRepository
{

    protected $customerModel, $contractdeviceaccessrepository, $contractcellphoneproviderrepository, $cmuf, $contractbillingcyclerepository, $contractpaymentmethodrepository, $holidayrepository, $holidaypaymentallocationrepository, $positionlookuprepository, $lineofbusiness, $businesssegmentrepository, $contractbillingratechangerepository, $userrepository, $officeaddressrepository, $parentcustomerrepository, $reasonformsubmissionrepository, $DocumentcategoryModel, $DocumenttypeModel, $userModel, $attachmentRepository, $customerRepository;

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    public function __construct(
        Customer $customerModel,
        ContractDeviceAccessRepository $contractdeviceaccessrepository,
        ContractCellphoneProviderRepository $contractcellphoneproviderrepository,
        ContractBillingCycleRepository $contractbillingcyclerepository,
        ContractPaymentMethodRepository $contractpaymentmethodrepository,
        Cmuf $cmuf,
        HolidayPaymentAllocationRepository $holidaypaymentallocationrepository,
        HolidayRepository $holidayrepository,
        PositionLookupRepository $positionlookuprepository,
        OfficeAddressRepository $officeaddressrepository,
        BusinessLineRepository $lineofbusinessRepository,
        ParentCustomerRepository $parentcustomerrepository,
        BusinessSegmentRepository $businesssegmentrepository,
        ClientRepository $clientRepository,
        DivisionLookupRepository $divisionlookuprepository,
        User $userModel,
        AttachmentRepository $attachmentRepository,
        ContractBillingRateChangeRepository $contractbillingratechangerepository,
        CustomerRepository $customerRepository,
        EmployeeAllocationRepository $employeeAllocationrepository,
        UserRepository $userrepository,
        ReasonForSubmissionRepository $reasonforsubmissionRepository,
        MailQueueRepository $mailQueueRepository
    ) {

        $this->cmuf = $cmuf;
        $this->customerModel = $customerModel;
        $this->clientRepository = $clientRepository;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->usermodel = $userModel;



        $this->attachmentRepository = $attachmentRepository;
        $this->customerRepository = $customerRepository;
        $this->employeeAllocationRepository = $employeeAllocationrepository;
        $this->reasonforsubmissionRepository = $reasonforsubmissionRepository;
        $this->lineofbusinessRepository = $lineofbusinessRepository;
        $this->businesssegmentrepository = $businesssegmentrepository;
        $this->divisionlookuprepository = $divisionlookuprepository;
        $this->parentcustomerrepository = $parentcustomerrepository;
        $this->officeaddressrepository = $officeaddressrepository;
        $this->userrepository = $userrepository;
        $this->contractbillingratechangerepository = $contractbillingratechangerepository;
        $this->positionlookuprepository = $positionlookuprepository;
        $this->holidaypaymentallocationrepository = $holidaypaymentallocationrepository;
        $this->holidayrepository = $holidayrepository;
        $this->contractbillingcyclerepository = $contractbillingcyclerepository;
        $this->contractpaymentmethodrepository = $contractpaymentmethodrepository;
        $this->contractdeviceaccessrepository = $contractdeviceaccessrepository;
        $this->contractcellphoneproviderrepository = $contractcellphoneproviderrepository;
    }

    public function saveContractform($request)
    {

        $contractattachmentid = $request->get('contract_document_attachment');
        $poattachmentid = 0;
        $pofile = $request->file('po_upload');
        if ($pofile != "") {
            $poattachmentid = $request->get('po_document_attachment');
        }


        $rfc_pricing_tamplate_attachment_id = $request->get('rfc_document_attachment');

        $contract_name = $request->get('customer_client');
        $contract_number = $request->get('contract_number');
        $submissiondate = date("Y-m-d");
        $area_manager_id = $request->get('area_manager_id');
        $area_manager_text = $request->get('area_manager_text');
        $reason_for_submission = $request->get('reason_for_submission');
        $contractonourtemplate = $request->get('contractonourtemplate');

        $formdata = [];
        $formdata["contract_attachment_id"] = $contractattachmentid;
        $formdata["contract_name"] = $contract_name;
        $formdata["contract_number"] = $contract_number;
        $formdata["submissiondate"] = $submissiondate;
        $formdata["area_manager_id"] = $area_manager_id;
        $formdata["area_manager_text"] = $area_manager_text;
        $formdata["reason_for_submission"] = $reason_for_submission;

        $formdata["business_segment"] = $request->get('business_segment');
        $formdata["line_of_business"] = $request->get('line_of_business');
        $formdata["multidivisioncontract"] = $request->get('multidivision');
        $formdata["lead_division"] = $request->get('division_lookup');
        $formdata["master_entity"] = $request->get('masterentity');
        $formdata["parent_customer"] = $request->get('master_customer');

        $formdata["area_manager"] = $request->get('area_manager');
        $formdata["area_manager_position_text"] = $request->get('area_manager_position_text');

        $formdata["area_manager_email_address"] = $request->get('area_manager_email_address');
        $formdata["area_manager_office_number"] = $request->get('area_manager_office_number');
        $formdata["area_manager_cell_number"] = $request->get('area_manager_cell_number');
        $formdata["area_manager_fax_number"] = $request->get('area_manager_fax_number');
        $formdata["office_address"] = $request->get('office_address');

        $formdata["sales_employee_id"] = $request->get('sales_employee_id');

        $formdata["sales_contact_job_title"] = $request->get('sales_contact_job_title');
        $formdata["sales_contact_emailaddress"] = $request->get('sales_contact_emailaddress');
        $formdata["sales_contact_office_number"] = $request->get('sales_contact_office_number');

        $formdata["sales_contact_cell_number"] = $request->get('sales_contact_cell_number');
        $formdata["sales_contact_faxno"] = $request->get('sales_contact_faxno');
        $formdata["sales_contact_division"] = $request->get('sales_contact_division');
        $formdata["sales_contact_office_address"] = $request->get('sales_contact_office_address');

        $formdata["contract_startdate"] = $request->get('contract_startdate');
        $formdata["contract_length"] = $request->get('contract_length');
        $formdata["contract_enddate"] = $request->get('contract_enddate');
        $formdata["renewable_contract"] = $request->get('renewable_contract');
        $formdata["contract_length_renewal_years"] = $request->get('contract_length_renewal_years');
        $formdata["contractonourtemplate"] = $request->get('contractonourtemplate');


        $formdata["contracttemplatename"] = $request->get('contracttemplatename');
        $formdata["termination_clause_client"] = $request->get('termination_clause_client');
        $formdata["terminationnoticeperiodclient"] = $request->get('terminationnoticeperiodclient');
        $formdata["termination_clause"] = $request->get('termination_clause');
        $formdata["terminationnoticeperiod"] = $request->get('terminationnoticeperiod');
        $formdata["billing_ratechange"] = $request->get('billing_ratechange');
        $formdata["contract_annualincrease_allowed"] = $request->get('contract_annualincrease_allowed');
        if ($request->get('rfc_document_attachment') == null) {
            $formdata["rfc_pricing_tamplate_attachment_id"] = 0;
        } else {
            $formdata["rfc_pricing_tamplate_attachment_id"] = $request->get('rfc_document_attachment');
        }

        $formdata["total_annual_contract_billing"] = $request->get('total_annual_contract_billing');
        $formdata["total_annual_contract_wages_benifits"] = $request->get('total_annual_contract_wages_benifits');
        $formdata["total_annual_expected_contribution_margin"] = $request->get('total_annual_expected_contribution_margin');
        $formdata["total_hours_perweek"] = $request->get('total_hours_perweek');
        if ($request->get('total_hours_perweek_minutes') > 0) {
            $decimalval = intval($request->get('total_hours_perweek_minutes')) / .6;
            $formdata["total_hours_perweek"] = $formdata["total_hours_perweek"] . "." . $decimalval;
        }
        $formdata["average_billrate"] = $request->get('average_billrate');
        $formdata["average_wagerate"] = $request->get('average_wagerate');
        $formdata["average_markup"] = $request->get('average_markup');

        $formdata["contract_billing_cycle"] = $request->get('contract_billing_cycle');
        $formdata["contract_payment_method"] = $request->get('contract_payment_method');

        $formdata["ponumber"] = $request->get('ponumber');
        $formdata["pocompanyname"] = $request->get('pocompanyname');
        $formdata["poattentionto"] = $request->get('poattentionto');
        $formdata["pomailingaddress"] = $request->get('pomailingaddress');
        $formdata["potitle"] = $request->get('potitle');
        $formdata["pocity"] = $request->get('pocity');
        $formdata["popostalcode"] = $request->get('popostalcode');
        $formdata["pophone"] = $request->get('pophone');
        $formdata["poemail"] = $request->get('poemail');
        $formdata["pocellno"] = $request->get('pocellno');
        $formdata["pofax"] = $request->get('pofax');
        $formdata["ponotes"] = $request->get('ponotes');
        $formdata["ponotes"] = $request->get('ponotes');


        if ($request->get('po_document_attachment') < 1 || $request->get('po_document_attachment') == null) {
            $formdata["po_attachment"] = 0;
        } else {
            $formdata["po_attachment"] = $poattachmentid;
        }

        $formdata["supervisorassigned"] = $request->get('supervisorassigned');
        if ($request->get('supervisorassigned') > 0) {
            $formdata["supervisoremployeenumber"] = $request->get('supervisoremployeenumber');
            $formdata["employeename"] = $request->get('employeename');
            $formdata["viewtrainingperformance"] = $request->get('viewtrainingperformance');
            $formdata["employeecellphone"] = $request->get('employeecellphone');
            $formdata["employeeemailaddress"] = $request->get('employeeemailaddress');
            $formdata["employeetelephone"] = $request->get('employeetelephone');
            $formdata["employeefaxno"] = $request->get('employeefaxno');
            $formdata["contractcellphoneprovider"] = $request->get('contractcellphoneprovider');
            $formdata["supervisortabletrequired"] = $request->get('supervisortabletrequired');
            $formdata["supervisorcgluser"] = $request->get('supervisorcgluser');
            $formdata["supervisorpublictransportrequired"] = $request->get('supervisorpublictransportrequired');
            $formdata["direction_nearest_intersection"] = $request->get('direction_nearest_intersection');
            $formdata["department_at_site"] = $request->get('department_at_site');
            $formdata["delivery_hours"] = $request->get('delivery_hours');
            $formdata["supervisorcanmailbesent"] = $request->get('supervisorcanmailbesent');
            $formdata["contractdeviceaccess"] = $request->get('contractdeviceaccess');
        } else {
            $formdata["supervisoremployeenumber"] = 0;
            $formdata["employeename"] = 0;
            $formdata["viewtrainingperformance"] = 0;
            $formdata["employeecellphone"] = 0;
            $formdata["employeeemailaddress"] = "";
            $formdata["employeetelephone"] = "";
            $formdata["employeefaxno"] = "";
            $formdata["contractcellphoneprovider"] = $request->get('contractcellphoneprovider');
            $formdata["supervisortabletrequired"] = $request->get('supervisortabletrequired');
            $formdata["supervisorcgluser"] = $request->get('supervisorcgluser');
            $formdata["supervisorpublictransportrequired"] = $request->get('supervisorpublictransportrequired');
            $formdata["direction_nearest_intersection"] = $request->get('direction_nearest_intersection');
            $formdata["department_at_site"] = $request->get('department_at_site');
            $formdata["delivery_hours"] = $request->get('delivery_hours');
            $formdata["supervisorcanmailbesent"] = $request->get('supervisorcanmailbesent');
            $formdata["contractdeviceaccess"] = $request->get('contractdeviceaccess');
        }


        $formdata["scopeofwork"] = $request->get('scopeofwork');
        try {
            $savedcontractid = $this->save($formdata);
        } catch (\Throwable $th) {
            throw $th;
        }

        $jsonarray = ["lastinserted" => $savedcontractid];
        $clientcontactcount = $request->get('clientcontactcount');
        if ($savedcontractid > 0) {

            $this->saveholidayagreement($savedcontractid, $request);

            $primary_contact = $request->get("primary_contact");
            $contact_name = $request->get("contact_name");
            $contact_jobtitle = $request->get("contact_jobtitle");
            $contact_emailaddress = $request->get("contact_emailaddress");
            $contact_phoneno = $request->get("contact_phoneno");
            $contact_cellno = $request->get("contact_cellno");
            $contact_faxno = $request->get("contact_faxno");

            $data["primary_contact"] = $primary_contact;
            $data["contact_name"] = $contact_name;
            $data["contact_jobtitle"] = $contact_jobtitle;
            $data["contact_emailaddress"] = $contact_emailaddress;
            $data["contact_phoneno"] = $contact_phoneno;
            $data["contact_cellno"] = $contact_cellno;
            $data["contact_faxno"] = $contact_faxno;
            $data["contractid"] = $savedcontractid;

            $this->saveClientcontact($data);
            $primary_contact = "";
            $contact_name = "";
            $contact_jobtitle = "";
            $contact_emailaddress = "";
            $contact_phoneno = "";
            $contact_cellno = "";
            $contact_faxno = "";
            if ($clientcontactcount > 1) {
                for ($i = 1; $i < intval($clientcontactcount); $i++) {

                    $primary_contact = $request->get("primary_contact_" . $i);
                    $contact_name = $request->get("contact_name_" . $i);
                    $contact_jobtitle = $request->get("contact_jobtitle_" . $i);
                    $contact_emailaddress = $request->get("contact_emailaddress_" . $i);
                    $contact_phoneno = $request->get("contact_phoneno_" . $i);
                    $contact_cellno = $request->get("contact_cellno_" . $i);
                    $contact_faxno = $request->get("contact_faxno_" . $i);

                    $data["primary_contact"] = $primary_contact;
                    $data["contact_name"] = $contact_name;
                    $data["contact_jobtitle"] = $contact_jobtitle;
                    $data["contact_emailaddress"] = $contact_emailaddress;
                    $data["contact_phoneno"] = $contact_phoneno;
                    $data["contact_cellno"] = $contact_cellno;
                    $data["contact_faxno"] = $contact_faxno;
                    $data["contractid"] = $savedcontractid;
                    try {
                        $this->saveClientcontact($data);
                    } catch (\Throwable $th) {
                        $this->handleCmuferror($savedcontractid);
                        break;
                    }
                    $primary_contact = "";
                    $contact_name = "";
                    $contact_jobtitle = "";
                    $contact_emailaddress = "";
                    $contact_phoneno = "";
                    $contact_cellno = "";
                    $contact_faxno = "";
                }
            }

            $amendment_description = $request->get("amendment_description");
            $amendment_attachment_idfile = $request->get("amendment_document_attachment");

            if ($amendment_attachment_idfile != "" || $amendment_description != "") {

                $module = "contracts";
                if ($amendment_attachment_idfile != "") {
                    //$file = $this->attachmentRepository->saveAttachmentFile($module, $request, "amendment_attachment_id");
                } else {
                    $file["file_id"] = 0;
                }



                $contractamendment = new ContractsAmendment;
                $amendmentdata["contract_id"] = $savedcontractid;
                $amendmentdata["amendment_description"] = $amendment_description;
                $amendment_document_attachment = $request->get("amendment_document_attachment");
                if ($amendmentdata["amendment_description"] != "" || $amendment_document_attachment != "") {
                    try {

                        $fileextension = $request->file("amendment_attachment_id")->getClientOriginalExtension();

                        if ($request->get('amendment_document_attachment') < 1 && $fileextension != "") {
                            $filefield = $this->attachmentRepository->saveAttachmentFile("contracts", $request, "amendment_attachment_id");
                            $amendment_document_attachment = $filefield["file_id"];
                        }
                    } catch (\Throwable $th) {
                    }

                    $amendmentdata["amendment_attachment_id"] = $amendment_document_attachment;

                    $contractamendment->savecontractamendment($amendmentdata);
                }
            }

            $amendmentcount = $request->get("amendmentcount");
            if ($amendmentcount > 1) {
                for ($i = 1; $i < $amendmentcount; $i++) {


                    $amendment_description = $request->get("amendment_description_" . $i);
                    $amendment_attachment_id = $request->get("amendment_document_attachment_" . $i);

                    if ($amendment_attachment_idfile != "" || $amendment_description != "") {
                        try {

                            $fileextension = $request->file("amendment_attachment_id_" . $i)->getClientOriginalExtension();

                            if ($request->get('amendment_document_attachment_' . $i) < 1 && $fileextension != "") {
                                $filefield = $this->attachmentRepository->saveAttachmentFile("contracts", $request, "amendment_attachment_id_" . $i);
                                $amendment_attachment_id = $filefield["file_id"];
                            }
                        } catch (\Throwable $th) {
                        }
                    }

                    $amendmentdata["contract_id"] = $savedcontractid;
                    $amendmentdata["amendment_description"] = $amendment_description;
                    $amendmentdata["amendment_attachment_id"] = $amendment_attachment_id;
                    if (str_replace($amendment_description, " ", "") != "" || $amendment_attachment_id > 0) {
                        $contractamendment = new ContractsAmendment;
                        $contractamendment->savecontractamendment($amendmentdata);
                    }
                }
            }

            try {

                $fileextension = $request->file("po_upload")->getClientOriginalExtension();

                if ($request->get('po_document_attachment') < 1 && $fileextension != "") {
                    $pofileid = $this->attachmentRepository->saveAttachmentFile("contracts", $request, "po_upload");

                    $cmuf = Cmuf::where('id', $savedcontractid)->first();
                    $cmuf->po_attachment = $pofileid["file_id"];
                    $cmuf->save();
                }
            } catch (\Throwable $th) {
                $pofileid = 0;
            }

            try {

                $fileextension = $request->file("rfc_pricing_template")->getClientOriginalExtension();

                if ($request->get('rfc_document_attachment') < 1 && $fileextension != "") {
                    $rfcfileid = $this->attachmentRepository->saveAttachmentFile("contracts", $request, "rfc_pricing_template");

                    $cmuf = Cmuf::where('id', $savedcontractid)->first();
                    $cmuf->rfc_pricing_tamplate_attachment_id = $rfcfileid["file_id"];
                    $cmuf->save();
                }
            } catch (\Throwable $th) {
            }
        }
        echo json_encode($jsonarray);
    }

    public function attachfile($request)
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
                        'file' => $filecontent,
                        'extension' => strtolower($filecontent->getClientOriginalExtension()),
                    ],
                    [
                        'file' => 'required',
                        'extension' => 'required|in:doc,docx,pdf,xls,xlsx,ods,ppt,pptx',
                    ],
                    [
                        'file' => 'max:10000',
                    ]
                );


                if ($validator->fails()) {
                    $errormessage = [$upload_file => ["Accepts only doc,docx,pdf,xls,xlsx,ods,ppt,pptx"]];
                    return response()->json([
                        'success' => 'false',
                        'errors' => $errormessage,
                    ], 400);
                } else {
                    $file = $this->attachmentRepository->saveAttachmentFile("contracts", $request, $upload_file);
                    echo ($file["file_id"]);
                }
            } else if ($upload_file == "rfc_pricing_template") {

                $validator = Validator::make(
                    [
                        'file' => $filecontent,
                        'extension' => strtolower($filecontent->getClientOriginalExtension()),
                    ],
                    [
                        'file' => 'required',
                        'extension' => 'required|in:doc,docx,pdf,xls,xlsx,ods,ppt,pptx',
                    ]
                );

                if ($validator->fails()) {
                    $errormessage = [$upload_file => ["Accepts only doc,docx,pdf,xls,xlsx,ods,ppt,pptx"]];
                    return response()->json([
                        'success' => 'false',
                        'errors' => $errormessage,
                    ], 400);
                } else {
                    $file = $this->attachmentRepository->saveAttachmentFile("contracts", $request, $upload_file);
                    echo ($file["file_id"]);
                }
            } else if ($upload_file == "po_upload") {
                $validator = Validator::make(
                    [
                        'file' => $filecontent,
                        'extension' => strtolower($filecontent->getClientOriginalExtension()),
                    ],
                    [
                        'file' => 'required',
                        'extension' => 'required|in:doc,docx,pdf,xls,xlsx,ods,ppt,pptx',
                    ]
                );
                if ($validator->fails()) {
                    $errormessage = [$upload_file => ["Accepts only doc,docx,pdf,xls,xlsx,ods,ppt,pptx"]];
                    return response()->json([
                        'success' => 'false',
                        'errors' => $errormessage,
                    ], 400);
                } else {
                    $file = $this->attachmentRepository->saveAttachmentFile("contracts", $request, $upload_file);
                    $this->attachmentRepository->unsetFilePersistant($file["file_id"]);
                    echo ($file["file_id"]);
                }
            } else if ($request->get("upload_amend") == "amendments") {

                $validator = Validator::make(
                    [
                        'file' => $filecontent,
                        'extension' => strtolower($filecontent->getClientOriginalExtension()),
                    ],
                    [
                        'file' => 'required',
                        'extension' => 'required|in:doc,docx,pdf,xls,xlsx,ods,ppt,pptx',
                    ]
                );
                if ($validator->fails()) {
                    $errormessage = [$upload_file => ["Accepts only doc,docx,pdf,xls,xlsx,ods,ppt,pptx"]];
                    return response()->json([
                        'success' => 'false',
                        'errors' => $errormessage,
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
                'errors' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Save Holiday payment variables
     * @return void
     */
    public function saveholidayagreement($savedcontractid, $request)
    {
        $holidaylist = StatHolidays::all();
        //dd($holidaylist);
        foreach ($holidaylist as $holidayvalue) {
            # code...

            $holidayid = $holidayvalue->id;
            //$holidaydate = $holidayvalue["holiday"];
            $paymentstatusid = $request->get("holiday-payment-" . $holidayid);
            //dd($paymentstatusid);
            if ($paymentstatusid > 0) {
                $data = [$savedcontractid, $holidayid, $paymentstatusid];
                //ContractsHolidayPaymentAgreement::create($data);
                $contractsholidayagreement = new ContractsHolidayPaymentAgreement;
                $contractsholidayagreement->contract_id = $savedcontractid;
                $contractsholidayagreement->holiday_id = $holidayid;
                $contractsholidayagreement->paymentstatus_id = $paymentstatusid;
                $contractsholidayagreement->save();
            }
        }
    }

    /**
     * Get Lookups for CMUF forms creations
     * @return array
     */
    public function getLookups()
    {
        $lookups['businessSegmentLookup'] = $this->businesssegmentrepository->getAll();
        $lookups['lineofBusinessLookup'] = $this->lineofbusinessRepository->getAll();
        $lookups['customerLookup'] = Customer::select('id', 'client_name', 'project_number')
            ->where('active', 1)
            ->orderBy('client_name', 'asc')
            ->get();
        $lookups['reasonforsubmissionLookup'] = $this->reasonforsubmissionRepository->getReasonListLookup();
        $lookups['divisionlookuprepository'] = $this->divisionlookuprepository->getAll();
        $lookups['parentcustomerlookuprepository'] = $this->parentcustomerrepository->getParentList();
        $lookups['officeaddresslookuprepository'] = $this->officeaddressrepository->getLookupList();
        $lookups['positionlookuprepository'] = $this->positionlookuprepository->getAll();
        $lookups['userlookuprepository'] = $this->userrepository->getUserTableList(true);
        $lookups['areamanagerlookup'] = User::whereHas("roles", function ($q) {
            return $q->whereNotIn("name", ["admin", "super_admin"])
                ->whereHas("permissions", function ($qry) {
                    return $qry->where("name", "area_manager");
                });
        })->orderBy("first_name", "asc")->get();
        $lookups['billingratechangerepository'] = $this->contractbillingratechangerepository->showTotalListLookup();
        $lookups['holidayrepository'] = StatHolidays::all();
        $lookups['holidaypaymentallocationrepository'] = $this->holidaypaymentallocationrepository->getAll();
        $lookups['contractbillingcyclerepository'] = $this->contractbillingcyclerepository->getAll();
        $lookups['contractpaymentmethodrepository'] = $this->contractpaymentmethodrepository->getAll();
        $lookups['contractdeviceaccessrepository'] = $this->contractdeviceaccessrepository->getAll();
        $lookups['contractcellphoneproviderrepository'] = $this->contractcellphoneproviderrepository->getAll();
        $lookups['contractprovidertemplate'] = ContractWrittenTemplateParty::all();

        return $lookups;
    }

    /**
     *  Function to get all the Client records
     *
     *  @param empty
     *  @return  array
     *
     */
    public function getContractclientdetails($clientid)
    {
        $ClientList = $this->customerModel->select('id', 'project_number', 'client_name', 'master_customer', 'contact_person_name', 'contact_person_email_id', 'contact_person_phone', 'contact_person_phone_ext', 'contact_person_cell_phone', 'contact_person_position', 'requester_name', 'requester_position', 'requester_empno', 'address', 'city', 'postal_code', 'billing_address', 'geo_location_lat', 'geo_location_long', 'description', 'proj_open', 'arpurchase_order_no', 'arcust_type', 'stc', 'inquiry_date', 'duty_officer_id', 'industry_sector_lookup_id', 'region_lookup_id')
            ->where('id', '=', $clientid)->first();
        $areamanager = $ClientList->employeeLatestCustomerAreaManager;

        if (isset($ClientList->employeeLatestCustomerAreaManager)) {

            $regionalmanagerdetail = $areamanager->areaManager;
            $regionalmanageremployeedetail = $regionalmanagerdetail->employee;
            $data["regionalmanagerid"] = $areamanager->user_id;
            $data["area_manager_position_text"] = $regionalmanageremployeedetail->position_id;
            $data["regionalmanagerfirstname"] = $regionalmanagerdetail->first_name;
            $data["regionalmanagerlastname"] = $regionalmanagerdetail->last_name;
            $data["regionalmanagerpositionid"] = $regionalmanageremployeedetail->position_id;
            $data["regionalmanageremailid"] = $regionalmanagerdetail->email;
            $data["regionalmanagerphone"] = $regionalmanageremployeedetail->phone;
            $data["regionalmanagercell"] = $regionalmanageremployeedetail->cell_no;
        } else {
            $data["regionalmanagerid"] = "";
            $data["regionalmanagerfirstname"] = "";
            $data["regionalmanagerlastname"] = "";
            $data["regionalmanagerpositionid"] = "";
            $data["regionalmanageremailid"] = "";
            $data["regionalmanagerphone"] = "";
            $data["regionalmanagercell"] = "";
        }




        $data["contract_number"] = $ClientList->project_number;
        $data["client_name"] = $ClientList->client_name;
        $data["contact_emailaddress"] = $ClientList->contact_person_email_id;
        $data["contact_phoneno"] = $ClientList->contact_person_phone;
        $clientdetailarray = $ClientList->toArray();
        $data["clientarray"] = $ClientList->toArray();

        if ($ClientList->proj_open != null) {
            $data["projectopendate"] = $ClientList->proj_open;
        } else {
            $data["projectopendate"] = "";
        }



        $data["areamanager"] = ["areamanager" => $areamanager];
        return $data;
    }

    /**
     * Save CMUF Forms
     * @return void
     */
    public function save($formdata)
    {
        $data = ["contract_name" => $formdata["contract_name"], "contract_number" => $formdata["contract_number"], "submission_date" => $formdata["submissiondate"]];
        $data = array_merge($data, ["area_manager_id" => $formdata["area_manager_id"], "reason_for_submission" => $formdata["reason_for_submission"]]);
        $data = array_merge($data, ["business_segment" => $formdata["business_segment"], "line_of_business" => $formdata["line_of_business"], "multidivisioncontract" => $formdata["multidivisioncontract"], "lead_division" => $formdata["lead_division"], "master_entity" => $formdata["master_entity"], "parent_customer" => $formdata["parent_customer"]]);
        $data = array_merge($data, ["area_manager" => $formdata["area_manager"], "area_manager_position_text" => $formdata["area_manager_position_text"], "area_manager_email_address" => $formdata["area_manager_email_address"], "area_manager_office_number" => $formdata["area_manager_office_number"], "area_manager_cell_number" => $formdata["area_manager_cell_number"], "area_manager_fax_number" => $formdata["area_manager_fax_number"], "office_address" => $formdata["office_address"]]);
        $data = array_merge($data, ["sales_employee_id" => $formdata["sales_employee_id"], "sales_contact_job_title" => $formdata["sales_contact_job_title"], "sales_contact_emailaddress" => $formdata["sales_contact_emailaddress"], "sales_office_number" => $formdata["sales_contact_office_number"], "sales_cell_number" => $formdata["sales_contact_cell_number"], "sales_contact_faxno" => $formdata["sales_contact_faxno"], "sales_contact_division" => $formdata["sales_contact_division"], "sales_contact_office_address" => $formdata["sales_contact_office_address"]]);
        $data = array_merge($data, ["contract_startdate" => $formdata["contract_startdate"], "contract_length" => $formdata["contract_length"], "contract_enddate" => $formdata["contract_enddate"], "renewable_contract" => $formdata["renewable_contract"], "contract_length_renewal_years" => $formdata["contract_length_renewal_years"], "billing_ratechange" => $formdata["billing_ratechange"], "contract_annualincrease_allowed" => $formdata["contract_annualincrease_allowed"], "contractonourtemplate" => $formdata["contractonourtemplate"]]);
        $data = array_merge($data, ["rfc_pricing_tamplate_attachment_id" => $formdata["rfc_pricing_tamplate_attachment_id"], "total_annual_contract_billing" => $formdata["total_annual_contract_billing"], "total_annual_contract_wages_benifits" => $formdata["total_annual_contract_wages_benifits"], "total_annual_expected_contribution_margin" => $formdata["total_annual_expected_contribution_margin"], "total_hours_perweek" => $formdata["total_hours_perweek"], "average_billrate" => $formdata["average_billrate"], "average_wagerate" => $formdata["average_wagerate"], "average_markup" => $formdata["average_markup"]]);
        $data = array_merge($data, ["contract_billing_cycle" => $formdata["contract_billing_cycle"], "contract_payment_method" => $formdata["contract_payment_method"], "ponumber" => $formdata["ponumber"], "pocompanyname" => $formdata["pocompanyname"], "poattentionto" => $formdata["poattentionto"], "potitle" => $formdata["potitle"], "pocity" => $formdata["pocity"], "popostalcode" => $formdata["popostalcode"]]);
        $data = array_merge($data, ["termination_clause_client" => $formdata["termination_clause_client"], "terminationnoticeperiodclient" => $formdata["terminationnoticeperiodclient"], "termination_clause" => $formdata["termination_clause"], "terminationnoticeperiod" => $formdata["terminationnoticeperiod"]]);
        $data = array_merge($data, ["pophone" => $formdata["pophone"], "poemail" => $formdata["poemail"], "pocellno" => $formdata["pocellno"], "pofax" => $formdata["pofax"], "ponotes" => $formdata["ponotes"], "po_attachment" => $formdata["po_attachment"]]);
        $data = array_merge($data, ["supervisorassigned" => $formdata["supervisorassigned"], "supervisoremployeenumber" => $formdata["supervisoremployeenumber"], "employeename" => $formdata["employeename"], "viewtrainingperformance" => $formdata["viewtrainingperformance"], "employeecellphone" => $formdata["employeecellphone"]]);
        $data = array_merge($data, ["employeeemailaddress" => $formdata["employeeemailaddress"], "employeetelephone" => $formdata["employeetelephone"], "employeefaxno" => $formdata["employeefaxno"], "contractcellphoneprovider" => $formdata["contractcellphoneprovider"], "supervisortabletrequired" => $formdata["supervisortabletrequired"]]);
        $data = array_merge($data, ["supervisorcgluser" => $formdata["supervisorcgluser"], "supervisorpublictransportrequired" => $formdata["supervisorpublictransportrequired"], "direction_nearest_intersection" => $formdata["direction_nearest_intersection"], "department_at_site" => $formdata["department_at_site"], "delivery_hours" => $formdata["delivery_hours"]]);
        $data = array_merge($data, ["supervisorcanmailbesent" => $formdata["supervisorcanmailbesent"], "contractdeviceaccess" => $formdata["contractdeviceaccess"], "scopeofwork" => $formdata["scopeofwork"]]);
        $data = array_merge($data, ["contract_attachment_id" => $formdata["contract_attachment_id"], "pomailingaddress" => $formdata["pomailingaddress"]]);
        $data = array_merge($data, ["created_by" => Auth::user()->id]);

        //dd($data);
        return $this->cmuf->savecontract($data);
    }

    /**
     * Save cmuf client data
     * @return void
     */
    public function saveClientcontact($data)
    {
        return ClientContactInformation::insert($data);
    }

    /**
     * Get get contract data with contract id
     * @param int contract id
     * @return array
     */
    public function getContractdata($contractid)
    {

        $contractdata = Cmuf::where('id', $contractid)->first();
        $leaddivision = 0;
        $customer = Customer::withTrashed()->find($contractdata->contract_name)->first();
        $customername = $customer->project_number . "-" . $customer->client_name;

        //$reasonforsubmission = $this->reasonforsubmissionRepository->getSinglereasonforsubmission($contractdata->reason_for_submission);
        //$businesssegment = $this->businesssegmentrepository->getSingleBusinessreason($contractdata->business_segment);
        //$lineofbusiness = $this->lineofbusinessRepository->getSingleLineofbusiness($contractdata->line_of_business);
        //dd($contractdata->lead_division);
        if ($contractdata->lead_division != null) {

            //$leaddivision = $this->divisionlookuprepository->getSingleDivision($contractdata->lead_division);
            //$data["leaddivision"] = $leaddivision->division_name;
        } else {
            //$data["leaddivision"] = "";
        }

        //$contactsprimaryofficeaddress = $this->officeaddressrepository->getSingleofficeaddress($contractdata->office_address);
        $salesuserdetails = $this->userrepository->getUserDetails($contractdata->sales_employee_id);

        //$salespersondivision = $this->divisionlookuprepository->getSingleDivision($contractdata->sales_contact_division);
        //$salespersonofficeaddress = $this->officeaddressrepository->getSingleofficeaddress($contractdata->sales_contact_office_address);
        $data["contractclients"] = ClientContactInformation::where('contractid', $contractid)->get();
        $data["contractamendments"] = ContractsAmendment::where('contract_id', $contractid)->get();

        $data["contractholidayagreement"] = ContractsHolidayPaymentAgreement::withTrashed()->where('contract_id', $contractid)->get();

        $data["contractdata"] = $contractdata;
        $data["customername"] = $customername;
        //$data["reasonforsubmission"] = $reasonforsubmission->reason;
        //$data["businesssegment"] = $businesssegment->segmenttitle;
        //$data["lineofbusiness"] = $lineofbusiness->lineofbusinesstitle;
        //$data["salespersondivision"] = $salespersondivision->division_name;
        //$data["contactsprimaryofficeaddress"] = $contactsprimaryofficeaddress->address;
        $data["salesuser"] = $contractdata->getSalesuser->first_name . " " . $contractdata->getSalesuser->last_name;
        //$data["salespersonofficeaddress"] = $salespersonofficeaddress->address;

        return $data;
    }

    public function prePopulatedata($contractdata)
    {
        $prepopulateddata["Businesssegmentid"] = $contractdata->getBusinesssegment->id;
        $prepopulateddata["Businesslineid"] = $contractdata->getBusinessline->id;
        $prepopulateddata["multidivisioncontract"] = $contractdata->multidivisioncontract;
        if ($contractdata->multidivisioncontract > 0) {
            try {
                $prepopulateddata["LeadDivisionlookupid"] = $contractdata->getLeadDivisionlookup->id;
            } catch (\Throwable $th) {
                //throw $th;
            }
        } else {
            $prepopulateddata["LeadDivisionlookupid"] = 0;
        }
        $prepopulateddata["area_manager_id"] = $contractdata->area_manager_id;
        $prepopulateddata["area_manager_position_text"] = $contractdata->area_manager_position_text;
        $prepopulateddata["area_manager_email_address"] = $contractdata->area_manager_email_address;
        $prepopulateddata["area_manager_office_number"] = $contractdata->area_manager_office_number;
        $prepopulateddata["area_manager_cell_number"] = $contractdata->area_manager_cell_number;
        $prepopulateddata["area_manager_fax_number"] = $contractdata->area_manager_fax_number;
        $prepopulateddata["office_address"] = $contractdata->office_address;

        $prepopulateddata["sales_employee_id"] = $contractdata->sales_employee_id;
        $prepopulateddata["sales_contact_job_title"] = $contractdata->sales_contact_job_title;
        $prepopulateddata["sales_contact_emailaddress"] = $contractdata->sales_contact_emailaddress;
        $prepopulateddata["sales_office_number"] = $contractdata->sales_office_number;
        $prepopulateddata["sales_cell_number"] = $contractdata->sales_cell_number;
        $prepopulateddata["sales_contact_faxno"] = $contractdata->sales_contact_faxno;
        $prepopulateddata["sales_contact_division"] = $contractdata->sales_contact_division;
        $prepopulateddata["sales_contact_office_address"] = $contractdata->sales_contact_office_address;





        return $prepopulateddata;
    }

    public function handleCmuferror($savedcontractid)
    {
        Cmuf::find($savedcontractid)->delete();

        $clientcontact = ClientContactInformation::where('contractid', $savedcontractid);
        $clientcontact->delete();
    }

    public function editContractblocks($contractid, $dbvariable)
    {
        Cmuf::find($contractid)->update($dbvariable);
    }

    /**
     * Function to store and update Documents
     * @param  $request
     * @param  $module
     * @return array
     */
    public function store($request, $module, $filetype)
    {

        $filecontrol = "cmuf_contract_document";

        //dd($request->all());
        //echo $module;
        if ($filetype == "main") {
            $filecontrol = "cmuf_contract_document";

            $file = $this->attachmentRepository->saveAttachmentFile($module, $request, $filecontrol);
        } else if ($filetype == "po") {
            $filecontrol = "po_upload";
            $file = $this->attachmentRepository->saveAttachmentFile($module, $request, $filecontrol);
        } else if ($filetype == "rfc") {
            $filecontrol = "rfc_pricing_template";
            $file = $this->attachmentRepository->saveAttachmentFile($module, $request, $filecontrol);
        } else if ($filetype == "contracts_amendment") {
            $filecontrol = "contracts_amendment";
            $file = $this->attachmentRepository->saveAttachmentFile($module, $request, $filecontrol);
        } else if ($filetype == "contracts_amendment") {
            $filecontrol = "contracts_amendment";
            //$file = $this->attachmentRepository->saveAttachmentFile($module, $request, $filecontrol);
        }


        return $attachment_id = $file["file_id"];
    }

    /**
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */
    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.contracts_attachment') . "/" . date("Y-m-d"), null);
    }

    /**
     * Function to Download attachments
     * @param $file_id
     * @return array
     */
    public static function getPoAttachmentPathArrFromFile($file_id)
    {
        $attachment = Cmuf::where('po_attachment', $file_id)->first();
        if (isset($attachment)) {
            $document_type_id = $attachment->document_type_id;
        }
        return array(config('globals.contracts_attachment'), $document_type_id);
    }

    /**
     * Function to Download attachments
     * @param $file_id
     * @return array
     */
    public static function getRfcAttachmentPathArrFromFile($file_id)
    {
        $attachment = Cmuf::where('rfc_pricing_tamplate_attachment_id', $file_id)->first();
        if (isset($attachment)) {
            $document_type_id = $attachment->document_type_id;
        }
        return array(config('globals.contracts_attachment'), $document_type_id);
    }

    /**
     * Function to Download attachments
     * @param $file_id
     * @return array
     */
    public static function getAttachmentPathArrFromFile($file_id)
    {
        $attachment = Cmuf::where('contract_attachment_id', $file_id)->first();
        // $attachment = $this->attachmentRepository->down


        return array(config('globals.contracts_attachment'), null);
    }

    public function getFilteredContracts($request)
    {

        $customername = $request->get('customername');
        $regionalmanager = $request->get('regionalmanager');
        $contractenddate_from = $request->get('contractenddate_from');
        $contractenddate_end = $request->get('contractenddate_end');
        $billingvaluerange = $request->get('billingvaluerange');
        $contractbillingvalue = $request->get('contractbillingvalue');


        $contractsarray = [];
        $cmuflists = Cmuf::select('id', 'created_by', 'contract_name', 'contract_number', 'contract_startdate', 'contract_enddate', 'area_manager', 'employeename', 'total_annual_contract_billing', 'submission_date', 'area_manager_id')->orderBy('id', 'desc');
        if ($customername != 0) {
            $cmuflists = $cmuflists->where('contract_number', $customername);
        }

        if ($regionalmanager > 0) {
            $cmuflists = $cmuflists->where('area_manager_id', $regionalmanager);
        }

        if ($contractenddate_from != "" && $contractenddate_end == "") {
            $cmuflists = $cmuflists->where('contract_enddate', '>=', $contractenddate_from);
        }

        if ($contractenddate_from == "" && $contractenddate_end != "") {
            $cmuflists = $cmuflists->where('contract_enddate', '<=', $contractenddate_end);
        }

        if ($contractenddate_from != "" && $contractenddate_end != "") {
            $cmuflists = $cmuflists->whereBetween('contract_enddate', [$contractenddate_from, $contractenddate_end]);
        }

        if ($contractbillingvalue > 0) {
            //
            if ($billingvaluerange == "greaterthan") {
                $cmuflists = $cmuflists->where('total_annual_contract_billing', '>=', $contractbillingvalue);
            } else if ($billingvaluerange == "lessthan") {
                $cmuflists = $cmuflists->where('total_annual_contract_billing', '<=', $contractbillingvalue);
            }
        }

        $cmuflists = $cmuflists->get();
        $i = 0;
        foreach ($cmuflists as $cmuflist) {

            $id = $cmuflist->id;
            $contractname = $cmuflist->getContractname->client_name;
            $projectnumber = $cmuflist->getContractname->project_number;
            $submissiondate = date("M-d-Y", strtotime($cmuflist->submission_date));
            $created_by = $cmuflist->getPreparedby->first_name . " " . $cmuflist->getPreparedby->last_name;

            $contract_startdate = $cmuflist->contract_startdate;
            $contract_enddate = $cmuflist->contract_enddate;
            $area_manager = $cmuflist->area_manager;
            $employeename = $cmuflist->employeename;
            $total_annual_contract_billing = $cmuflist->total_annual_contract_billing;
            $contractsarray[$i]["customer_contract_number"] = strtoupper((substr($contractname, 0, 3)) . "Contract00" . $id);
            $contractsarray[$i]["contract_name"] = $contractname;
            $contractsarray[$i]["contract_number"] = $projectnumber;
            $contractsarray[$i]["submission_date"] = $submissiondate;

            $contractsarray[$i]["preparedby"] = $created_by;
            $contractsarray[$i]["contract_startdate"] = $contract_startdate;
            $contractsarray[$i]["contract_enddate"] = $contract_enddate;
            $contractsarray[$i]["regional_manager"] = $area_manager;
            $contractsarray[$i]["supervisor_name"] = $employeename;
            $contractsarray[$i]["billing_value"] = $total_annual_contract_billing;
            $contractsarray[$i]["billing_value_formatted"] = '$'.number_format($total_annual_contract_billing,2);


            $contractsarray[$i]["id"] = $id;
            $i++;
        }

        return $contractsarray;
    }

    public function getContracts()
    {
        $contractsarray = [];
        $cmuflists = Cmuf::select('id', 'created_by', 'contract_name', 'contract_number', 'contract_startdate', 'contract_enddate', 'area_manager', 'employeename', 'total_annual_contract_billing', 'submission_date', 'area_manager_id')->orderBy('id', 'desc')->get();

        $i = 0;
        foreach ($cmuflists as $cmuflist) {

            $id = $cmuflist->id;
            $contractname = $cmuflist->getContractname->client_name;
            $projectnumber = $cmuflist->getContractname->project_number;
            $submissiondate = date("M-d-Y", strtotime($cmuflist->submission_date));
            $created_by = $cmuflist->getPreparedby->first_name . " " . $cmuflist->getPreparedby->last_name;

            $contract_startdate = $cmuflist->contract_startdate;
            $contract_enddate = $cmuflist->contract_enddate;
            $area_manager = $cmuflist->area_manager;
            $employeename = $cmuflist->employeename;
            $total_annual_contract_billing = $cmuflist->total_annual_contract_billing;
            $contractsarray[$i]["customer_contract_number"] = strtoupper((substr($contractname, 0, 3)) . "Contract00" . $id);
            $contractsarray[$i]["contract_name"] = $contractname;
            $contractsarray[$i]["contract_number"] = $projectnumber;
            $contractsarray[$i]["submission_date"] = $submissiondate;

            $contractsarray[$i]["preparedby"] = $created_by;
            $contractsarray[$i]["contract_startdate"] = date("M d,Y", strtotime($contract_startdate));
            $contractsarray[$i]["contract_enddate"] = date("M d,Y", strtotime($contract_enddate));
            $contractsarray[$i]["contract_startdate_raw"] = strtotime($contract_enddate);
            $contractsarray[$i]["contract_enddate_raw"] = strtotime($contract_enddate);
            $contractsarray[$i]["regional_manager"] = $area_manager;
            if ($employeename != "0") {
                $contractsarray[$i]["supervisor_name"] = $employeename;
            } else {
                $contractsarray[$i]["supervisor_name"] = "";
            }

            $contractsarray[$i]["billing_value"] = $total_annual_contract_billing;
            $contractsarray[$i]["billing_value_formatted"] = '$'.number_format($total_annual_contract_billing,2);


            $contractsarray[$i]["id"] = $id;
            $i++;
        }

        return $contractsarray;
    }

    public function getContractsBetweenTwoDatesByCustomerId($clientId, $startDate)
    {
        return $this->cmuf
            ->where('contract_name', '=', $clientId)
            ->where(function ($query) use ($startDate) {
                $query->where('contract_enddate', '>=', $startDate);
            })->orderBy('contract_startdate', 'asc')->select('total_hours_perweek')->first();
    }

    public function getContractsafterdate($clientId, $startDate)
    {
        return $this->cmuf->with(["client_contact_information"])
            ->where('contract_name', '=', $clientId)
            ->where(function ($query) use ($startDate) {
                $query->where('contract_enddate', '>=', $startDate);
            })->orderBy('contract_startdate', 'asc')->first();
    }

    public function contractExpiryReminder($template, $expiryDate)
    {
        $now = Carbon::now()->toDateString();
        $contractDetails = $this->cmuf->with(['getContractname'])
            ->where('contract_enddate', '=', $expiryDate)
            ->get();
        if ($contractDetails != null) {
            foreach ($contractDetails as $key => $each_list) {
                $now = Carbon::now()->toDateString();
                $daysDiff = Carbon::parse($now)->diffInDays($each_list->contract_enddate, false);
                $helper_variables = array(
                    '{contractName}' =>  $each_list->getContractname->getCustomerNameAndNumberAttribute(),
                    '{expiryDate}' => $each_list->contract_enddate,
                    '{contractExpiryDueInDays}' =>  $daysDiff,
                );
                $this->mailQueueRepository->prepareMailTemplate(
                    $template,
                    $each_list->contract_name,
                    $helper_variables,
                    'Modules\Contracts\Models\Cmuf',
                    0
                );
            }
        }
    }
}
