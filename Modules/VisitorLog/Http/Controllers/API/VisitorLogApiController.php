<?php

namespace  Modules\VisitorLog\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\VisitorLog\Http\Resources\VisitorResource;
use Modules\VisitorLog\Http\Resources\ScreeningQuestionsResource;
//Models
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\VisitorLogTypeLookup;
use Modules\Admin\Models\VisitorLogCustomerTemplateAllocation;
use Modules\Client\Models\VisitorLogDetails;
//Repositories
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Client\Repositories\VisitorRepository;
use Modules\Client\Repositories\VisitorLogRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerTermsAndConditionRepository;
use Modules\Admin\Repositories\VisitorLogScreeningTemplateCustomerAllocationRepository;
use Modules\Client\Repositories\VisitorLogScreeningSubmissionRepository;
use Modules\Admin\Repositories\VisitorLogScreeningTemplateQuestionRepository;
use Modules\Admin\Repositories\VisitorLogTemplateRepository;
use Modules\Client\Models\VisitorLogMeta;

class VisitorLogApiController
{
    protected $customerRepo, $directory_seperator, $visitorLogScreeningTemplateQuestionRepo;
    protected $visitorRepo, $customerEmployeeAllocationRepo, $visitorLogRepo, $termsAndConditionRepository;
    protected $visitorLogTemplateRepo;

    public function __construct(
        CustomerRepository $customerRepo,
        VisitorRepository $visitorRepo,
        VisitorLogRepository $visitorLogRepo,
        VisitorLogTemplateRepository $visitorLogTemplateRepo,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepo,
        CustomerTermsAndConditionRepository $termsAndConditionRepository,
        VisitorLogScreeningTemplateCustomerAllocationRepository $visitorLogScreeningTemplateCustomerAllocationRepository,
        VisitorLogScreeningSubmissionRepository $visitorLogScreeningSubmissionRepository,
        VisitorLogScreeningTemplateQuestionRepository $visitorLogScreeningTemplateQuestionRepo
    ) {
        $this->customerRepo = $customerRepo;
        $this->visitorRepo = $visitorRepo;
        $this->visitorLogRepo = $visitorLogRepo;
        $this->customerEmployeeAllocationRepo = $customerEmployeeAllocationRepo;
        $this->visitorLogDetails = new VisitorLogDetails();
        $this->directory_seperator = "/";
        $this->termsAndConditionRepository = $termsAndConditionRepository;
        $this->visitorLogScreeningTemplateCustomerAllocationRepository = $visitorLogScreeningTemplateCustomerAllocationRepository;
        $this->visitorLogScreeningSubmissionRepository = $visitorLogScreeningSubmissionRepository;
        $this->visitorLogScreeningTemplateQuestionRepository = $visitorLogScreeningTemplateQuestionRepo;
        $this->visitorLogTemplateRepo = $visitorLogTemplateRepo;
    }

    public function storeVisitors(Request $request)
    {
        try {
            // \Log::channel('customlog')->info('Visitor store inputs'.json_encode($request->all()));
            $this->visitorRepo->store($request);
            $msg = 'Done';
            $status = 200;
        } catch (\Exception $e) {
            \Log::channel('customlog')->info('Visitor store inputs' . json_encode($request->all()) . ' -- ERROR =>' . $e->getMessage());
            $msg = $e->getMessage();
            $status = 400;
        }
        return response()->json(['message' => $msg],  $status);
    }

    public function fetchVisitors(Request $request)
    {
        $request->validate([
            'customerId' => 'required|integer'
        ]);

        $ts = Carbon::now()->toDateTimeString();

        $inputs = $request->all();

        $visitors = $this->visitorRepo->getByFilters($inputs);

        return response()->json([
            "data" => new VisitorResource($visitors),
            "ts" => $ts
        ]);
    }

    //TODO::remove later
    public function fetchVisitorsFallback(Request $request, $customerId)
    {
        $ts = Carbon::now()->format('Y-m-d H:i:s');

        $inputs = $request->all();
        $inputs['customerId'] = $customerId;
        $visitors = $this->visitorRepo->getByFilters($inputs);

        return response()->json([
            "data" => new VisitorResource($visitors),
            "ts" => $ts
        ]);
    }

    public function getPeerSyncVisitorLogs(Request $request)
    {
        $ts = Carbon::now()->toDateTimeString();

        $inputs = $request->all();
        $vlogs = $this->visitorLogRepo->getByFilters($inputs);

        $processedPayloads = [];

        foreach ($vlogs as $vl) {
            $processedPayloads[] = json_decode($vl->payload);
        }

        return response()->json([
            "data" => $processedPayloads,
            "ts" => $ts
        ]);
    }

    public function storeVisitorLogs(Request $request)
    {
        try {
            //\Log::channel('customlog')->info(json_encode($request->all()));
            // \Log::channel('customlog')->info('-------storeVisitorLogs -- '.'Customer --> '.$request->input('clientId').'screeningId --> '.$request->input('screeningId').' <-- Request '.json_encode($request->all()));
            $visitorLogs = [];
            $data = [];
            // checkInOption value will be 'Manual or Qr'.
            if ($request->has('checkInOption')) {

                if ($request->input('checkInOption') == 'Manual') {
                    if ($request->has('data')) {
                        $visitorLogs = json_decode($request->input('data'), true);
                    }
                } elseif ($request->input('checkInOption') == 'Qr' || $request->input('checkInOption') == 'Authorized Entrant') {
                    if ($request->has('visitorPayload')) {
                        $data = json_decode($request->input('visitorPayload'), true);
                        $visitorLogs['first_name'] = $data['firstName'] . ' ' . $data['lastName'];
                        $visitorLogs['email'] = $data['email'];
                        $visitorLogs['phone'] = $data['phone'];
                        $visitorLogs['uid'] = $data['uid'];
                    }
                } else {
                    \Log::channel('customlog')->info('----storeVisitorLogs -- checkInOption is ' . $request->input("checkInOption") . ' -- Request ' . json_encode($request->all()));
                }

                if (!empty($visitorLogs)) {

                    // $visitorLogs = $data;
                    //Fetching screening details
                    $visitorLogs['visitor_log_screening_submission_uid'] = $request->input('screeningId');
                    //Common inputs
                    $visitorLogs['check_in_option'] = $request->input('checkInOption');
                    $visitorLogs['customer_id'] = $request->input('clientId');
                    $visitorLogs['template_id'] = $request->input('templateId');
                    $visitorLogs['uuid'] = $request->input('uuid');
                    $visitorLogs['force_checkout'] = $request->input('forceCheckout');
                    $visitorLogs['checkin'] = \Carbon\Carbon::parse($request->input('checkInAt'))->format('Y-m-d H:i:s');
                    $visitorLogs['checkout'] = null;
                    if ($request->has('checkOutAt')) {
                        $visitorLogs['checkout'] = \Carbon\Carbon::parse($request->input('checkOutAt'))->format('Y-m-d H:i:s');
                    }

                    $visitorLogs['created_by'] = \Auth::user()->id;

                    $visitorLogs['visitor_type_id'] = $request->input('visitorTypeId');
                    if ($request->input('visitorTypeId') == 0) {
                        $typeId = VisitorLogTypeLookup::where('type', 'Employee')
                            ->select('id')
                            ->first();
                        $visitorLogs['visitor_type_id'] = $typeId->id;
                    }
                    $log = $this->visitorLogRepo->getByuuid($visitorLogs['uuid']);
                    $visitorLogs['payload'] = $this->visitorLogRepo->procesPayload($request->all());

                    if (empty($log)) {
                        $result = $this->visitorLogRepo->storeFromApp($visitorLogs);
                        //Store meta info
                        $customFields = $this->visitorLogTemplateRepo->getTemplateCustomFields($request->input('templateId'));
                        foreach ($customFields as $cf) {
                            if (array_key_exists($cf->fieldname, $visitorLogs)) {
                                VisitorLogMeta::create([
                                    'visitor_log_id' => $result->id,
                                    'key' => $cf->fieldname,
                                    'value' => $visitorLogs[$cf->fieldname]
                                ]);
                            }
                        }

                        $request->request->add(['visitor_log_id' => $result->id]);
                        if ($request->hasFile('image')) {
                            $request->request->add(['imagetype' => 'picture']);
                            $this->uploadImage($request->all());
                        }

                        if ($request->hasFile('signature')) {
                            $request->request->add(['imagetype' => 'signature']);
                            $this->uploadImage($request->all());
                        }
                    } else {
                        //Filter input array //TODO:check
                        // $visitorLogs = array_intersect_key($log->getFillable(), $visitorLogs);
                        $this->visitorLogRepo->update($log->id, $visitorLogs);
                    }


                    $msg = 'Done';
                    $status = 200;
                } else {
                    \Log::channel('customlog')->info('----storeVisitorLogs -- Missing check in user details -- Request ' . json_encode($request->all()));
                    $msg = 'Missing check in user details';
                    $status = 402;
                }
            } else {
                \Log::channel('customlog')->info('----storeVisitorLogs -- checkInOption not found on request. -- Request ' . json_encode($request->all()));
                $msg = 'Check in option not found';
                $status = 402;
            }
        } catch (\Exception $e) {
            \Log::channel('customlog')->info('-- storeVisitorLogs store ERROR =>' . $e->getMessage());
            $msg = $e->getMessage();
            $status = 400;
        }

        return response()->json(['message' => $msg],  $status);
    }

    /**
     * To upload a file
     *
     * @param [type] $data
     * @return void
     */

    public function uploadImage($request)
    {
        try {
            $filetxt = $request['imagetype'] == 'signature' ? 'visitor_signature_' : 'visitor_image_';

            switch ($request['imagetype']) {
                case 'signature':
                    $filetxt = 'visitor_signature_';
                    $fieldname = 'signature_file_name';
                    break;
                case 'picture':
                    $filetxt = 'visitor_image_';
                    $fieldname = 'picture_file_name';
                    break;
                case 'checkout_signature':
                    $filetxt = 'visitor_checkout_signature_';
                    $fieldname = 'checkout_file_name';
                    break;

                default:
                    $filetxt = 'image_';
                    break;
            }

            $filename = uniqid($filetxt);
            $path = public_path() . '/visitor_log';

            if (!file_exists($path . $this->directory_seperator . $request['visitor_log_id'])) {
                mkdir($path . $this->directory_seperator . $request['visitor_log_id'], 0777, true);
            }

            //profile pick
            if (isset($request['image']) && $request['imagetype'] == 'picture') {
                $image = $request['image'];
            }

            //signature
            if (isset($request['signature']) && $request['imagetype'] == 'signature') {
                $image = $request['signature'];
            }

            $destination = $path . $this->directory_seperator . $request['visitor_log_id'] . $this->directory_seperator . $filename . "." . $image->getClientOriginalExtension();
            $image->move($path . '/' . $request['visitor_log_id'], $destination);
            VisitorLogDetails::where('id', $request['visitor_log_id'])->update([$fieldname => $filename . "." . $image->getClientOriginalExtension()]);

            $msg = 'Done';
            $status = 200;
        } catch (\Exception $e) {
            \Log::channel('customlog')->info('-- storeVisitorLogs store ERROR =>' . $e->getMessage());
            $msg = $e->getMessage();
            $status = 400;
        }
        return response()->json(['message' => $msg],  $status);
    }

    /**
     * Get customer list based on allocation
     *
     * @param
     * @return object
     */

    public function getCustomers()
    {
        //Fetching allocated customerIds
        $customerIds = $this->customerEmployeeAllocationRepo->getAllocatedCustomers(\Auth::user());

        //Fetching Customers
        $customers = Customer::whereIn('id', $customerIds)
            ->whereNull('deleted_at')
            ->orderBy('client_name')
            ->select('id', 'project_number as projectNumber', 'client_name as name')
            ->get();

        return response()->json([
            "data" => $customers
        ]);
    }

    public function fetchCustomerTemplate($customer_id)
    {
        $customertemplateallocation = VisitorLogCustomerTemplateAllocation::where('customer_id', $customer_id)->get();
        $i = 0;
        $templatelistarray = [];

        foreach ($customertemplateallocation as $templates) {

            $templatelistarray[$i]["id"] = $templates->template->id;
            $templatelistarray[$i]["name"] = $templates->template->template_name;
            foreach ($templates->template_feature as $temp_feature) {

                // For Mandatory validation
                $reqval = false;
                if ($temp_feature->is_required == 1) {
                    $reqval = true;
                }

                // For Mandatory validation
                $is_visible = false;
                if ($temp_feature->is_visible    == 1) {
                    $is_visible = true;
                }

                if ($temp_feature->feature_name == "picture") {
                    $templatelistarray[$i]["reqImageCapture"] = $reqval;
                    $templatelistarray[$i]["enImageCapture"] = $is_visible;
                }
                if ($temp_feature->feature_name == "signature") {
                    $templatelistarray[$i]["reqSignature"] = $reqval;
                    $templatelistarray[$i]["enSignature"] = $is_visible;
                }
            }
            $fieldarray = [];
            $j = 0;
            foreach ($templates->visible_template_fields as $temp_fields) {

                // Validation
                $reqval = false;
                if ($temp_fields->is_required == 1) {
                    $reqval = true;
                }
                $templatelistarray[$i]["fields"][$j]["name"] = $temp_fields->fieldname;
                if ($temp_fields->fieldname == "first_name") {
                    $field_type = "text";
                } else if ($temp_fields->fieldname == "email") {
                    $field_type = "email";
                } else if ($temp_fields->fieldname == "phone") {
                    $field_type = "phone";
                } else if ($temp_fields->fieldname == "visitor_type_id") {
                    $field_type = "radio";
                } else if ($temp_fields->fieldname == "checkin") {
                    $field_type = "time";
                } else {
                    $field_type = "text";
                }
                $templatelistarray[$i]["fields"][$j]["type"] = $field_type;
                $templatelistarray[$i]["fields"][$j]["label"] = $temp_fields->field_displayname;
                $templatelistarray[$i]["fields"][$j]["mandatory"] = $reqval;
                $templatelistarray[$i]["fields"][$j]["pattern"] = "";
                $j++;
            }
            $i++;
        }
        return  $templatelistarray;
    }

    public function getCustomerTemplates($customer_id)
    {
        return response()->json([
            "data" => $this->fetchCustomerTemplate($customer_id)
        ]);
    }

    //Fetch directly from template and writ a resource for template
    public function getTemplate($customer_id, $template_id)
    {
        $template = null;
        $templates = $this->fetchCustomerTemplate($customer_id);
        foreach ($templates as $t) {
            if ($t["id"] == $template_id) {
                $template = $t;
            }
        }
        return response()->json([
            "data" => (object)$template
        ]);
    }

    public function getVisitorTypes()
    {
        $response = VisitorLogTypeLookup::orderBy('type')
            ->select('id', 'type as name')
            ->get();
        return response()->json([
            'data' => $response
        ]);
    }

    public function getTermsAndCondition($customer_id)
    {
        $response =  $this->termsAndConditionRepository->getByCustomerAndType(VISITOR_LOG_TYPE, $customer_id);
        if (empty($response)) {
            $response =  $this->termsAndConditionRepository->getDefaultByType(VISITOR_LOG_TYPE);
        }
        return response()->json(['data' => $response]);
    }

    public function fetchScreeningQuestions(Request $request, $customerId)
    {
        $questions = [];
        $questions['lastSynced'] = \Carbon::now()->format('Y-m-d H:i:s');
        $questions['screenEnabled'] = false;
        $questions['templateId'] = null;
        $questions['questions'] = [];

        $inputs = $request->all();
        $inputs['customerId'] = $customerId;
        $screeningTemplate = $this->visitorLogScreeningTemplateCustomerAllocationRepository->getTemplateByCustomerId($inputs);
        // dd($screeningTemplate->VisitorLogScreeningTemplate->VisitorLogScreeningTemplateQuestion);
        if (!empty($screeningTemplate)) {
            if (!empty($screeningTemplate->VisitorLogScreeningTemplate) && sizeof($screeningTemplate->VisitorLogScreeningTemplate->VisitorLogScreeningTemplateQuestion)) {
                $questions['screenEnabled'] = true;
                $questions['templateId'] = $screeningTemplate->visitor_log_screening_template_id;
                $questions['questions'] = new ScreeningQuestionsResource($screeningTemplate->VisitorLogScreeningTemplate->VisitorLogScreeningTemplateQuestion);
            }
        }
        return response()->json([
            "data" => $questions,
        ]);
    }

    public function storeScreeningQuestion(Request $request)
    {
        $insertedIds = [];
        try {
            \DB::beginTransaction();
            $payloads = $request->input('payload');

            foreach ($payloads as $payload) {
                $input = json_decode($payload, true);
                $isExists = $this->visitorLogScreeningSubmissionRepository->getByUID($input['uid']);
                if (sizeof($isExists) == 0) {
                    $input['screened_at'] = \Carbon\Carbon::parse($input['createdAt'])->format('Y-m-d H:i:s');
                    $input['customer_id'] = $input['customerId'];
                    $input['visitor_log_screening_template_id'] = $input['templateId'];
                    $id = $input['id'];
                    $questionAnswers = json_decode($input['results'], true);

                    unset($input['results']);
                    unset($input['createdAt']);
                    unset($input['customerId']);
                    unset($input['id']);

                    $submission = $this->visitorLogScreeningSubmissionRepository->save($input);

                    $questionAnswersInputs['visitor_log_screening_submission_id'] = $submission->id;
                    foreach ($questionAnswers as $questionAnswer) {
                        $que = $this->visitorLogScreeningTemplateQuestionRepository->getIncludingTrashed($questionAnswer['questionId']);
                        $questionAnswersInputs['visitor_log_screening_template_question_str'] = $que->question;
                        $questionAnswersInputs['visitor_log_screening_template_question_expected_answer'] = $que->answer;
                        $questionAnswersInputs['visitor_log_screening_template_question_id'] = $questionAnswer['questionId'];
                        $questionAnswersInputs['answer'] = $questionAnswer['answer'];
                        $questionAnswersInputs['created_at'] = $input['screened_at'];
                        $this->visitorLogScreeningSubmissionRepository->saveQuestionAnswers($questionAnswersInputs);
                    }
                    array_push($insertedIds, $id);
                }
            }
            \DB::commit();
            $msg = 'Success';
            $status = 200;
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::channel('customlog')->info('-- storeScreeningQuestion ERROR =>' . $e->getMessage());
            $msg = $e->getMessage();
            $status = 400;
            $insertedIds = [];
        }
        return response()->json(['message' => $msg, 'ids' => $insertedIds],  $status);
    }
}
