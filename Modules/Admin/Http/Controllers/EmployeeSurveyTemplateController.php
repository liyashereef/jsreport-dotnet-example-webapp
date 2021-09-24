<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\EmployeeSurveyTemplateRequest;
use Modules\Admin\Repositories\EmployeeSurveyTemplateRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\RolesAndPermissionRepository;
use Modules\Hranalytics\Models\EmployeeSurveyTemplate;
use Modules\Hranalytics\Models\EmployeeSurveyQuestion;
use Modules\Hranalytics\Models\EmployeeSurveyEntry;
use Modules\Hranalytics\Models\EmployeeSurveyCustomerAllocation;
use Modules\Hranalytics\Models\EmployeeSurveyRoleAllocation;
use DB;
use Auth;

class EmployeeSurveyTemplateController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CandidateExperienceLookupRepository $candidateExperienceLookupRepository
     * @return void
     */
    public function __construct(
        EmployeeSurveyTemplateRepository $employeeSurveyTemplateRepository,
        HelperService $helperService,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        RolesAndPermissionRepository $rolesAndPermissionRepository
    ) {
        $this->repository = $employeeSurveyTemplateRepository;
        $this->helperService = $helperService;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->rolesAndPermissionRepository = $rolesAndPermissionRepository;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entries = EmployeeSurveyEntry::pluck('survey_id')->toArray();
        return view('admin::employee-survey.employee-survey-template', compact('entries'));
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CandidateExperienceRequest $request
     * @return json
     */
    public function store(EmployeeSurveyTemplateRequest $request)
    {
        try {
            DB::beginTransaction();
            //$id = $request->get('id');
            $template_data['survey_name'] = $request->get('template_name');
            $template_data['start_date'] = $request->get('start_date');
            $template_data['customer_based'] = !empty($request->get('customer_id')) && (!in_array(0, $request->get('customer_id'))) ? 1 : 0;
            $template_data['role_based'] = !empty($request->get('role_id')) && (!in_array(0, $request->get('role_id'))) ? 1 : 0;
            $template_data['expiry_date'] = $request->get('end_date');
            $template_data['active'] = true;
            $template_data['created_by'] = Auth::user()->id;
            $obj_template = EmployeeSurveyTemplate::updateOrCreate(array('id' => $request->get('id')), $template_data);
            $template_id = $obj_template->id;
            $question_text_arr = $request->get('question_text');
            $customer_arr = $request->get('customer_id');
            EmployeeSurveyCustomerAllocation::where(['survey_id' => $request->get('id')])->delete();
            if (!empty($customer_arr) && (!in_array(0, $customer_arr))) {
                foreach ($customer_arr as $key => $each_customer) {
                    $templateFrom = EmployeeSurveyCustomerAllocation::create(
                        [
                            'survey_id' => $template_id,
                            'customer_id' =>  $each_customer,
                            'created_by' => Auth::user()->id,
                        ]
                    );
                }
            }
            $role_arr = $request->get('role_id');
            EmployeeSurveyRoleAllocation::where(['survey_id' => $request->get('id')])->delete();
            if (!empty($role_arr) && (!in_array(0, $role_arr))) {
                foreach ($role_arr as $key => $each_role) {
                    $templateFrom = EmployeeSurveyRoleAllocation::create(
                        [
                            'survey_id' => $template_id,
                            'role_id' => $each_role,
                            'created_by' => Auth::user()->id,
                        ]
                    );
                }
            }
            $survey_quest = EmployeeSurveyQuestion::where(['survey_id' => $request->get('id')])->pluck('id')->toArray();
            $diff_arr = array_diff($survey_quest, $request->question_id);
            foreach ($diff_arr as $key => $questId) {
                EmployeeSurveyQuestion::where('id', $questId)->delete();
                unset($request->request->answer_type[$key]);
                unset($request->request->sequence[$key]);
                unset($request->request->question_text[$key]);
            }
            for ($i = 0; $i < count($request->get('question_text')); $i++) {
                $questionset_arr = [
                    'survey_id' => $template_id,
                    'question' => $request->question_text[$i],
                    'answer_type' => $request->answer_type[$i],
                    'sequence' => $request->sequence[$i],
                    'created_by' => Auth::user()->id,
                ];
                EmployeeSurveyQuestion::updateOrCreate(array('id' => $request->question_id[$i]), $questionset_arr);
            }
            //0 in $customer_arr Indicating all customers
            if (in_array(0, $customer_arr)) {
                $customer_arr = $this->customerEmployeeAllocationRepository->getCustomersList()->pluck('id')->toArray();
            }
            //0 in $role_Arr Indicating all roles
            if (in_array(0, $role_arr)) {
                $role_arr = $this->rolesAndPermissionRepository->getRoleList()->pluck('id')->toArray();
            }
            $notification = $this->repository->triggerPushNotification($customer_arr, $role_arr, $template_id);
            DB::commit();
            return response()->json(array('success' => 'true'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Controller for GET method of add and edit of template
     *  - Edit fetch the form content and populate select values
     *  - Add populates select values
     * @param int $template_id template id for edit
     */
    public function addTemplate($template_id = null, $is_view = null)
    {
        if (isset($template_id)) {
            $customer_list = $this->customerEmployeeAllocationRepository->getAllCustomersList();
            $roles = $this->rolesAndPermissionRepository->getRoleList();
            $template_obj = EmployeeSurveyTemplate::with('templateForm')->where('id', $template_id)->get();
            $customer_arr = EmployeeSurveyCustomerAllocation::where('survey_id', $template_id)->pluck('customer_id')->toArray();
            $role_arr = EmployeeSurveyRoleAllocation::where('survey_id', $template_id)->pluck('role_id')->toArray();
            $template_arr = $template_obj->toArray()[0];
            $template_form_arr = $template_arr['template_form'];
            return view('admin::employee-survey.add', compact('customer_arr', 'answer_type', 'template_arr', 'template_form_arr', 'role_arr', 'last_template_position', 'customer_list', 'roles', 'is_view'));
        } else {
            $customer_list = $this->customerEmployeeAllocationRepository->getAllCustomersList();
            $roles = $this->rolesAndPermissionRepository->getRoleList();
            return view('admin::employee-survey.add', compact('customer_list', 'roles', 'is_view'));
        }
    }
}
