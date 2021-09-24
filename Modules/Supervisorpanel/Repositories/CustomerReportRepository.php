<?php

namespace Modules\Supervisorpanel\Repositories;

use Auth;
use Modules\Admin\Models\LeaveReason;
use Modules\Admin\Models\PayPeriod;
use Modules\Admin\Models\Template;
use Modules\Admin\Models\TemplateForm;
use Modules\Admin\Models\TemplateQuestionsCategory;
use Modules\Admin\Models\TemplateSettingRules;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Supervisorpanel\Models\CustomerPayperiodTemplate;
use Modules\Supervisorpanel\Models\CustomerReport;
use Modules\Supervisorpanel\Models\CustomerReportAdhoc;
use Modules\Supervisorpanel\Models\CustomerReportsAreaManagerNote;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportAdhocRepository;
use View;

class CustomerReportRepository
{

    private $index;
    private $employee_list_arr;
    private $leave_reason_arr;
    protected $user_repository;
    protected $customer_report_adhoc_repository;
    protected $customer_repository;
    protected $customer_reports_area_manager_note;
    protected $template_setting_rule;
    protected $customer_map_repository;
    protected $pay_period_repository;

    public function __construct(
        UserRepository $user_repository,
        CustomerReportsAreaManagerNote $customer_reports_area_manager_note,
        CustomerReportAdhocRepository $customer_report_adhoc_repository,
        CustomerRepository $customer_repository,
        TemplateSettingRules $template_setting_rule,
        CustomerMapRepository $customer_map_repository,
        PayPeriodRepository $pay_period_repository
    ) {
        $this->employee_list_arr = array();
        $this->leave_reason_arr = array();
        $this->user_repository = $user_repository;
        $this->customer_report_adhoc_repository = $customer_report_adhoc_repository;
        $this->customer_reports_area_manager_note = $customer_reports_area_manager_note;
        $this->customer_repository = $customer_repository;
        $this->template_setting_rule = $template_setting_rule;
        $this->customer_map_repository = $customer_map_repository;
        $this->pay_period_repository = $pay_period_repository;
    }

    /**
     * Get template by id
     *
     * @param integer id
     * @return object
     *
     */
    public function getTemplateById($id)
    {
        $template_by_id = Template::with('templateForm')->where('id', $id)->first();
        return $template_by_id;
    }

    /**
     * Get template by customer payperiod
     *
     * @param integer customer_id
     * @param integer payperiod_id
     * @return object
     *
     */
    public function getTemplateByCustomerPayperiod($customer_id, $payperiod_id)
    {
        $template_id = CustomerPayperiodTemplate::select('template_id')->where('payperiod_id', $payperiod_id)->where('customer_id', $customer_id)->first();
        if (!isset($template_id)) {
            return null;
        }
        $curr_template = $this->getTemplateById($template_id->template_id);
        return $curr_template;
    }

    /**
     * Get latest template
     *
     * @param empty
     * @return object
     *
     */
    public function getLatestTemplate()
    {
        $curr_template = Template::with('templateForm')->where('start_date', '<=', today())->orderBy('start_date', 'asc')->first();
        return $curr_template;
    }
    /**
     * Get current template
     *
     * @param empty
     * @return object
     *
     */
    public function getCurrentTemplate()
    {
        $curr_template = Template::with('templateForm')->where('start_date', '<=', today())->where('end_date', '>=', today())->first();
        return $curr_template;
    }

    /**
     * Get active template's categories
     *
     * @param empty
     * @return array
     *
     */
    public function getCurrentTemplateCategories()
    {
        $current_template = $this->getCurrentTemplate();
        if ($current_template) {
            $current_template_id = $current_template->id;
            $template_category = TemplateQuestionsCategory::with(['templateForm' => function ($query) use ($current_template_id) {
                $query->where('template_id', $current_template_id);
            }, 'templateForm.customerReport'])
                ->withTrashed()->get();
        } else {
            $template_category = TemplateQuestionsCategory::with(['templateForm' => function ($query) {
                //$query->where('template_id',$current_template_id);
            }, 'templateForm.customerReport'])
                ->withTrashed()->get();
        }
        $template_categories = $template_category->filter(function ($item) {
            // Filter deleted category that has no questions answered.
            // if the item is trashed and the category has any reports under it
            if ($item->trashed() && sizeof(data_get($item, 'templateForm.*.customerReport.*')) > 0 && sizeof(data_get($item, 'templateForm.*')) > 0) {
                return $item;
            } elseif (!$item->trashed()) {
                // if the category is not trashed
                return $item;
            }
        });
        $template_categories = $template_categories->values()->all();
        return $template_categories;
    }

    /**
     * Get Parent Questions of a active template's given category
     *
     *  @param integer question_category_id
     *  @return object
     *
     */
    public function getCurrentTemplateParentQuestions($question_category_id = null)
    {
        $template_query = Template::with(['templateForm' => function ($query) use ($question_category_id) {
            if (!empty($question_category_id)) {
                $query->where('question_category_id', $question_category_id);
            }
            $query->whereNull('parent_position');
        }])->where('start_date', '<=', today())->where('end_date', '>=', today());
        return $template_query->first();
    }
    /**
     *  Get all colors from the template setting rules
     *
     *  @param empty
     *  @return array
     */
    public function getColorOptions()
    {
        $template_settings = TemplateSettingRules::with('color')->get();
        $color_options = $template_settings->map(function ($item) {
            $option = '<option value="bar-color-' . $item->color->color_class_name . '">' . ucfirst($item->color->color_class_name) . '</option>';

            return $option;
        });
        return $color_options;
    }
    /**
     * Get Parent Question answers of a active template's given category
     *
     *  @param integer question_category_id
     *  @return object
     *
     */
    public function getCurrentTemplateParentQuestionsAnswers($question_category_id)
    {
        $template_query = $this->getCurrentTemplateParentQuestions($question_category_id);
        // Handling null case
        if (is_null($template_query)) {
            $dashboard_data['payperiod'] = null;
            $dashboard_data['filter_dropdown'] = null;
            $dashboard_data['questions'] = null;
            $dashboard_data['answers'] = null;
            return $dashboard_data;
        }
        $template_id = $template_query->id;
        $template_form_ids = [];
        foreach ($template_query->templateForm as $template_form) {
            $template_form_ids[] = $template_form['id'];
        }
        sort($template_form_ids);
        $question_count = count($template_form_ids);

        $color_options = $this->getColorOptions();
        $payperiods = PayPeriod::get()->map(function ($item) {

            return '<option value="' . $item->id . '">' . $item->pay_period_name . '</option>';
        });

        $customer_report = CustomerPayperiodTemplate::where('template_id', $template_id)
            ->with(['customerReport' => function ($query) use ($template_form_ids) {
                $query->whereIn('element_id', $template_form_ids);
            }])
            ->with(['customer', 'customerTrashed', 'payperiod'])
            ->get();

        $dashboard_answers = $customer_report->map(function ($item) use ($question_count) {
            $answer['project_number'] = $item->customerTrashed->project_number;
            $answer['client_name'] = $item->customerTrashed->client_name;
            if (!$item->customer) {
                $answer['area_manager'] = '';
                $answer['customer_id'] = '';
            } else {
                $client_managers = $this->customer_repository->getCustomerWithMangers($item->customerTrashed->id);
                $answer['area_manager'] = ($client_managers['areamanager']) ? $client_managers['areamanager']['full_name'] : '';
                $answer['customer_id'] = $item->customer->id;
            }
            $answer['payperiod'] = $item->payperiod_id;
            $i = 1;
            if ($item->customerReport->isEmpty()) {
                $color = 'empty';
                $answer['answer_' . $i++] = '<span class="dot bar-color-' . $color . '" ></span><p style="display:none;">dot bar-color-empty</p>';
            }

            foreach ($item->customerReport as $report) {
                $color = 'empty';
                if ($report['score'] !== null) {
                    $color_details = $this->template_setting_rule->where('min_value', '<=', $report['score'])
                        ->where('max_value', '>=', $report['score'])
                        ->with(['color'])
                        ->first();
                    $color = $color_details->color->color_class_name;
                    $color = ($color_details->color->color_class_name) ? $color_details->color->color_class_name : $color;
                }
                $answer['answer_' . $i++] = '<span class="dot bar-color-' . $color . '" ></span><p style="display:none;">dot bar-color-' . $color . '</p>'; //$report['answer'];
            }
            // If questions are added after a customer submits survey - scenario
            // Showing empty color answer for the additional questions
            if (($i - 1) != $question_count) {
                for ($j = $i; $j <= $question_count; $j++) {
                    $answer['answer_' . $j] = '<span class="dot bar-color-empty" ></span><p style="display:none;">dot bar-color-empty</p>';
                }
            }
            return $answer;
        });

        $dashboard_data['payperiod'] = $payperiods;
        $dashboard_data['filter_dropdown'] = $color_options;
        $dashboard_data['questions'] = $template_query;
        $dashboard_data['answers'] = $dashboard_answers;
        return $dashboard_data;
    }

    /**
     * Get template by payperiod id
     *
     * @param integer payperiod_id
     * @return object
     *
     */
    public function getTemplateByPayPeriod($payperiod_id)
    {
        $payperiodObj = PayPeriod::find($payperiod_id);
        $curr_template = Template::with('templateForm')->where('start_date', '<=', $payperiodObj->end_date)->where('end_date', '>=', $payperiodObj->start_date)->first();
        return $curr_template;
    }
    /**
     * Get format the template
     *
     * @param array payperiod_id
     * @param integer payperiod_id
     * @param integer customer_id
     * @param integer template_customer_payperiod_id
     * @param boolean only_questions
     *
     * @return object
     *
     */
    public function formatTemplate($current_template, $payperiod_id, $customer_id, $template_customer_payperiod_id = null, $only_questions = false)
    {
        $question_obj['template_id'] = $current_template['id'];
        $question_obj['template_customer_payperiod_id'] = $template_customer_payperiod_id;
        $question_obj['payperiod_id'] = $payperiod_id;
        $question_obj['customer_id'] = $customer_id;
        $question_obj['questions'] = array();
        $each_question_obj = array();
        $report_questions = $current_template['template_form'];
        $this->initialiseIndex();
        while ($this->index < count($report_questions)) {
            $question_category = $this->getQuestionCategory($report_questions[$this->index]['question_category_id']);
            $each_question_obj[$question_category][] = $this->prepareQuestionObject($report_questions, $template_customer_payperiod_id, 0, false, $only_questions);
            $this->incrementIndex();
        }
        $question_obj['questions'] = $each_question_obj;
        return $question_obj;
    }

    /**
     * Store customer template payperiod
     *
     * @param integer template_id
     * @param integer customer_id
     * @param integer payperiod_id
     * @param integer template_customer_payperiod_id
     *
     * @return object
     *
     */
    public function storeCustomerTemplatePayperiod($template_id, $customer_id, $payperiod_id, $template_customer_payperiod_id)
    {
        $customer_form['payperiod_id'] = $payperiod_id;
        $customer_form['customer_id'] = $customer_id;
        $customer_form['template_id'] = $template_id;
        if (empty($template_customer_payperiod_id)) {
            $customer_form['created_by'] = Auth::user()->id;
        }
        $customer_form['updated_by'] = Auth::user()->id;
        $obj_customer_payperiod = CustomerPayperiodTemplate::updateOrCreate(array('id' => $template_customer_payperiod_id), $customer_form);
        return $obj_customer_payperiod->id;
    }

    /**
     * Store customer report
     *
     * @param integer question
     * @param array request
     * @param integer customer_payperiod_id
     * @param integer leave_type
     *
     * @return object
     *
     */
    public function storeCustomerReport($question, $request, $customer_payperiod_id, $leave_type = false)
    {

        $curr_element = $request[$question['element_id']]; //name is same as element id
        $employee_leave = array();
        foreach ($curr_element as $key => $value) {
            // each element is given as array
            $score = $this->fetchScore($question, $value);
            if ($leave_type == true) {
                array_push($employee_leave, $value);
                if (sizeof($employee_leave) == 5) { //Here 5 is the count of fields in the leave type section.
                    $employee_leave = $this->customer_report_adhoc_repository->store($employee_leave, $request['payperiod_id'], $customer_payperiod_id);
                    $value = $employee_leave->id;
                    CustomerReport::create([
                        'customer_payperiod_template_id' => $customer_payperiod_id,
                        'element_id' => $question['element_id'],
                        'question' => $question['question'],
                        'answer' => $value,
                        'score' => $score,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                    $employee_leave = array();
                }
            } else {
                CustomerReport::create([
                    'customer_payperiod_template_id' => $customer_payperiod_id,
                    'element_id' => $question['element_id'],
                    'question' => $question['question'],
                    'answer' => $value,
                    'score' => $score,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);
            }
        }
        if (isset($question['children'])) {
            $children_count = count($question['children']);
            foreach ($question['children'] as $question_key => $question_child) {
                if ($question_child['answer_type'] == 4) { //Here 4 represent the answer type 'Leave Type'
                    $this->storeCustomerReport($question_child, $request, $customer_payperiod_id, true);
                } else {
                    $this->storeCustomerReport($question_child, $request, $customer_payperiod_id);
                }
            }
        }
    }

    /**
     * Delete customer report
     *
     * @param integer customer_payperiod_id
     *
     * @return empty
     *
     */

    public function deleteCustomerReport($customer_payperiod_id)
    {
        CustomerReport::where('customer_payperiod_template_id', $customer_payperiod_id)->delete();
    }

    /**
     * Delete employee leave
     *
     * @param integer customer_payperiod_id
     *
     * @return empty
     *
     */
    public function deleteEmployeeLeave($customer_payperiod_id)
    {
        $template_id = CustomerPayperiodTemplate::where('id', $customer_payperiod_id)->value('template_id');
        $position_ids = TemplateForm::where([['template_id', '=', $template_id], ['answer_type_id', '=', 4]])->pluck('position');
        $employee_leave_ids = CustomerReport::where('customer_payperiod_template_id', $customer_payperiod_id)->whereIn('element_id', $position_ids)->pluck('answer')->toArray();
        $employee_leave_delete = $this->customer_report_adhoc_repository->delete($employee_leave_ids);
    }

    /**
     * To check average
     *
     * @param integer question_category_id
     * @return boolean
     */

    public function isAverageChecked($question_category_id)
    {
        $average_checked_text = TemplateQuestionsCategory::withTrashed()->select('average')->find($question_category_id);
        if (strtolower($average_checked_text->average) == 'yes') {
            return true;
        } else {
            return false;
        }
    }

    /*     * ********************* Private functions *********************************************** */

    /**
     *  To initialise the index variable
     *  @param empty
     *
     *  @return empty
     */
    private function initialiseIndex()
    {
        $this->index = 0;
    }

    /**
     *  To increment the index variable
     *  @param empty
     *
     *  @return empty
     */
    private function incrementIndex()
    {
        $this->index = $this->index + 1;
    }

    /**
     * Function to prepare question object.
     * @param type $report_questions - Template questions full array
     * @param type $template_customer_payperiod_id (optional) - Corresponding template customer payperiod id if available
     * @param int $answer_index (optional) - Pass down index for recursion
     * @param type $child_question (optional) - If called for child, set are true. Default false
     * @param type $only_questions (optional) - If true no answer is rendered. Default false
     * @return type
     */
    private function prepareQuestionObject($report_questions, $template_customer_payperiod_id = null, $answer_index = 0, $child_question = false, $only_questions = false)
    {
        $each_question = $report_questions[$this->index];

        $question_obj_questions['template_pos'] = $each_question['position'];
        $question_obj_questions['element_id'] = $each_question['id'];
        $question_obj_questions['question'] = $each_question['question_text'];
        $question_obj_questions['question_category'] = $each_question['question_category_id'];
        $question_obj_questions['answer_type'] = $each_question['answer_type_id'];
        $question_obj_questions['multi_answer'] = $this->makeBoolean($each_question['multi_answer']);
        $question_obj_questions['show_if_yes'] = $this->makeBoolean($each_question['show_if_yes']);
        $question_obj_questions['score_yes'] = $each_question['score_yes'];
        $question_obj_questions['score_no'] = $each_question['score_no'];
        $question_obj_questions['parent_position'] = $each_question['parent_position'];
        $question_obj_questions['answer_html'] = (!$only_questions) ? $this->prepareAnswerHtml($each_question, $template_customer_payperiod_id, $answer_index) : "";
        $question_obj_questions['answer_index'] = $answer_index;
        if ($each_question['show_if_yes'] === null) {
            //no children
            $question_obj_questions['children'] = null;
        } else {
            // has children
            $answercount = 0;
            $answer_index = 0;
            if (isset($template_customer_payperiod_id)) {
                $answercount = CustomerReport::where('customer_payperiod_template_id', $template_customer_payperiod_id)->where('element_id', $report_questions[($this->index + 1)]['id'])->count();
            }
            $child_count = 0;
            do {
                $this->incrementIndex();
                if ($answer_index == 0) {
                    $child_count++;
                }
                //recursively add children
                $question_obj_questions['children'][] = $this->prepareQuestionObject($report_questions, $template_customer_payperiod_id, $answer_index, true);
                if ((!isset($report_questions[($this->index + 1)]) || $report_questions[($this->index + 1)]['parent_position'] == null)) {
                    if ($answercount > 1) {
                        $answer_index++;
                        if ($answer_index < $answercount) {
                            $this->index = $this->index - $child_count;
                            continue;
                        }
                    }
                    //if next element is not found
                    break;
                }
                //do until next element is a parent
            } while ($report_questions[($this->index + 1)]['parent_position'] != null);
        }
        if (!$child_question && !$only_questions) {
            $question_obj_questions['area_manager_notes'] = $this->prepareAreaManagerNotes($each_question['id'], $template_customer_payperiod_id);
        }
        return $question_obj_questions;
    }

    /**
     * Prepare areamanager notes
     *
     * @param integer element_id
     * @param integer template_customer_payperiod_id
     *
     * @return html
     *
     */
    private function prepareAreaManagerNotes($element_id, $template_customer_payperiod_id = null)
    {
        $answer = "";
        $datetime = "";
        if (isset($template_customer_payperiod_id)) {
            $note_obj = $this->customer_reports_area_manager_note->fetchSingleAreaManagerNote($template_customer_payperiod_id, $element_id);
        }
        if (isset($note_obj)) {
            $answer = $note_obj->notes;
            $datetime = isset($note_obj->created_at) ? $note_obj->created_at->toDayDateTimeString() : "";
        }
        $blade_parameters = array('name' => 'am_' . $element_id, 'answer' => $answer, 'datetime' => $datetime, 'can_write' => $this->getCanEditAreaManagerNotes());
        $rendered_html = View::make('supervisorpanel::partials.areamanager-comments')->with($blade_parameters)->render();
        return $rendered_html;
    }
    /**
     * Get question category
     *
     * @param integer category id
     *
     * @return string
     *
     */

    private function getQuestionCategory($id)
    {
        $questionCategoryObj = TemplateQuestionsCategory::withTrashed()->select('description')->where('id', $id)->get();
        $questionCategory_arr = $questionCategoryObj->toArray();
        return $questionCategory_arr[0]['description'];
    }

    /**
     *
     * @param type $element
     * @param type $null returns null if the value is null and $null is set. If $null not set, returns false if $element is not set
     * @return type
     */
    private function makeBoolean($element, $null = false)
    {
        return (isset($element)) ? (($element == 1) ? true : false) : (($null) ? null : false);
    }

    /**
     * Prepare answer html
     *
     * @param array each_question
     * @param integer template_customer_payperiod_id
     * @param integer answer_index
     *
     * @return html
     *
     */
    private function prepareAnswerHtml($each_question, $template_customer_payperiod_id = null, $answer_index = 0)
    {
        $blade_template['name'] = $this->getAnswerTemplate($each_question);
        $blade_template['parameters'] = $this->getAnswerParementers($each_question, $blade_template['name'], $template_customer_payperiod_id, $answer_index);
        $rendered_html = View::make('supervisorpanel::partials.' . $blade_template['name'])->with($blade_template['parameters'])->render();
        return $rendered_html;
    }

    /**
     * Get answer template
     *
     * @param array each_question
     *
     * @return string
     *
     */
    private function getAnswerTemplate($each_question)
    {
        $blade_template = '';
        $answer_type = $each_question['answer_type_id'];
        switch ($answer_type) {
            case 1:
                $blade_template = 'radiobutton';
                break;
            case 2:
                $blade_template = 'text';
                break;
            case 3:
                $blade_template = 'employeelist';
                break;
            case 4:
                $blade_template = 'leavetype';
                break;
        }
        return $blade_template;
    }

    /**
     * Get score
     *
     * @param array each_question
     *
     * @return string
     *
     */

    private function getScore($each_question)
    {
        $score = "";
        if (!isset($each_question['show_if_yes'])) {
            $score = "null";
        } elseif ($this->makeBoolean($each_question['show_if_yes'])) {
            $score = $each_question['score_yes'];
        } else {
            $score = $each_question['score_no'];
        }
        return (string) $score;
    }
    /**
     * Get answer parameters
     *
     * @param array each_question
     * @param string blade_name
     * @param integer template_customer_payperiod_id
     * @param integer answer_index
     *
     * @return array
     *
     */

    private function getAnswerParementers($each_question, $blade_name, $template_customer_payperiod_id = null, $answer_index = 0)
    {
        $blade_parameters = '';
        $completed = false;
        $question_text = $each_question['question_text'];
        $answer_type = $each_question['answer_type_id'];
        $element_name = $each_question['id'] . '[]';
        $current_role = \Auth::user()->roles[0]->name;
        $user = auth()->user();

        if (($answer_type == 3 && empty($this->employee_list_arr))
            || ($answer_type == 4
                && empty($this->employee_list_arr)
                && empty($this->leave_reason_arr))
        ) {
            //employeelist
            // if ($user->hasAnyPermission(['super_admin', 'admin'])) {
            //     $employee_arr = $this->user_repository->getUserLookup(['area_manager', 'guard', 'supervisor'], ['admin', 'super_admin']);
            // } else {
            //     $employee_arr = $this->user_repository->getUserList(
            //         null,
            //         null,
            //         $user->id
            //     )->pluck('name_with_emp_no', 'id');
            // }
            $employee_arr = [];
            $this->employee_list_arr = $employee_arr;
            $this->leave_reason_arr = LeaveReason::orderBy('reason')->pluck('reason', 'id')->toArray();
        }

        $selected_employee_arr = array();
        $answer = null;
        $employee_id = null;
        $date = null;
        $hours_off = null;
        $reason_id = null;
        $notes = null;

        if (isset($template_customer_payperiod_id)) {
            $completed = true;
            $reportValueObj = CustomerReport::where('customer_payperiod_template_id', $template_customer_payperiod_id)->where('element_id', $each_question['id'])->get();
            if (count($reportValueObj) > 0) {
                $answer = $reportValueObj[($answer_index)]->answer;
                if ($answer_type == 3) { //Here 3 represent the answer type 'Employee List'
                    $employee_id = $reportValueObj[($answer_index)]->answer;
                    if (!$user->hasAnyPermission(['super_admin', 'admin'])) { //Listing employees already chosen by admin or other user
                        $this->employee_list_arr = $this->getChosenAllocatedEmployees($employee_id, $this->employee_list_arr);
                    }
                }
                if ($answer_type == 4) { //Here 4 represent the answer type 'Leave Type'
                    $answer = CustomerReportAdhoc::where('id', $answer)->get();
                    $employee_id = $answer[0]->employee_id;
                    $date = $answer[0]->date;
                    $hours_off = $answer[0]->hours_off;
                    $reason_id = $answer[0]->reason_id;
                    $notes = $answer[0]->notes;
                    if (!$user->hasAnyPermission(['super_admin', 'admin'])) { //Listing employees already chosen by admin or other user
                        $this->employee_list_arr = $this->getChosenAllocatedEmployees($employee_id, $this->employee_list_arr);
                    }
                }
            } else {
                $answer = "";
                $employee_id = "";
                $date = "";
                $hours_off = "";
                $reason_id = "";
                $notes = "";
            }
        }
        switch ($answer_type) {
                //radio
            case 1:
                $score = $this->getScore($each_question);
                $blade_parameters = array(
                    'name' => $element_name,
                    'show_if_yes' => $this->makeBoolean($each_question['show_if_yes'], true),
                    'question_text' => $question_text,
                    'score' => $score,
                    'answer' => $answer,
                    'completed' => $completed,
                );
                break;
                //text
            case 2:
                $blade_parameters = array(
                    'name' => $element_name,
                    'question_text' => $question_text,
                    'answer' => $answer,
                    'completed' => $completed,
                );
                break;
                //employeelist
            case 3:
                $blade_parameters = array(
                    'name' => $element_name,
                    'employee_list' => $this->employee_list_arr,
                    'question_text' => $question_text,
                    'answer' => $answer,
                    'completed' => $completed,
                );
                break;
                //leavetype
            case 4:
                $blade_parameters = array(
                    'name' => $element_name,
                    'employee_list' => $this->employee_list_arr,
                    'leave_reason' => $this->leave_reason_arr,
                    'question_text' => $question_text,
                    'employee_id' => $employee_id,
                    'date' => $date,
                    'hours_off' => $hours_off,
                    'reason_id' => $reason_id,
                    'notes' => $notes,
                    'completed' => $completed,
                );
                break;
        }
        return $blade_parameters;
    }

    /**
     * Function to get chosen and allocated employees list
     * @param  $employee_id       [Chosen employee]
     * @param  $employee_list_arr [Allocated Employee list of logged in user]
     * @return array              [Array of allocated and chosen employee]
     */
    public function getChosenAllocatedEmployees($employee_id, $employee_list_arr)
    {
        $selected_employee = array();
        array_push($selected_employee, $employee_id);
        $selected_employee_array_filter = array_filter($selected_employee);
        $employee_lookup_obj = User::whereIn('id', $selected_employee_array_filter)->with('employee')->get();
        $selected_employee_arr = $employee_lookup_obj->pluck('name_with_emp_no', 'id');
        return $employee_list_arr->union($selected_employee_arr);
    }
    /**
     *  To return submit survey permission
     *  @param empty
     *  @return  boolean
     */

    public function getCanWriteSurvey()
    {
        //admin/supervisor
        return \Auth::user()->can('submit-survey');
    }
    /**
     *  To return edit area manager notes permission
     *  @param empty
     *  @return  boolean
     */
    public function getCanEditAreaManagerNotes()
    {
        //admin/area manager
        return \Auth::user()->can('edit-area-manager-notes');
    }

    /**
     *  To return view area manager notes permission
     *  @param empty
     *  @return  boolean
     */
    public function getCanViewAreaMangerNotes()
    {
        //admin/area manager/COO
        return \Auth::user()->can('view-area-manager-notes');
    }

    /**
     *  To fetch score
     *  @param array question
     *  @param string value
     *
     *  @return  integer
     */
    private function fetchScore($question, $value)
    {
        $default_score = null;
        $score = $default_score;
        $question_category = $question['question_category'];
        if (!$this->isAverageChecked($question_category)) {
            $score = $default_score;
        } elseif (isset($value)) {
            if (strtolower($value) == "yes") {
                $score = ($question['score_yes']) ?? $default_score;
            } elseif (strtolower($value) == "no") {
                $score = ($question['score_no']) ?? $default_score;
            }
        } else {
            $score = $default_score;
        }
        return $score;
    }

    /**
     * @param $customer_id
     * @param $payperiod_start - Start date of payperiod
     * @param $payperiod_end - End date of payperiod
     * @return array
     */
    public function customerPayperiodTrendReport($customer_id, $payperiod_start, $payperiod_end)
    {
        $pay_periods = array();
        $current_report = null;
        $report_keys = null;
        $trendForClientArr = array();
        $current_payperiod = $this->pay_period_repository->getCurrentPayperiod();
        if ($current_payperiod) {
            $pay_periods[] = $current_payperiod->id;
            $current_report = $this->customer_map_repository->getPayperiodAvgReport($customer_id, $pay_periods);
        }
        $pay_periods = $this->pay_period_repository->getPayperiodRange($payperiod_start, $payperiod_end);
        $average_report = $this->customer_map_repository->getPayperiodAvgReport($customer_id, $pay_periods);
        $trendchart = array();
        if (empty(!$average_report)) {
            foreach ($pay_periods as $eachperiod) {
                $pay_periods = array();
                $payperiod_name = $this->pay_period_repository->getShortPayperiodName($eachperiod);
                $payperiod_start = $this->pay_period_repository->getPayperiodStart($eachperiod);
                $pay_periods[] = $eachperiod;
                $trend_report = $this->customer_map_repository->getPayperiodAvgReport($customer_id, $pay_periods);
                if ($trend_report && empty(!$trend_report['score'])) {
                    $scoreVal = number_format((float) $trend_report['score']['total'], 2);
                    $trendchart[$payperiod_name->short_name] = $scoreVal;
                    $trendForClient['date'] = $payperiod_start->start_date;
                    $trendForClient['value'] = (float) $scoreVal;
                } else {
                    $trendForClient['date'] = $payperiod_start->start_date;
                    $trendForClient['value'] = 0;
                }
                array_push($trendForClientArr, $trendForClient);
            }

            if (isset($current_report) && empty(!$current_report['score'])) {
                $report_keys = array_intersect(array_keys($current_report['score']), array_keys($average_report['score']));
            } else {
                $report_keys = array_keys($average_report['score']);
            }
        }
        return array(
            'current_report' => $current_report,
            'average_report' => $average_report,
            'trendchart' => $trendchart,
            'report_keys' => $report_keys,
            'trend_client' => $trendForClientArr,
        );
    }

    /**
     * @param $date
     * @return array [id,payperiod_id,customer_id,template_id,created_at]
     */

    public function getCustomerDetails($inputs)
    {

        $customerPayperiodTemplateIds = CustomerReport::groupBy('customer_payperiod_template_id')
            ->when(isset($inputs['date']), function ($query) use ($inputs) {
                return $query->whereDate('updated_at', $inputs['date']);
            })
            ->when(isset($inputs['start_date']), function ($query) use ($inputs) {
                return $query->whereDate('updated_at', '>=', $inputs['start_date']);
            })
            ->when(isset($inputs['end_date']), function ($query) use ($inputs) {
                return $query->whereDate('updated_at', '<=', $inputs['end_date']);
            })
            ->pluck('customer_payperiod_template_id');

        return CustomerPayperiodTemplate::whereIn('id', $customerPayperiodTemplateIds)
            ->select("id", "payperiod_id", "customer_id", "template_id", "created_at")
            ->with('payperiodTrashed')
            ->get();
    }
}
