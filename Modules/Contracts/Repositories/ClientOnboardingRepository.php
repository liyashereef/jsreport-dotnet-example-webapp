<?php

namespace Modules\Contracts\Repositories;

use App\Models\Attachment;
use App\Repositories\MailQueueRepository;
use App\Services\HelperService;
use Carbon\Carbon;
use Dompdf\Exception;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\PostOrderGroup;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AttachmentRepository;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\ClientOnboardingSettingRepository;
use Modules\Contracts\Models\ClientOnboarding;
use Modules\Contracts\Models\ClientOnboardingSection;
use Modules\Contracts\Models\ClientOnboardingStep;
use Modules\Contracts\Models\ClientOnboardingStepLog;
use Modules\Contracts\Models\PostOrder;

class ClientOnboardingRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $sectionModel;
    protected $stepModel;
    protected $stepLogModel;
    protected $attachment_repository;
    protected $mailQueueRepository;
    protected $settingRepository;
    protected $helperService;

    /**
     * Create a new PostOrderRepository instance.
     *
     * @param ClientOnboarding $clientOnboarding
     * @param ClientOnboardingSection $clientOnboardingSection
     * @param ClientOnboardingStep $clientOnboardingStep
     * @param ClientOnboardingStepLog $clientOnboardingStepLog
     * @param ClientOnboardingSettingRepository $clientOnboardingSettingRepository
     * @param MailQueueRepository $mailQueueRepository
     * @param HelperService $helperService
     */
    public function __construct(
        ClientOnboarding $clientOnboarding,
        ClientOnboardingSection $clientOnboardingSection,
        ClientOnboardingStep $clientOnboardingStep,
        ClientOnboardingStepLog $clientOnboardingStepLog,
        ClientOnboardingSettingRepository $clientOnboardingSettingRepository,
        MailQueueRepository $mailQueueRepository,
        HelperService $helperService
    )
    {
        $this->model = $clientOnboarding;
        $this->sectionModel = $clientOnboardingSection;
        $this->stepModel = $clientOnboardingStep;
        $this->stepLogModel = $clientOnboardingStepLog;
        $this->helperService = $helperService;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->settingRepository = $clientOnboardingSettingRepository;
    }


    /**
     * Get list
     *
     * @param empty
     * @return array
     */
//    public function getList()
//    {
//        return $this->model->orderby('group','asc')->pluck( 'group','id')->toArray();
//    }

    /**
     * Get list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model
            ->with('section', 'section.step')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Display details of single
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with('section', 'section.step', 'createdby', 'section.step.assignedTo')->find($id);
    }

    /**
     * Display details of single
     *
     * @param $rfpDetailsId
     * @return object
     */
    public function getByRfpId($rfpDetailsId)
    {
        $test = $this->model
            ->with('section', 'createdby')
            ->where('rfp_details_id', $rfpDetailsId)
            ->with(['section.step' => function ($q) {
                if (\Auth::user()->hasPermissionTo('view_all_client_onboarding_steps')) {
                    return $q;
                } else {
                    $q->where('assigned_to', \Auth::user()->id);
                }
            }]);
        return $test;
    }

    /**
     * Store a newly created in storage.
     *
     * @param  $data
     * @return object
     */
//    public function save($data)
//    {
//        if(!isset($data['id'])){
//            $data['created_by'] = Auth::user()->id;
//        }
//        $data['updated_by'] = Auth::user()->id;
//        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
//    }

    /**
     * Remove from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.post_order_attachment_folder'));
    }

    /**
     * Function handle save of incident report along with attachments
     * @param $request
     * @return bool
     */
    public function storeClientOnboarding()
    {
        $section_id = array();
        $inputData = request()->all();
        $data['id'] = request('id') ?? null;
        $data['rfp_details_id'] = request('rfpDetailsId');
        $rfp_id = $data['rfp_details_id'];

        if (!isset($data['id'])) {
            $data['created_by'] = Auth::user()->id;
        }
        $data['updated_by'] = Auth::user()->id;
        $clientOnboardingId = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        $inputData['client-onboarding-id'] = $clientOnboardingId->id;
        

        // Section //
        //-- delete --//
        $existingSectionArr = $this->getSectionByTemplate($clientOnboardingId->id)->pluck('id')->toArray();
        $newSectionArr = $inputData['client-section-id'];
        $sectionDelete = (array_diff($existingSectionArr, $newSectionArr));
        if (sizeof($sectionDelete) > 0) {
            $this->deleteSection($sectionDelete);
        }
        //-- add --//
        $sectionArr = $inputData['section'];
        foreach ($sectionArr as $key => $section) {
            $sectionId = $inputData['client-section-id'][$key];
            if (!isset($sectionId)) {
                $sectionData['created_by'] = Auth::user()->id;
            }
            $sectionData['updated_by'] = Auth::user()->id;

            $sectionData['id'] = $inputData['client-section-id'][$key];
            $sectionData['client_onboarding_id'] = $clientOnboardingId->id;
            $sectionData['section'] = $section;
            $sectionData['sort_order'] = $inputData['sort'][$key];
            $sectionData['updated_by'] = $data['updated_by'];
            $section_id[$key] = $this->sectionModel->updateOrCreate(array('id' => $sectionData['id']), $sectionData)->id;
            if (!isset($sectionData['id'])) {
                $inputData['client-section-id'][$key] = $section_id[$key];
            }
        }

        //Step
        //-- delete --//
        $existingStepArr = $this->getStepListBySection($section_id)->pluck('id')->toArray();
        $newStepArr = data_get($inputData['client-step-id'], '*.*');
        $stepsDelete = (array_diff($existingStepArr, $newStepArr));
        if (sizeof($stepsDelete) > 0) {
            $this->deleteStep($stepsDelete);
        }
        //-- add --//
        $stepAddSectionIndex = 0;
        foreach ($inputData['client-step'] as $sectionKey => $stepArr) {
            foreach ($stepArr as $stepKey => $step) {
                $stepId = $inputData['client-step-id'][$sectionKey][$stepKey];
                $stepData['section_id'] = $section_id[$stepAddSectionIndex];
                $stepData['step'] = $step;
                $stepData['assigned_to'] = $inputData['client-step-assignee'][$sectionKey][$stepKey];
                $stepData['target_date'] = Carbon::createFromFormat('Y-m-d', $inputData['client-step-target-date'][$sectionKey][$stepKey])->toDateString();
                $stepData['sort_order'] = $inputData['client-step-sort-order'][$sectionKey][$stepKey];

                if (!isset($stepId)) {
                    $stepData['created_by'] = Auth::user()->id;
                }
                $stepData['updated_by'] = Auth::user()->id;
                $stepColl = $this->stepModel->updateOrCreate(array('id' => $stepId), $stepData);
                if (!isset($stepId)) {
                    $inputData['client-step-id'][$sectionKey][$stepKey] = $stepColl->id;
                    $this->sendAssignedNotification($stepData, $rfp_id, $clientOnboardingId->id);
                }
            }
            $stepAddSectionIndex++;
        }
        return $this->updatePercentageCompleted($inputData);
    }

    public function validatePercentageData($inputData)
    {
        // Validate input step Id
        $requestStepId = data_get($inputData, 'client-step-id.*.*');
        $stepIdArr = data_get($this->sectionModel
            ->where('client_onboarding_id', $inputData['client-onboarding-id'])
            ->with('step:id,section_id')->get(), "*.step.*.id");

        if (sizeof(array_diff($requestStepId, $stepIdArr)) > 0) {
            throw new Exception("Invalid data");
        }
    }

    public function changeStatus($request)
    {
        return $this->model->where('id', $request->id)->update(['reviewed_status' => $request->status, 'reviewed_by' => \Auth::user()->id, 'reviewed_at' => \Carbon::now()]);
    }

    public function updatePercentageCompleted($inputData = null)
    {
        $updateStepPercent = true;

        if (!isset($inputData)) {
            $inputData = Input::all();
        } else {
            $updateStepPercent = false;
        }

        if ($updateStepPercent) {
            $this->validatePercentageData($inputData);
        }

        $stepLogArr = array();
        $stepLog['onboarding_id'] = $inputData['client-onboarding-id'];
        $stepAddSectionIndex = 0;
        foreach ($inputData['client-step-id'] as $sectionKey => $stepArr) {
            $stepLog['section_id'] = $inputData['client-section-id'][$stepAddSectionIndex];
            $stepLog['section'] = $this->sectionModel->find($stepLog['section_id'])->section;
            foreach ($stepArr as $stepKey => $step) {
                $stepId = $inputData['client-step-id'][$sectionKey][$stepKey];
                if ($updateStepPercent) {
                    $stepData['percentage_completed'] = isset($inputData['client-step-percentage']) ? $inputData['client-step-percentage'][$sectionKey][$stepKey] : 0;
                }
                $stepData['updated_by'] = Auth::user()->id;
                $stepColl = $this->stepModel->updateOrCreate(array('id' => $stepId), $stepData);

                $stepDetails = $this->stepModel->find($stepId);
                $stepLog['step_id'] = $stepDetails->id;
                $stepLog['step'] = $stepDetails->step;
                $stepLog['step_percentage_completed'] = $stepDetails->percentage_completed;
                $stepLog['target_date'] = $stepDetails->target_date;
                $stepLog['assigned_to'] = $stepDetails->assigned_to;
                $stepLog['created_by'] = Auth::user()->id;
                $this->stepLogModel->create($stepLog)->save();
                //array_push($stepLogArr,$stepLog);
            }
            $stepAddSectionIndex++;
        }

        foreach ($inputData['client-section-id'] as $sectionKey => $sectionId) {
            $sectionPercentCol = $this->stepModel
                ->select(\DB::raw("AVG(percentage_completed) as section_percent"))
                ->where('section_id', $sectionId)->first();
            $this->sectionModel->where('id', $sectionId)
                ->update(["percentage_completed" => $sectionPercentCol->section_percent]);
        }

        $onBoardingPercentCol = $this->sectionModel
            ->select(\DB::raw("AVG(percentage_completed) as percentage_completed"))
            ->where('client_onboarding_id', $inputData['client-onboarding-id'])->first();
        $this->model
            ->where('id', $inputData['client-onboarding-id'])
            ->update(["percentage_completed" => $onBoardingPercentCol->percentage_completed]);

        return response()->json($this->helperService->returnTrueResponse());
    }

    /**
     * Get Step list be section
     *
     * @param empty
     * @return array
     */
    public function getStepListBySection($section_id)
    {
        \DB::enableQueryLog();
        $stepColl = $this->stepModel;
        if (is_array($section_id)) {
            $stepColl = $stepColl->whereIn('section_id', $section_id);
        } else {
            $stepColl = $stepColl->where('section_id', $section_id);
        }
        $stepColl->orderby('sort_order', 'asc')->get();
        return $stepColl;
    }

    /**
     * Get Section
     *
     * @param empty
     * @return array
     */
    public function getSection($section_id)
    {
        return $this->sectionModel->where('section_id', $section_id)->orderby('sort_order', 'asc')->first();
    }

    /**
     * Get Section list by template
     *
     * @param empty
     * @return array
     */
    public function getSectionByTemplate($clientOnboardingTemplateId)
    {
        return $this->sectionModel
            ->where('client_onboarding_id', $clientOnboardingTemplateId)
            ->orderby('sort_order', 'asc')->get();
    }

    public function getSectionWithTask($id = null)
    {
        $idArr = is_array($id) || $id == null ? $id : [$id];
        $sectionWithStep = $this->sectionModel->with('step')->orderby('sort_order', 'asc');
        if (isset($idArr)) {
            $sectionWithStep = $sectionWithStep->whereIn('id', $idArr);
        }
        $returnVal = is_array($id) || $id == null ? $sectionWithStep->get() : $sectionWithStep->first();
        return $returnVal;
    }

    public function getSectionWithTaskByOnboarding($client_onboarding_id = null)
    {
        $sectionWithStep = $this->sectionModel->with('step')->orderby('sort_order', 'asc');
        $sectionWithStep->where('client_onboarding_id', $client_onboarding_id);
        $returnVal = $sectionWithStep->get();
        return $returnVal;
    }

    /**
     * Remove.
     *
     * @param  $id
     * @return object
     */
    public function deleteStep($id)
    {
        return $this->stepModel->destroy($id);
    }

    /**
     * Remove.
     *
     * @param  $id
     * @return object
     */
    public function deleteSection($id)
    {
        return $this->sectionModel->destroy($id);
    }

    public function sendAssignedNotification($stepData, $rfpId, $clientOnboadingId)
    {
        $receiver = User::find($stepData['assigned_to']);
        $helper_variable = array(
            '{receiverFullName}' => HelperService::sanitizeInput($receiver->full_name),
            '{receiverEmployeeNumber}' => HelperService::sanitizeInput($receiver->employee->employee_no),
            '{trackingURL}' => route('rfp.track-client-onboarding', $rfpId),
            '{step}' => HelperService::sanitizeInput($stepData['step']),
            '{targetDate}' => Carbon::createFromFormat('Y-m-d', $stepData['target_date'])
                ->format('l, F j Y')
        );
        $emailResult = $this->mailQueueRepository
            ->prepareMailTemplate("client_onboarding_task_assigned",
                null, $helper_variable,
                "Modules\Contracts\Models\ClientOnboarding",
                0,
                $receiver->id);
        return $emailResult;
    }


    /**
     * Send Notifictaion  mail
     *
     * @param empty
     * @return array
     */
    public function sendNotification()
    {
        $onboardingList = $this->settingRepository->getAllByType('mail');
        if(sizeof($onboardingList) > 0) {
            $onboardingReminders = "(" . implode(',', data_get($onboardingList, '*.value')) . ")";
            $pendingTask = $this->stepModel
                ->with(['section.clientOnboarding.rfp:id'])
                ->where('percentage_completed', '<', 100)
                ->whereRaw('DATEDIFF(target_date, "' . date("Y-m-d") . '") in '. $onboardingReminders)
                ->get();
            foreach ($pendingTask as $eachTask) {
                $receiver = User::find($eachTask->assigned_to);
                $helper_variable = array(
                    '{receiverFullName}' => HelperService::sanitizeInput($receiver->full_name),
                    '{receiverEmployeeNumber}' => HelperService::sanitizeInput($receiver->employee->employee_no),
                    '{trackingURL}' => route('rfp.track-client-onboarding', $eachTask->section->clientOnboarding->rfp->id),
                    '{step}' => HelperService::sanitizeInput($eachTask->step),
                    '{targetDate}' => Carbon::createFromFormat('Y-m-d', $eachTask->target_date)
                        ->format('l, F j Y'),
                );
                $emailResult = $this->mailQueueRepository
                    ->prepareMailTemplate("client_onboarding_status_due_reminder",
                        null, $helper_variable,
                        "Modules\Contracts\Models\ClientOnboarding",
                        0,
                        $receiver->id);
            }
        }
        return true;
    }

}
