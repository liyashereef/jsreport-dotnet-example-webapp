<?php

namespace Modules\Supervisorpanel\Http\Controllers;

use App\Services\HelperService;
use Auth;
use Carbon\Carbon;
use Charts;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller;
use Mail;
use Modules\Admin\Models\Color;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\Admin\Models\LeaveReason;
use Modules\Admin\Models\PayPeriod;
use Modules\Admin\Models\ShiftModule;
use Modules\Admin\Models\Template;
use Modules\Admin\Models\TemplateSetting;
use Modules\Admin\Models\TemplateSettingRules;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\IndustrySectorLookupRepository;
use Modules\Admin\Repositories\LandingPageRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\RegionLookupRepository;
use Modules\Admin\Repositories\SiteNoteStatusLookupRepository;
use Modules\Admin\Repositories\TemplateSettingRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Supervisorpanel\Http\Requests\CustomerRatingRequest;
use Modules\Supervisorpanel\Http\Requests\SiteNotesRequest;
use Modules\SupervisorPanel\Mail\SendSurveySubmitNotification;
use Modules\Supervisorpanel\Models\CustomerPayperiodTemplate;
use Modules\Supervisorpanel\Models\CustomerReport;
use Modules\Supervisorpanel\Models\SiteNote;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;
use Modules\Supervisorpanel\Repositories\CustomerRatingRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportAreamanagerNotesRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportEmailSchedulerRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;
use Modules\Supervisorpanel\Repositories\GuardTourRepository;
use Modules\Supervisorpanel\Repositories\ShiftJournalRepository;
use Modules\Supervisorpanel\Repositories\SiteNoteRepository;
use Modules\Supervisorpanel\Repositories\SiteNoteTaskRepository;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;
use Modules\Admin\Models\TemplateQuestionsCategory;
use View;

class SupervisorPanelController extends Controller
{

    protected $customer_report_repository;
    protected $customer_map_repository;
    protected $customer_employee_allocation_repository;
    protected $pay_period_repository;
    protected $customer_rating_repository;
    protected $area_manager_repository;
    protected $guard_tour_repository;
    protected $employee_rating_lookups;
    protected $user_repository;
    protected $customer_model;
    protected $industrySectorLookupRepository;
    protected $regionLookupRepository;
    protected $color_model;
    protected $customerEmployeeAllocationModel;
    protected $shift_journal_repository;
    protected $employee_allocation_repository;
    protected $site_note_repository;
    protected $site_note_task_repository;
    protected $site_note_status_repository;
    public $site_notes_dateformat;
    private $average_score_arr;
    private $score_class;
    protected $Customer_Report_EmailScheduler_Repository;
    protected $template_setting_repository;
    protected $employee_shift_repository, $landingPageRepository;

    protected $customer_pay_period_template_repository;

    public function __construct(
        CustomerReportRepository $customer_report_repository,
        CustomerMapRepository $customer_map_repository,
        CustomerEmployeeAllocationRepository $customer_employee_allocation_repository,
        CustomerRepository $customer_repository,
        PayPeriodRepository $pay_period_repository,
        CustomerRatingRepository $customer_rating_repository,
        CustomerReportAreamanagerNotesRepository $area_manager_repository,
        GuardTourRepository $guard_tour_repository,
        EmployeeRatingLookup $employee_rating_lookups,
        UserRepository $user_repository,
        Customer $customer_model,
        IndustrySectorLookupRepository $industrySectorLookupRepository,
        RegionLookupRepository $regionLookupRepository,
        Color $color_model,
        CustomerEmployeeAllocation $customerEmployeeAllocationModel,
        ShiftJournalRepository $shift_journal_repository,
        EmployeeAllocationRepository $employee_allocation_repository,
        SiteNoteRepository $site_note_repository,
        CustomerReportEmailSchedulerRepository $Customer_Report_EmailScheduler_Repository,
        SiteNoteTaskRepository $site_note_task_repository,
        SiteNoteStatusLookupRepository $site_note_status_repository,
        TemplateSettingRepository $template_setting_repository,
        EmployeeShiftRepository $employee_shift_repository,
        LandingPageRepository $landingPageRepository,
        User $usermodel
    ) {
        $this->customer_report_repository = $customer_report_repository;
        $this->customer_map_repository = $customer_map_repository;
        $this->customer_employee_allocation_repository = $customer_employee_allocation_repository;
        $this->customer_repository = $customer_repository;
        $this->pay_period_repository = $pay_period_repository;
        $this->customer_rating_repository = $customer_rating_repository;
        $this->area_manager_repository = $area_manager_repository;
        $this->customer_rating_repository = $customer_rating_repository;
        $this->guard_tour_repository = $guard_tour_repository;
        $this->employee_rating_lookups = $employee_rating_lookups;
        $this->user_repository = $user_repository;
        $this->customer_model = $customer_model;
        $this->industrySectorLookupRepository = $industrySectorLookupRepository;
        $this->regionLookupRepository = $regionLookupRepository;
        $this->color_model = $color_model;
        $this->customerEmployeeAllocationModel = $customerEmployeeAllocationModel;
        $this->shift_journal_repository = $shift_journal_repository;
        $this->employee_allocation_repository = $employee_allocation_repository;
        $this->site_note_repository = $site_note_repository;
        $this->site_note_task_repository = $site_note_task_repository;
        $this->site_note_status_repository = $site_note_status_repository;
        $this->site_notes_dateformat = 'm/d/Y';
        $this->Customer_Report_EmailScheduler_Repository = $Customer_Report_EmailScheduler_Repository;
        $this->average_score_arr = null;
        $this->score_class = null;
        $this->template_setting_repository = $template_setting_repository;
        $this->employee_shift_repository = $employee_shift_repository;
        $this->landingPageRepository = $landingPageRepository;
        $this->usermodel = $usermodel;
        $this->helperService = new HelperService();
    }

    public function setAverageScoreArr($average_score_arr)
    {
        $this->average_score_arr = $average_score_arr;
    }

    public function getAverageScoreArr()
    {
        return $this->average_score_arr;
    }

    public function setScoreClass($score_class)
    {
        $this->score_class = $score_class;
    }

    public function getScoreClass()
    {
        return $this->score_class;
    }

    /**
     * Display map markers and customer details
     * @return type
     */
    public function index($stc = null, Request $request)
    {
        $shift_customerid = $request->get('shift_customerid');
        $employee_list = $this->employee_shift_repository->shiftEntryDetails($request->get('shift_customerid'));
        $today_shift_details = $this->employee_shift_repository->dailyShiftDetails($request->get('shift_customerid'));
        $filter_values = $this->getFilterDropdowns('supervisorpanel', $stc);
        $project_number = $filter_values['project_number'];
        $city = $filter_values['city'];
        $status = $filter_values['status'];
        $industry_sector = $filter_values['industry_sector'];
        $region = $filter_values['region'];
        $area_manager = $filter_values['area_manager'];
        $supervisor = $filter_values['supervisor'];
        $form_route = route('customers.mapping');
        $sort = ($request->get('sort_param') != null) ? $request->get('sort_param') : "actual";
        $latest_template = $this->customer_report_repository->getLatestTemplate();
        $customers_arr = $this->customer_map_repository->getCustomerMapDetails($latest_template, $stc, $request);

        /** START ** Get Customer Ids from Session and Filter */
        $customer_ids = $this->helperService->getCustomerIds();
        if (!empty($customer_ids)) {
            $customers_arr_filer = array();
            foreach ($customers_arr['customer_score'] as $customer) {
                if (in_array($customer['customer']['details']['id'], $customer_ids)) {
                    array_push($customers_arr_filer, $customer);
                }
            }
            $customers_arr['customer_score'] = $customers_arr_filer;
        }
        /** END ** Get Customer Ids from Session and Filter */

        $shift_flag = 0;
        $checkbox_after_filtering = $request->filtering;
        $colors = TemplateSettingRules::select('min_value', 'max_value', 'color_id')
            ->with(array('color' => function ($query) {
                $query->select('id', 'color_class_name');
            }))->get();
        //dd($colors);
        if (count($customers_arr['customer_score']) > 0) {
            $customer_score = $customers_arr['customer_score'];
            $customer_rating = $customers_arr['customer_rating'];
            $customer_rating_color = $customers_arr['customer_rating_color'];
            return view('supervisorpanel::customers-in-map', compact('shift_customerid', 'employee_list', 'customer_score', 'customer_rating', 'customer_rating_color', 'shift_flag', 'project_number', 'region', 'industry_sector', 'city', 'area_manager', 'supervisor', 'status', 'request', 'form_route', 'stc', 'sort', 'checkbox_after_filtering', 'colors', 'today_shift_details'));
        } else {
            return view('supervisorpanel::customers-in-map', compact('project_number', 'region', 'industry_sector', 'city', 'area_manager', 'supervisor', 'status', 'request', 'form_route', 'stc', 'sort'));
        }
    }

    /* Start function for giving background color to the average value*/
    // public function getColorByAverage($avg_score = null)
    // {
    //     $color = '';
    //     if ($avg_score != null) {
    //         $avg_score_color = $this->template_setting_repository->getAvgColor($avg_score);
    //         if (!empty($avg_score_color)) {
    //             if (isset($avg_score_color->color)) {
    //                 $color = $avg_score_color->color->color_name;
    //             }
    //         }
    //     }
    //     return $color;
    // }
    /*end of function for giving background color to the average value*/
    /**
     * Fetch the details of customer
     *
     * @param [type] $id
     * @param Request $request
     * @return void
     */
    public function customerDetails($id, $payperiod_id = null, $analytics = null, Request $request)
    {
        $current_payperiod_page = 1;
        $incident_load = false;
        $analytics_load = false;
        $customer = null;
        if ($this->customer_employee_allocation_repository->getAllocatedCustomers(Auth::user())) {
            $customer = $this->customer_repository->getCustomerWithMangers($id);

            $customer_id = $customer['details']['id'];
        }
        if ($payperiod_id == null && $analytics == null) {
            $payperioddateabovedatecount = $this->pay_period_repository->getPayperiodcountabovedate();
            if ($payperioddateabovedatecount > 0) {
            } else {
                $current_payperiod = null;
            }
            $current_payperiod = $this->pay_period_repository->getCurrentPayperiodrevision();
        } else if ($analytics != null) {
            $analytics_load = true;
            $current_payperiod = PayPeriod::find($payperiod_id);
            $incident_load = $payperiod_id;
        } else {
            $current_payperiod = PayPeriod::find($payperiod_id);
            $incident_load = $payperiod_id;
        }
        $payperiods_lists = $this->pay_period_repository->getLastNPayperiodWithCurrent()->pluck('id')->toArray();
        $payperiod_update_limit = TemplateSetting::first()->pluck('last_update_limit');
        $get_current_payperiod = $this->pay_period_repository->getCurrentPayperiod();
        $allowed_payperiods = $this->pay_period_repository->getLastNPayperiod($payperiod_update_limit[0])->pluck('id');
        $allowed_payperiods_arr = $allowed_payperiods->toArray();
        if (isset($get_current_payperiod->id)) {
            array_push($allowed_payperiods_arr, $get_current_payperiod->id);
        }
        $no_of_payperiods = config('globals.customer_detail_payperiod_paginations');
        foreach (array_chunk($payperiods_lists, $no_of_payperiods) as $id => $payperiods_list) {
            foreach ($payperiods_list as $payperiod) {
                if (isset($current_payperiod->id) && $payperiod == $current_payperiod->id) {
                    $current_payperiod_page = $id + 1;
                } else if (!isset($current_payperiod->id)) {
                    $current_payperiod_page = $id;
                }
            }
        }

        $currentPage = $current_payperiod_page;
        $currentPage = ($request->get('page')) ?? $currentPage;
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $chosenpayperiod = PayPeriod::find($current_payperiod->id);

        $payperiodenddate = $chosenpayperiod->end_date;
        $lessthanenddate = $payperiodenddate > today();

        $payperiods = PayPeriod::whereActive(true)
            ->where('start_date', '<=', Carbon::today())
            ->orderBy('start_date', 'desc')
            ->simplePaginate(config('globals.customer_detail_payperiod_paginations'));
        if ($customer === null) {
            return redirect()->route('customers.mapping');
        }

        $current_template_id = Template::select('id')->where('start_date', '<=', today())->where('end_date', '>=', today())->where('active', true)->pluck('id')->first();
        $customers = $this->customer_repository->getCustomerMap([$customer['details']['id']], $current_template_id);
        $last_report_key = count($customers->first()->customerPayperiodTemplate) - 1;
        $last_report_payperiod = ($customers->first()->customerPayperiodTemplate[$last_report_key]->payperiod_id) ?? null;

        if (isset($customers->first()->customerPayperiodTemplate[$last_report_key])) {
            $last_updated = $customers->first()->customerPayperiodTemplate[$last_report_key]->customerReport->first()->updated_at;
        }

        $payperiod_array = array();
        array_push($payperiod_array, $current_payperiod->id);
        $current_report = $this->customer_map_repository->getPayperiodAvgReport($customer_id, $payperiod_array);
        if (!empty($current_report)) {
            $average_score_arr = (!empty($current_report['score'])) ? $current_report['score'] : array('total' => 0);
            $score_class = (!empty($current_report['color_class'])) ? $current_report['color_class'] : array('total' => 'gray');
        } elseif (isset($customers->first()->customerPayperiodTemplate[$last_report_key]) && in_array($last_report_payperiod, $allowed_payperiods_arr) && isset($current_template_id)) {
            $score_arr = $this->customer_map_repository->calculateScoreArr($customers->first(), $last_report_key);
            $average_score_arr = (!empty($score_arr)) ? $this->customer_map_repository->getAverageScore($score_arr) : array('total' => 0);
            $score_class = (!empty($score_arr)) ? $this->customer_map_repository->getColorClassForRule($average_score_arr) : array('total' => 'gray');
        } else {

            $default_color = $this->customer_map_repository->getDefaultColor();
            $average_score_arr = ["total" => 0];
            $score_class = ["total" => $default_color];
        }
        $all_payperiods = $this->pay_period_repository->getAllPayPeriodListWithNameAndYear();
        $report_key = ((count($customer['details']['customer_payperiod_template']) - 1) < 0) ? 0 : count($customer['details']['customer_payperiod_template']) - 1;
        $note_list = $this->site_note_repository->getSiteNoteDatesByCustomer($customer['details']['id']);
        $site_note_dateformat = $this->site_notes_dateformat;
        $this->setAverageScoreArr($average_score_arr);
        $this->setScoreClass($score_class);
        $abovedate = $this->pay_period_repository->getPayperiodcountabovedate();
        $belowdate = PayPeriod::where('id', '<', $current_payperiod->id)->count();

        return view('supervisorpanel::customer-details', compact('payperiods', 'current_payperiod', 'customer', 'report_key', 'incident_load', 'analytics_load', 'all_payperiods', 'note_list', 'average_score_arr', 'score_class', 'site_note_dateformat', 'abovedate', 'belowdate', 'lessthanenddate'));
    }

    public function getCustomerScoreColor()
    {
    }

    /**
     * Generate customer report
     *
     * @param [type] $customer_id
     * @param [type] $payperiod_id
     * @param [type] $template_id
     * @param [type] $template_customer_payperiod_id
     * @return void
     */
    public function customerReport(
        $customer_id,
        $payperiod_id,
        $template_id = null,
        $template_customer_payperiod_id = null
    ) {
        if (isset($template_customer_payperiod_id)) {
            $current_template = Template::with('templateForm')->find($template_id);
            $reportDateObj = CustomerPayperiodTemplate::select('created_at')
                ->find($template_customer_payperiod_id);
            $reportDate = Carbon::parse($reportDateObj->created_at)->toDayDateTimeString();
            $report_submitted = true;
        } else {
            $current_template = $this->customer_report_repository->getCurrentTemplate();
            $reportDateObj = Carbon::now();
            $reportDate = $reportDateObj->toDayDateTimeString();
            $report_submitted = false;
        }
        //$current_template = $this->customer_report_repository->getTemplateByPayPeriod($payperiod_id);
        $current_template_arr = $current_template->toArray();
        $formated_template = $this->customer_report_repository->formatTemplate($current_template_arr, $payperiod_id, $customer_id, $template_customer_payperiod_id);
        $payperiod_name_obj = PayPeriod::select('pay_period_name')->find($payperiod_id);
        $payperiod_name = $payperiod_name_obj->pay_period_name;
        $can_submit = $this->customer_report_repository->getCanWriteSurvey();
        $can_view_areamanager_notes = $this->customer_report_repository->getCanViewAreaMangerNotes();
        $can_edit_areamanager_notes = $this->customer_report_repository->getCanEditAreaManagerNotes();

        $ratingLookups = $this->employee_rating_lookups
            ->orderBy('score', 'ASC')
            ->pluck('rating', 'id')
            ->toArray();
        if (\Auth::user()->hasAnyPermission(['super_admin', 'admin'])) {
            $employeeList = $this->user_repository->getUserList(
                true,
                null,
                null,
                ['admin', 'super_admin']
            )->sortBy('full_name')
                ->pluck('full_name', 'id')
                ->toArray();
        } else {
            $employeeList = $this->user_repository->getUserList(
                true,
                null,
                Auth::user()->id,
                null
            )->sortBy('full_name')
                ->pluck('full_name', 'id')
                ->toArray();
        }
        $reasonList = LeaveReason::orderBy('reason')->pluck('reason', 'id')->toArray();
        return view(
            'supervisorpanel::customer-report',
            compact(
                'formated_template',
                'reportDate',
                'payperiod_name',
                'report_submitted',
                'can_view_areamanager_notes',
                'can_edit_areamanager_notes',
                'can_submit',
                'employeeList',
                'reasonList',
                'ratingLookups'
            )
        );
    }

    /**
     * The page to show the average colours over the payperiods
     *
     * @param [type] $customer_id
     * @param [type] $payperiod_id
     * @param [type] $current_payperiod_id
     * @param [type] $template_id
     * @return void
     */
    public function customerReportResult($customer_id, $payperiod_id, $current_payperiod_id = null, $template_id = null)
    {

        $payperiods_lists = PayPeriod::whereActive(true)
            ->where('start_date', '<=', Carbon::today())
            ->orderBy('start_date', 'desc')
            ->pluck('id')
            ->toArray();
        $no_of_payperiods = config('globals.customer_detail_payperiod_paginations');
        foreach (array_chunk($payperiods_lists, $no_of_payperiods) as $id => $payperiods_list) {
            foreach ($payperiods_list as $payperiod) {
                if (isset($payperiod_id) && $payperiod == $payperiod_id) {
                    $current_payperiod_page = $id + 1;
                } else if (!isset($payperiod_id)) {
                    $current_payperiod_page = $id;
                }
            }
        }
        $currentPage = $current_payperiod_page;
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        if (!isset($template_id)) {
            $current_template = $this->customer_report_repository->getCurrentTemplate();
        } else {
            $current_template = $this->customer_report_repository->getTemplateById($template_id);
        }
        $payperiods = PayPeriod::whereActive(true)
            ->where('start_date', '<=', Carbon::today())
            ->take(1)
            //->orderByRaw('FIELD(id, ' . $payperiod_id . ') desc')
            ->orderBy('week_one_end_date', 'desc')
            ->simplePaginate(config('globals.customer_detail_payperiod_paginations'));
        $html = $heading = '';
        $question_arr = array();
        foreach ($current_template->templateForm as $eachQuestion) {

            if ($eachQuestion->questionCategory->average == 'Yes' && $eachQuestion->parent_position == null) {
                if (!isset($question_arr[$eachQuestion->questionCategory->description])) {
                    $question_arr[$eachQuestion->questionCategory->description] = array();
                }
                array_push($question_arr[$eachQuestion->questionCategory->description], array('question_text' => $eachQuestion->question_text, 'id' => $eachQuestion->id));
            }
        }
        foreach ($question_arr as $category => $eachQuestionCategory) {
            foreach ($eachQuestionCategory as $eachQuestion) {
                if ($heading != $category) {
                    $heading = $category;
                    $html .= '<div class="formpanel-header-sub"><h8 class="color-white">' . $heading . '</h8></div>';
                }
                $html .= '<div class="form-group row"><label class="col-sm-5 col-md-4 qlabel-report">' . $eachQuestion['question_text'] . '</label><div class="stacked-bar-graph-content col-sm-7  col-md-8"><span class="stacked-bar-graph-prev"></span>';
                foreach ($payperiods as $eachPayperiod) {
                    $color = 'empty';
                    $score = null;
                    $answer = null;

                    $customerPayperiodTemplate = CustomerPayperiodTemplate::where('customer_id', '=', $customer_id)
                        ->where('payperiod_id', '=', $eachPayperiod->id)
                        ->where('template_id', '=', $current_template->id)
                        ->first();
                    if ($customerPayperiodTemplate != null) {
                        $answer_object_for_this_question = CustomerReport::where('customer_payperiod_template_id', '=', $customerPayperiodTemplate->id)
                            ->where('element_id', '=', $eachQuestion['id'])
                            ->first();
                        if ($answer_object_for_this_question != null && $answer_object_for_this_question->score !== null) {
                            $templateSettingRules = \Modules\Admin\Models\TemplateSettingRules::with('color')
                                ->where('min_value', '<=', $answer_object_for_this_question->score)
                                ->where('max_value', '>=', $answer_object_for_this_question->score)
                                ->first();

                            $color = ($templateSettingRules->color->color_class_name) ?? $color;

                            $score = $answer_object_for_this_question->score;
                            $answer = ($answer_object_for_this_question->answer) ?? "NA";
                        }
                    }

                    $html .= '<span data-score="' . $score . '" title="' . $answer . '" class="stacked-bar-graph-content-size bar-color-' . $color . '" ></span>';
                }
                $html .= '<span class="stacked-bar-graph-next"></span></div></div>';
            }
        }
        return view('supervisorpanel::partials.customer-report-result', compact('html', 'current_template', 'payperiods', 'templateSettingRules'));
    }

    /**
     * Function to decide to show
     *     - survey questions if active template is present and report not filled
     *     - report if survey question filled
     *     - Message if not template found
     * @param type $customer_id
     * @param type $payperiod_id
     * @return type
     */
    public function customerPayperiodReport($customer_id, $payperiod_id)
    {

        $current_template = null;
        $selected_payperiod = PayPeriod::whereActive(true)->find($payperiod_id);

        $payperiodabovedate = $this->pay_period_repository->getPayperiodcountabovedate();

        $chosenpayperiod = PayPeriod::find($payperiod_id);
        $selectedpayperiodenddate = $selected_payperiod->end_date;

        $previouspayperiodarray = PayPeriod::where('end_date', '<=', today())->orderBy('start_date', 'desc')->take(1)->first();

        if ($previouspayperiodarray != null) {
            $previouspayperidid = $previouspayperiodarray->id;
        }

        $payperiodenddate = $chosenpayperiod->end_date;
        $lessthanenddate = $payperiodenddate > today();

        $current_payperiod = $this->pay_period_repository->getCurrentPayPeriodextended();

        if (isset($current_payperiod) && ($current_payperiod->id == $payperiod_id)) {
            $currentpayperiodenddate = $current_payperiod->week_one_end_date;
            $weekenddateflag = $currentpayperiodenddate == date("Y-m-d", strtotime(today()));
            $weekenddateflag = true;
        } else if ($payperiod_id == $previouspayperiodarray->id) {
            if (isset($current_payperiod)) {
                if ($current_payperiod->week_one_end_date >= date("Y-m-d")) {
                    $weekenddateflag = true;
                } else {
                    $weekenddateflag = false;
                }
            } else {
                $current_payperiod = $this->pay_period_repository->getPayperiodByDate(Carbon::now()->subDays(8)->toDateTimeString());
                $weekenddateflag = true;
            }
        } else {

            $current_payperiod = $this->pay_period_repository->getPayperiodByDate(Carbon::now()->subDays(8)->toDateTimeString());
            $weekenddateflag = true;
        }

        $latestpayperiod = $this->pay_period_repository->getCurrentPayPeriodextended();

        $payperiodabovedate = $this->pay_period_repository->getPayperiodcountabovedate();

        $current_template = $this->customer_report_repository->getTemplateByCustomerPayperiod($customer_id, $payperiod_id);

        if (isset($current_payperiod) && $current_payperiod->id == $selected_payperiod->id && !isset($current_template)) {
            $current_template = $this->customer_report_repository->getCurrentTemplate();
        } else if (!isset($current_template)) {
            $current_template = $this->customer_report_repository->getTemplateByPayPeriod($selected_payperiod->id);
        }

        if ($current_template != null && $current_payperiod != null) {
            /* check if an entry against customer and payperiod is exist */

            $customerPayperiodTemplate = CustomerPayperiodTemplate::where('customer_id', '=', $customer_id)
                ->where('payperiod_id', '=', $payperiod_id)
                ->where('template_id', '=', $current_template->id)
                ->first();

            if ($customerPayperiodTemplate == null && !((Auth::user()->can('submit-survey')))) {
                return response()->json(array('success' => true, 'content' => 'Survey not Submitted by the Supervisor'));
            }
            if ($customerPayperiodTemplate == null && $current_payperiod != null && $payperiod_id == $current_payperiod->id && $payperiodabovedate > 0) {
                $content = $this->customerReport($customer_id, $payperiod_id);
            } else if ($customerPayperiodTemplate == null && $current_payperiod != null && $payperiod_id == $current_payperiod->id && $lessthanenddate == true && $payperiodabovedate < 1) {
                $content = $this->customerReport($customer_id, $payperiod_id);
            } else if ($customerPayperiodTemplate == null && $previouspayperidid == $payperiod_id && $weekenddateflag == true) {

                $content = $this->customerReport($customer_id, $payperiod_id);
            } else if ($customerPayperiodTemplate != null) {
                $content = $this->customerReportResult($customer_id, $payperiod_id, $current_payperiod->id, $current_template->id);
            }
        }
        if (\Auth::user()->hasAnyPermission(['super_admin', 'admin'])) {
            $employeeList = $this->user_repository->getUserList(
                true,
                null,
                null,
                ['admin', 'super_admin']
            )->sortBy('full_name')
                ->pluck('full_name', 'id')
                ->toArray();
        } else {
            $employeeList = $this->user_repository->getUserList(
                true,
                null,
                Auth::user()->id,
                null
            )->sortBy('full_name')
                ->pluck('full_name', 'id')
                ->toArray();
        }
        $employeeHtml = '<option value="">Please Select</option>';
        foreach ($employeeList as $key => $value) {
            $employeeHtml .= '<option value="' . $key . '">' . $value . '</option>';
        }
        return response()->json(array(
            'success' => true,
            'employeeHtml' => $employeeHtml,
            'content' => !empty($content) ? ($content->render()) : ' No Active Template/Report Found!'
        ));
    }

    /**
     * Function to show customer trend report
     * @param type $customer_id
     * @param type $payperiod_start
     * @param type $payperiod_end
     * @param bool $is_html
     * @return type
     */
    public function customerPayperiodTrendReport($customer_id, $payperiod_start, $payperiod_end, $is_html = true)
    {
        $report_arr = $this->customer_report_repository
            ->customerPayperiodTrendReport(
                $customer_id,
                $payperiod_start,
                $payperiod_end
            );
        $trendchart = $report_arr['trendchart'];
        $report_keys = $report_arr['report_keys'];
        $average_report = $report_arr['average_report'];
        $current_report = $report_arr['current_report'];
        if (empty(!$average_report)) {

            $charts = Charts::create('line', 'highcharts')
                ->title('Average Site Trend')
                //->dimensions(1000, 400) // Width x Height
                ->dimensions(0, 400) // Width x Height
                ->template("orange-material")
                ->colors(['#E65100', '#E65100', '#E65100'])
                ->elementLabel('Average Site Trend')
                ->values($trendchart)
                ->labels(array_keys($trendchart));

            $view = View::make('supervisorpanel::partials.trend-chart', ['chart' => $charts]);
            $contents = $view->render();

            $html = '<div class="row">
         <div class="col-lg-4 p-0 pr-2">
           <p
           class="text-center pt-2 pb-2"
           style="background-color: #E65100; color: white; margin-bottom: 0px !important;">
           <i
           class="fas fa-arrow-left float-left pt-1 pl-2"
           onclick="javascript: history.go(0)"
           style="cursor: pointer;">
           </i>
           Period Report
           </p>
             <table class="table period-report table-bordered m-0 p-0">
               <thead>
                 <tr>
                   <th style="background-color: #343F4E;"></th>
                   <th style="color: white; background-color: #343F4E; text-align:center;" width="20%">Current</th>
                   <th style="color: white; background-color: #343F4E; text-align:center;" width="20%">Average</th>
                   <th style="color: white; background-color: #343F4E; text-align:center;" width="20%">Trend</th>
                 </tr>
               </thead>
               <tbody>';

            foreach ($report_keys as $eachkey) {
                // if ($eachkey == 'total') {
                //     continue;
                // }
                if (isset($current_report) && empty(!$current_report['score'])) {
                    if (
                        is_numeric($average_report['score'][$eachkey])
                        && is_numeric($current_report['score'][$eachkey])
                    ) {
                        $average_value =
                            (float) $current_report['score'][$eachkey] - (float) $average_report['score'][$eachkey];
                    }
                } else {
                    $average_value = (float) $average_report['score'][$eachkey];
                }

                if ($average_value == 0) {
                    $average_color = config('globals.colour_yellow');
                    $trend_text_color = config('globals.colour_black');
                } elseif ($average_value > 0) {
                    $average_color = config('globals.colour_green');
                    $trend_text_color = config('globals.colour_black');
                } else {
                    $average_color = config('globals.colour_red');
                    $trend_text_color = config('globals.colour_white');
                }

                if (
                    isset($current_report)
                    && empty(!$current_report['color_class'])
                    && ($current_report['color_class'][$eachkey] == 'red')
                ) {
                    $text_color = config('globals.colour_white');
                } else {
                    $text_color = config('globals.colour_black');
                }

                if (
                    isset($average_report)
                    && empty(!$average_report['color_class'])
                    && ($average_report['color_class'][$eachkey] == 'red')
                ) {
                    $avg_color = config('globals.colour_white');
                } else {
                    $avg_color = config('globals.colour_black');
                }

                $html .= "<tr>
                            <td class='text-capitalize' scope='row' style='color: white; background-color: #343F4E;'>" . $eachkey . "</td>";
                if (isset($current_report) && empty(!$current_report['score'])) {
                    $html .= "<td
                                        align='center'
                                        class='bar-color-"
                        . $current_report['color_class'][$eachkey] . "'>
                                        <span style='font-weight: bold;color:" . $text_color . "; '>"
                        . number_format((float) $current_report['score'][$eachkey], 3, '.', '')
                        . "</span>
                                        </td>";
                } else {
                    $html .= "<td
                                        align='center'
                                        style='color:#fff;'
                                        class='bar-color-black'>
                                        <span style='font-weight: bold;color:#fff;'> 0.000 </span>
                                        </td>";
                }
                $html .= "<td
                            align='center'
                            class='bar-color-" . $average_report['color_class'][$eachkey] . "'>
                            <span  style='font-weight: bold;color:" . $avg_color . "; '>"
                    . number_format((float) $average_report['score'][$eachkey], 3, '.', '') . "</span>
                            </td>
                            <td  align='center'  class='bar-color-" . $average_color . "'>
                            <span style='font-weight: bold;color:" . $trend_text_color . "; '>"
                    . number_format((float) $average_value, 3, '.', '') . "</span>
                            </td>
                            </tr>";
            }

            $html .= '</tbody>
             </table>
         </div>
         <div class="col-lg-8 p-0" style="overflow-x: auto; white-space: nowrap; border: thin solid #ceb2b2; background-color: white;">
            <div class="d-flex bd-highlight" style="background-color: #E65100; color: white;">
                <div class="mr-auto p-2 bd-highlight">Site Trend Scores By Period</div>
                <div class="p-2 bd-highlight">Current</div>';
            if (
                isset($current_report)
                && empty(!$current_report['color_class'])
                && ($current_report['color_class']['total'] == 'red')
            ) {
                $graph_text_color = config('globals.colour_white');
            } else {
                $graph_text_color = config('globals.colour_black');
            }

            if (isset($current_report) && empty(!$current_report['score'])) {
                $html .= '<div
                            class="p-2 bd-highlight bar-color-' . $current_report['color_class']['total'] . '"
                            style="color: ' . $graph_text_color . '">'
                    . number_format((float) $current_report['score']['total'], 3, '.', '')
                    . '</div>';
            } else {
                $html .= '<div class="p-2 bd-highlight bar-color-black" style="color: #fff;">0.000</div>';
            }

            $html .= '<div class="p-3 bd-highlight"></div>
                <div class="p-2 bd-highlight">Average</div>';
            if (
                isset($average_report)
                && empty(!$average_report['color_class'])
                && ($average_report['color_class']['total'] == 'red')
            ) {
                $graph_avg_color = config('globals.colour_white');
            } else {
                $graph_avg_color = config('globals.colour_black');
            }
            $html .= '<div
                class="p-2 bd-highlight bar-color-' . $average_report['color_class']['total'] . '"
                style="color: ' . $graph_avg_color . ';">'
                . number_format((float) $average_report['score']['total'], 3, '.', '')
                . '</div>';

            $html .= '</div>
           <div class="container" style="background-color: white;" id="trend-container">'
                . $contents .
                '</div>
         </div>
       </div>';
            return response()->json(array('success' => true, 'content' => $html));
        } else {
            return response()->json(array('success' => false, 'content' => 'Survey not Submitted by the Supervisor'));
        }
    }

    /**
     * Edit Report
     *
     * @param [type] $customer_id
     * @param [type] $payperiod_id
     * @return void
     */
    public function customerPayperiodReportEdit($customer_id, $payperiod_id)
    {
        $customerTemplatePayperiod = CustomerPayperiodTemplate::where('customer_id', $customer_id)->where('payperiod_id', $payperiod_id)->orderby('created_at', 'desc')->first();
        if ($customerTemplatePayperiod != null) {
            $content = $this->customerReport($customer_id, $payperiod_id, $customerTemplatePayperiod->template_id, $customerTemplatePayperiod->id);
        }
        if (\Auth::user()->hasAnyPermission(['super_admin', 'admin'])) {
            $employeeList = $this->user_repository->getUserList(
                true,
                null,
                null,
                ['admin', 'super_admin']
            )->sortBy('full_name')
                ->pluck('full_name', 'id')
                ->toArray();
        } else {
            $employeeList = $this->user_repository->getUserList(
                true,
                null,
                Auth::user()->id,
                null
            )->sortBy('full_name')
                ->pluck('full_name', 'id')
                ->toArray();
        }
        $employeeHtml = '<option value="">Please Select</option>';
        foreach ($employeeList as $key => $value) {
            $employeeHtml .= '<option value="' . $key . '">' . $value . '</option>';
        }
        return response()->json(array(
            'success' => true,
            'employeeHtml' => $employeeHtml,
            'content' => !empty($content) ? ($content->render()) : 'Survey not Submitted by the Supervisor'
        ));
    }

    /**
     * Save the survey to DB
     * @param Request $request
     * @return type
     */
    public function customerPayperiodReportStore(Request $request)
    {
        try {
            DB::beginTransaction();
            $report_form = $request->all();
            $template_id = $request->get('template_id');
            $customer_id = $request->get('customer_id');
            $payperiod_id = $request->get('payperiod_id');
            $template_customer_payperiod_id = $request->get('template_customer_payperiod_id');
            if ((empty($template_customer_payperiod_id) && \Auth::user()->can('submit-survey')) || (\Auth::user()->can('edit-survey'))) {
                //if not area manager
                $customer_payperiod_id = $this->customer_report_repository->storeCustomerTemplatePayperiod($template_id, $customer_id, $payperiod_id, $template_customer_payperiod_id);

                $get_formated_template = $this->customer_report_repository->formatTemplate($this->customer_report_repository->getTemplateById($template_id)->toArray(), $payperiod_id, $customer_id);
                $template_questions = $get_formated_template['questions'];

                if (!empty($template_customer_payperiod_id)) {
                    $this->customer_report_repository->deleteEmployeeLeave($template_customer_payperiod_id);
                    $this->customer_report_repository->deleteCustomerReport($template_customer_payperiod_id);
                }

                foreach ($template_questions as $category => $question_arr) {
                    foreach ($question_arr as $question_key => $question) {
                        $this->customer_report_repository->storeCustomerReport($question, $report_form, $customer_payperiod_id);
                    }
                }
            }
            $areamanager_note_name_arr = $this->area_manager_repository->getAreaManagerNotesNames($report_form);
            $existing_comments = $this->area_manager_repository->fetchAreaManagerNotes($template_customer_payperiod_id);
            $this->area_manager_repository->storeAreaManagerNotes($areamanager_note_name_arr, $template_customer_payperiod_id, $existing_comments);
            DB::commit();
            $last_updated_at = CustomerReport::latest()->value('updated_at')->format('Y-m-d H:i:s');
            return response()->json(array('success' => 'true', 'last_update' => $last_updated_at));
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Store customer rating
     * @param Request $request
     * @return object
     */
    public function customerRatingStore(CustomerRatingRequest $request)
    {
        $rating_data = [
            'customer_id' => $request->get('customer_id'),
            'rating_id' => $request->get('rating_id'),
            'notes' => $request->get('notes'),
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id
        ];
        $this->customer_rating_repository->save($rating_data);
    }

    /**
     * Display map markers and customer details
     * @return type
     */
    public function guardTourMap($guard_tour)
    {

        $latest_template = $this->customer_report_repository->getLatestTemplate();
        $customers_arr = $this->customer_map_repository->getCustomerMapDetails($latest_template, $guard_tour);

        /* START * Get Customer Ids from Session and Filter */
        $customer_ids = $this->helperService->getCustomerIds();
        if (!empty($customer_ids)) {
            $customers_arr_filer = array();
            foreach ($customers_arr['customer_score'] as $customer) {
                if (in_array($customer['customer']['details']['id'], $customer_ids)) {
                    array_push($customers_arr_filer, $customer);
                }
            }
            $customers_arr['customer_score'] = $customers_arr_filer;
        }
        /* END * Get Customer Ids from Session and Filter */
        
        $shift_flag = 1;
        if (count($customers_arr['customer_score']) > 0) {
            $customer_score = $customers_arr['customer_score'];
            $customer_rating = $customers_arr['customer_rating'];
            $customer_rating_color = $customers_arr['customer_rating_color'];
            return view('supervisorpanel::customers-in-map', compact('customer_score', 'customer_rating', 'customer_rating_color', 'shift_flag'));
        } else {
            return view('supervisorpanel::customers-in-map', compact('shift_flag'));
        }
    }

    /**
     * Fetch the details of customer Guard Tour
     *
     * @param [type] $id
     * @param Request $request
     * @return view
     */
    public function customerGuardTourDetails($id)
    {
        $customer = $this->customer_repository->getCustomerWithMangers($id);
        $user = \Auth::user();
        $emp_array = array();
        $stdate = date('Y-m-d', strtotime("-2 days"));
        $endate = date("Y-m-d");
        if ($user->hasPermissionTo('view_all_shift_journal_20_transaction')) {
            $modules = ShiftModule::where('customer_id', $id)->where('is_active', 1)->pluck('module_name', 'id')->toArray();
            $emp_array = $this->user_repository->getUserLookup(null, ['super_admin', 'admin'], true, false, null, false);
            asort($emp_array);
        } else if ($user->hasPermissionTo('view_shift_journal_20_transaction')) {
            $modules = ShiftModule::where('customer_id', $id)->where('is_active', 1)->pluck('module_name', 'id')->toArray();
            $employees = $this->employee_allocation_repository->getEmployeeAssigned([$user->id]);

            foreach ($employees as $key => $emp) {
                $emp_array[$emp->user_id] = $emp->user->first_name . ' ' . $emp->user->last_name;
            }

            if ($user->hasPermissionTo('view_shift_journal_20_transaction')) {
                $emp_array[$user->id] = $user->first_name . ' ' . $user->last_name;
            }
            asort($emp_array);
        } else {
            $modules = [];
        }
        if ($user->hasAnyPermission(['admin', 'super_admin'])) {
            $emp_array = $this->user_repository->getUserLookup(null, ['super_admin', 'admin'], true, false, null, false);
        }
        return view('supervisorpanel::customer-guardtour-details', compact('customer', 'id', 'modules', 'emp_array', 'stdate', 'endate'));
    }

    /**
     * Fetch the List of customer guard Tour
     *
     * @param [type] $customer_id
     * @param Request $request
     * @return json
     */
    public function guardTourList($customer_id)
    {
        $datavalues = $this->guard_tour_repository->getList($customer_id);
        // dd($datavalues);
        return response()->json(['success' => true, 'message' => 'success', 'data' => ($datavalues), 'module_order' => [[1, "desc"], [2, "desc"]]]);
        //return datatables()->of($datavalues)->toJson();
    }

    /**
     * Get real path to Guard Tour file upload
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */

    public function getfiles($guard_tour_id, $image_id)
    {
        try {
            $path = $this->guard_tour_repository->guardTourAttachment($guard_tour_id, $image_id);
            return response()->download($path['path'], $path['file'], [], 'inline');
        } catch (\Exception $e) {
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Function to get dropdown values for filter
     * @return array
     */
    public function getFilterDropdowns($supervisorpanel, $stc)
    {
        $filter_values['industry_sector'] = $this->industrySectorLookupRepository->getList();
        $filter_values['region'] = $this->regionLookupRepository->getList();
        if ($stc == 'stc') {
            $project_numbers = $this->customer_model->where('stc', STC_CUSTOMER)->pluck('project_number')->toArray();
            $cities = $this->customer_model->orderBy('city')->where('stc', STC_CUSTOMER)->pluck('city')->toArray();
        } else {
            $project_numbers = $this->customer_model->where('stc', PERMANENT_CUSTOMER)->pluck('project_number')->toArray();
            $cities = $this->customer_model->orderBy('city')->where('stc', PERMANENT_CUSTOMER)->pluck('city')->toArray();
        }
        $project_number = $city = [];
        foreach ($project_numbers as $key => $value) {
            $project_number[$value] = $value;
        }
        $filter_values['project_number'] = $project_number;
        foreach ($cities as $key => $value) {
            $city[$value] = $value;
        }
        $filter_values['city'] = $city;
        $statuses = $this->color_model->orderBy('color_name')->pluck('color_name')->toArray();
        foreach ($statuses as $key => $value) {
            $status[$value] = $value;
        }
        $filter_values['status'] = $status;
        $filter_values['area_manager'] = $this->user_repository->getUserLookup(['area_manager']);
        $filter_values['supervisor'] = $this->user_repository->getUserLookup(['supervisor']);
        return $filter_values;
    }

    /**
     * Function to send an email notification to all the supervisors about updating SU Panel, at the end of a pay period.
     * Author : Liya shereef
     *
     * @return type
     */

    public function mailToSupervisorextended()
    {
        $customers_list = array();
        $survey_submitted_customers = array();
        $today = date("Y-m-d");
        /**
         * Level 1
         * Creating database for email cron job if today is startdate of next payperiod
         */
        $payperiodstartdatecount = $this->pay_period_repository->checkPayperiodstartdate($today);
        $lastpayperiod = $this->pay_period_repository->getLastPayperiod($today);
        $payperiodid = $lastpayperiod->id;
        $payperiodstartdate = $lastpayperiod->start_date;

        $customers_details_list = $this->customer_repository->getCustomerList()->toArray();
        foreach ($customers_details_list as $key => $customer) {
            array_push($customers_list, $customer['id']);
        }

        $customerPayperiodTemplate = $this->Customer_Report_EmailScheduler_Repository->getCustomersubmittedtemplate($payperiodid, $customers_list);

        if ($payperiodstartdatecount > 0) {

            /////////////////
            /**
             * Get Customer who have submitted for pay period template
             */
            $customernotsendarray = array_diff($customers_list, $customerPayperiodTemplate);
            ////////////////////////
            foreach ($customernotsendarray as $cuskey => $cusvalue) {
                # code...
                $customer_id = $cusvalue;
                $supervisorarray = $this->customer_employee_allocation_repository->allocationList($customer_id, ["supervisor"]);
                $this->Customer_Report_EmailScheduler_Repository->setCustomernotsubmitteddb($customer_id, $supervisorarray, $today, $payperiodstartdate, $payperiodid);
            }
        }

        /**
         * Level-2
         * Checking if the user already submitted the report
         */

        foreach ($customerPayperiodTemplate as $customer) {

            $this->Customer_Report_EmailScheduler_Repository->removeFromScheduledemail($payperiodid, $customer, $today);
        }
        //$this->Customer_Report_EmailScheduler_Repository->checkSubmittedReportsScheduledemail($payperiodid);

        /**
         * Level3
         * Send mail if there is any mail scheduled for today's date
         */
        $this->Customer_Report_EmailScheduler_Repository->sendScheduledemail($today);
    }

    /**
     * Function to send an email notification to all the supervisors about updating SU Panel, at the end of a pay period.
     *
     * @return type
     */
    public function mailToSupervisor()
    {
        $customers_list = array();
        $survey_submitted_customers = array();
        $current_payperiod = $this->pay_period_repository->getLastNPayperiodWithCurrent()->first();
        if ($current_payperiod->end_date == date('Y-m-d')) {
            $current_template = $this->customer_report_repository->getCurrentTemplate();
            $customers_details_list = $this->customer_repository->getCustomerList()->toArray();
            foreach ($customers_details_list as $key => $customer) {
                array_push($customers_list, $customer['id']);
            }
            $customerPayperiodTemplate = CustomerPayperiodTemplate::whereIn('customer_id', $customers_list)
                ->where('payperiod_id', '=', $current_payperiod->id)
                ->where('template_id', '=', $current_template->id)
                ->get()
                ->toArray();
            foreach ($customerPayperiodTemplate as $key => $survey_submitted_customer) {
                array_push($survey_submitted_customers, $survey_submitted_customer['customer_id']);
            }
            $survey_not_submitted_customers = array_diff($customers_list, $survey_submitted_customers);
            $survey_not_submitted_supervisors = $this->customerEmployeeAllocationModel->whereIn('customer_id', $survey_not_submitted_customers)->pluck('user_id')->toArray();
            if (!empty($survey_not_submitted_supervisors)) {
                $this->sendNotification($survey_not_submitted_supervisors);
            }
        }
    }

    /**
     * To send notificaion to supervisors
     *
     * @param $survey_not_submitted_supervisors
     * @return void
     */
    public function sendNotification($survey_not_submitted_supervisors)
    {
        /*send mail - start */
        $supervisors = array();
        foreach ($survey_not_submitted_supervisors as $survey_not_submitted_supervisor_id) {
            array_push($supervisors, $this->user_repository->getUserDetails($survey_not_submitted_supervisor_id));
        }
        $email_ids = data_get($supervisors, '*.email');
        $mail = Mail::to($email_ids);
        $mail->send(new SendSurveySubmitNotification('mail.survey-submit-notification.create'));
        /*send mail - end */
    }

    /**
     * Get Latest Guard Tour Customer Id
     * @return id
     */
    public function getLatestGuardTourCustomerId()
    {

        $user_details = \Auth::user();
        $guard_tour_enabled = true;

        if ($user_details->hasAnyPermission(['view_all_guard_tour', 'view_all_shift_journal'])) {
            $customers_list = array_keys($this->customer_repository->getGuardTourCustomerList());
        } else {
            $customers_list = $this->customer_repository->getAllAllocatedCustomerId([\Auth::user()->id]);
        }
        $customers_list = $this->customer_model->with('employeeShiftPayperiods', 'employeeShiftPayperiods.shifts')->whereIn('id', $customers_list)->where('guard_tour_enabled', $guard_tour_enabled)->get();

        if (!empty($customers_list)) {

            return $this->getRecentShiftSubmittedCustomer($customers_list);
        } else {
        }
    }

    public function getRecentShiftSubmittedCustomer($customers_list)
    {
        foreach ($customers_list as $each_customer) {

            $shiftPayperiods = $each_customer->employeeShiftPayperiods;
            if ($each_customer->employeeShiftPayperiods->count() > 0) {
                $recent_shift[] = $this->guard_tour_repository->getLatestShift($shiftPayperiods);
            }
        }

        if (isset($recent_shift)) {
            usort($recent_shift, function ($a, $b) {
                return strcmp($b['created_at'], $a['created_at']);
            });
            foreach ($recent_shift as $key => $latest_shift) {
                $recent_shift = $latest_shift;
                break;
            }
            $employee_shift_payperiod_id = $recent_shift['employee_shift_payperiod_id'];
            $customer_id = EmployeeShiftPayperiod::where('id', $employee_shift_payperiod_id)->value('customer_id');
            return $customer_id;
        }
    }

    /**
     * Fetch the details of customer Shift Journal
     *
     * @param [type] $id
     * @param Request $request
     * @return view
     */
    public function customerShiftJournalDetails($id)
    {

        $customer = $this->customer_repository->getCustomerWithMangers($id);
        return view('supervisorpanel::customer-shiftjournal-details', compact('customer', 'id'));
    }

    /**
     * Fetch the List of customer Shift Journal
     *
     * @param [type] $customer_id
     * @param Request $request
     * @return json
     */
    public function shiftJournalList($customer_id)
    {

        $datavalues = $this->shift_journal_repository->getList($customer_id);

        return response()->json(['success' => true, 'message' => 'success', 'data' => ($datavalues), 'module_order' => [[0, "desc"], [1, "desc"]]]);
        //return datatables()->of($datavalues)->toJson();
    }

    /**
     *Save  Shift Journal from Web
     *
     * @param [type] $customer_id
     * @param Request $request
     * @return json
     */
    public function saveShiftJournalWeb(Request $request)
    {
        $data = $this->shift_journal_repository->saveShiftJournalWeb($request);
        return response()->json(array('success' => 'true', 'data' => $data));
    }

    /**
     * Function to fetch site note
     * @param type $customer_id
     * @param type $note_id
     * @return type
     */
    public function siteNotesIndex($customer_id, $note_id)
    {
        $note_data = $this->site_note_repository->getSiteNote($note_id);
        $allocated_users = $this->customer_employee_allocation_repository->allocationList($customer_id);
        $site_note_status = $this->site_note_status_repository->getLookupList();
        $site_note_dateformat = $this->site_notes_dateformat;
        $html = View::make('supervisorpanel::partials.site-notes')
            ->with(compact(['customer_id', 'allocated_users', 'site_note_status', 'note_data', 'site_note_dateformat']))
            ->render();
        $content = view('supervisorpanel::partials.customer-report-result', compact('html'));
        return response()->json(array('success' => true, 'content' => !empty($content) ? ($content->render()) : 'Something went wrong!!!'));
    }

    /**
     * Function to save site notes
     * @param SiteNotesRequest $request
     * @param integer $customer_id
     * @param integer $note_id - if 0- new create, >0 edit
     */
    public function saveSiteNote(SiteNotesRequest $request, $customer_id, $note_id)
    {
        try {
            $message = "";
            if ($note_id == 0) {
                $message = "Site Notes has been successfully saved";
            } else {
                $message = "Site Notes has been successfully updated";
            }
            DB::beginTransaction();
            $note_id = $this->site_note_repository->save($request, $customer_id, $note_id);
            $this->site_note_task_repository->save($request, $customer_id, $note_id);
            DB::commit();
            return response()->json(array('success' => true, 'message' => $message, 'id' => $note_id));
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'message' => 'Something went wrong!'));
        }
    }

    /**Shift Module
     * Fetch the List of customer Shift Module
     *
     * @param [type] $module_id
     * @param [type] $customer_id
     * @param Request $request
     * @return json
     */

    public function shiftModuleList($module_id, $customer_id)
    {
        if (request('from_date')) {
            $from_date = request('from_date');
        } else {
            $from_date = date('Y-m-d', strtotime("-2 days"));
        }
        if (request('to_date')) {
            $to_date = request('to_date');
        } else {
            $to_date = date("Y-m-d");
        }
        $name = request('name');
        $moduleOrder = [0, "desc"];

        $widgetRequest = false;
        if (request('tab_id')) {
            $widgetRequest = true;
        }

        $details = ShiftModule::select('enable_timeshift')->where('id', $module_id)->first();
        if ($details->enable_timeshift) {
            $time_shift_enabled = 1;
            $datavalues = $this->shift_journal_repository->getShiftModuleList($module_id, $customer_id, $time_shift_enabled, $from_date, $to_date, $name, $widgetRequest);
            if ($name != '') {
                $moduleOrder = [0, "asc"];
            }
        } else {
            $time_shift_enabled = 0;
            $datavalues = $this->shift_journal_repository->getShiftModuleList($module_id, $customer_id, $time_shift_enabled, $from_date, $to_date, $name, $widgetRequest);
        }
        if ($widgetRequest) {
            $datavalues = $this->landingPageRepository->resetDisabledFieldsFromShiftModuleResultArray(request('tab_id'), $module_id, $datavalues);
        }
        return response()->json(['success' => true, 'message' => 'success', 'data' => ($datavalues), 'module_order' => $moduleOrder]);
    }

    /**Shift Module
     * Fetch the List of customer Shift Journal
     *
     * @param [type] $customer_id
     * @param Request $request
     * @return json
     */
    public function timeshiftList($customer_id)
    {
        $datavalues = $this->shift_journal_repository->getTimeshiftList($customer_id);
        return response()->json(['success' => true, 'message' => 'success', 'data' => ($datavalues), 'module_order' => [[0, "desc"], [2, "asc"]]]);
        //return datatables()->of($datavalues)->toJson();
    }

    /**
     * Update All Employee Shift End Time (If shift duration limit exceeds)
     */
    public function updateEmployeeShiftEndTime()
    {
        $datavalues = $this->employee_shift_repository->endAllEmployeeShiftsExceedsDuration();
    }

    public function siteNotesDetails($id, $siteNote = null, Request $request)
    {
        return $this->customerDetails($id, null, null, $request);
    }

    public function shiftModuleMap()
    {
        $current = Carbon::now();
        $startdate = $current->toDateString();
        if (\Auth::user()->can('view_all_shift_module_mapping')) {
            $project_list = $this->customer_repository->getList();
        } else {
            $project_list = $this->customer_employee_allocation_repository->getDirectAllocatedCustomersList(\Auth::user());
        }
        $module_list = [];
        return view('supervisorpanel::shiftmodule-mapping', compact('project_list', 'startdate', 'module_list'));
    }

    public function getAllShiftModules($id)
    {
        $module_list = ShiftModule::where('customer_id', $id)->where('is_active', 1)->pluck('module_name', 'id')->toArray();
        return $module_list;
    }

    public function getAllShiftModulesMapping(Request $request)
    {
        $date = isset($request->date) ? $request->date : null;
        $project_id = isset($request->id) ? $request->id : null;
        $module_id = isset($request->module_id) ? $request->module_id : null;
        return $this->shift_journal_repository->getShiftModuleMappingList($project_id, $module_id, $date);
    }

    public function getCustomerScoreList()
    {
        $customerId = request('id');
        $payPeriodList = $this->pay_period_repository->getLastNPayperiodWithCurrent(6); //get last four
        $reportKeyList = TemplateQuestionsCategory::withTrashed()->get()->pluck('description')->toArray();
        $reportKeys = [];
        $payperiods = [];
        $reportcolor = [];
        foreach ($payPeriodList as $key => $eachList) {
            $report_arr = $this->customer_report_repository->customerPayperiodTrendReport(
                $customerId,
                $eachList->start_date,
                $eachList->end_date
            );
            if ($eachList[$key] == 0) {
                $payperiods[0] = "Criteria";
            }
            if ($report_arr['report_keys'] != null) {
                foreach ($report_arr['report_keys']  as $key => $eachRow) {
                    if ($eachRow != 'total') {
                        if (!isset($reportKeys[$key])) {
                            array_push($reportKeys, $eachRow);
                        }
                    }
                }
            }
            array_push($payperiods, carbon::parse($eachList->start_date)->format('M d, Y'));
            array_push($reportcolor, $report_arr['average_report']);
        }
        if (!empty($reportKeys)) {
            $content =  $reportKeys;
        } else {
            $current_template = $this->customer_report_repository->getCurrentTemplate();
            $question_arr = [];
            $test = [];
            if ($current_template) {
                foreach ($current_template->templateForm as $eachQuestion) {
                    if ($eachQuestion->questionCategory->average == 'Yes' && $eachQuestion->parent_position == null) {
                        if (!isset($question_arr[$eachQuestion->questionCategory->description])) {
                            $question_arr[$eachQuestion->questionCategory->description] = array();
                        }
                    }
                }
                $content = array_keys($question_arr);
            }
        }
        if (!empty($content)) {
            return response()->json(array('success' => true, 'content' => $content, 'payperiods' => $payperiods, 'contentColors' => $reportcolor));
        } else {
            return response()->json(array('success' => false, 'content' => 'No Data Found!!!'));
        }
    }

    public function getCustomerMoreDetails()
    {
        $customerId = request('id');
        $customer_details = Customer::with('employeeLatestCustomerSupervisor', 'employeeLatestCustomerAreaManager', 'ratingDetails.user.trashedEmployee')->where('id', $customerId)->get()->toArray();
        $customer['rating_details'] = $customer_details[0]['rating_details'];
        $customer['areamanager'] = $customer_details[0]['employee_latest_customer_area_manager']['area_manager'] ?? '';
        $customer['supervisor'] = $customer_details[0]['employee_latest_customer_supervisor']['supervisor'] ?? '';
        return $customer;
    }
}
