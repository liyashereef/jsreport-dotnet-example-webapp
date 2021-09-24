<?php

namespace Modules\FMDashboard\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\IncidentPriorityLookup;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\IncidentCategoryRepository;
use Modules\Admin\Repositories\IncidentPriorityLookupRepository;
use Modules\Admin\Repositories\IncidentReportSubjectRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\PositionLookupRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Client\Repositories\VisitorLogRepository;
use Modules\FMDashboard\Repositories\DashboardWidgetRepository;
use Modules\Hranalytics\Models\Job;
use Modules\Hranalytics\Repositories\CandidateRepository;
use Modules\Hranalytics\Repositories\JobRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;
use Modules\Supervisorpanel\Repositories\IncidentReportRepository;
use Modules\Timetracker\Repositories\EmployeeShiftCpidRepository;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;
use View;

class FMDashboardAPIController extends Controller
{

    public function __construct(IncidentReportRepository $incidentReportRepository,
        IncidentPriorityLookupRepository $incidentPriorityLookupRepository,
        IncidentReportSubjectRepository $incidentReportSubjectRepository,
        CandidateRepository $candidateRepository,
        UserRepository $userRepository,
        VisitorLogRepository $visitorLogRepository,
        PayPeriodRepository $payPeriodRepository,
        EmployeeShiftRepository $employeeShiftRepository,
        PositionLookupRepository $positionLookupRepository,
        EmployeeShiftCpidRepository $employeeShiftCpidRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        TrainingUserCourseAllocationRepository $trainingUserCourseAllocationRepository,
        JobRepository $jobRepository,
        IncidentCategoryRepository $incidentCategoryRepository,
        DashboardWidgetRepository $dashboardWidgetRepository
    ) {
        $this->incidentReportRepository = $incidentReportRepository;
        $this->incidentPriorityLookupRepository = $incidentPriorityLookupRepository;
        $this->incidentReportSubjectRepository = $incidentReportSubjectRepository;
        $this->candidateRepository = $candidateRepository;
        $this->visitorLogRepository = $visitorLogRepository;
        $this->pay_period_repository = $payPeriodRepository;
        $this->employee_shift_repository = $employeeShiftRepository;
        $this->helper_service = new HelperService();
        $this->position_lookup_repository = $positionLookupRepository;
        $this->employee_shift_cpid_repository = $employeeShiftCpidRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->training_user_course_allocation_repo = $trainingUserCourseAllocationRepository;
        $this->user_repo = $userRepository;
        $this->job_repo = $jobRepository;
        $this->incidentCategoryRepository = $incidentCategoryRepository;
        $this->dashboardWidgetRepository = $dashboardWidgetRepository;

    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        //store  dashboard filters
        $this->storeFilters($request);
        $data = [];

        //for getting dashboard filters
        $data['filters'] = $this->getFilters();

/**Start*  Total headder counts */
        $data['counts'] = [];
        $data['counts']['job_tickets_count'] = $this->getJobCount();
        //for total  visitor  count
        $data['counts']['visitor_count'] = $this->visitorLogRepository->getVisitorCountWithFilters();
        // -- hours_worked_count -- at Time widget area.
        /**END*  Total headder counts */

/**Start*  Time widget*/
        $data['fcm_time'] = [];
        $hours_worked = 0;
        $data['fcm_time'] = $this->getPositionGrossAndNetTime();
        $data['counts']['hours_worked_count'] = 0;
        if (isset($data['fcm_time']['hours_worked'])) {
            $hours_worked = $data['fcm_time']['hours_worked'];
        }
        $data['counts']['hours_worked_count'] = $hours_worked;

/**End*  Time widget*/

/**Start* of hr analytics*/
        $data['fcm_hr'] = $this->getHRAnalytics();
/**END** hr analytics*/

/**START* Incident Summary & Priority Start*/

        $data['fcm_incident_summary'] = $this->getIncidentSummaryData();
        $fcm_incident_priority = $this->getIncidentPriorityData();

        if (!empty($fcm_incident_priority)) {
            $data['fcm_incident_priority'] = $fcm_incident_priority['fcm_incident_priority'];
            $data['counts']['incident_count'] = $fcm_incident_priority['total_incident_count'];
        } else {
            $data['fcm_incident_priority'] = [];
            $data['counts']['incident_count'] = 0;
        }

/**END* Incident Summary & Priority Start*/

        $data['fcm_job_tickets'] = $this->getJobTickets();
        $data['fcm_training_compliance'] = $this->getAllCourseAllocatedAndCompletedCount();
        return $data;

    }

    public function getTimeData()
    {

    }

    public function storeFilters($request)
    {

        $customerIds = [];
        $customerIds = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
        $from_date = date('Y-m-d', strtotime('-30 days'));
        $to_date = date('Y-m-d');

        if ($request->has('customer_ids') && is_array($request->input('customer_ids'))) {
            $customerIds = $request->input('customer_ids');
        }
        if ($request->has('from_date') && !empty($request->input('from_date'))) {
            $from_date = $request->input('from_date');
        }

        if ($request->has('to_date') && !empty($request->input('to_date'))) {
            $to_date = $request->input('to_date');
        }

        session()->put('fm_dashboard_customer_ids', $customerIds);
        session()->put('from_date', $from_date);
        session()->put('to_date', $to_date);

        $data['customer_ids'] = session()->get('fm_dashboard_customer_ids');
        if (empty($data['customer_ids'])) {
            $data['customer_ids'] = [];
        }
        $data['from_date'] = session()->get('from_date');
        $data['to_date'] = session()->get('to_date');

        return response()->json([
            'sync' => true,
            'payload' => $data,
        ]);

    }

    public function getFilters()
    {

        $data['customer_ids'] = session()->get('fm_dashboard_customer_ids');
        if (empty($data['customer_ids'])) {
            $data['customer_ids'] = [];
        }
        $data['from_date'] = session()->get('from_date');
        $data['to_date'] = session()->get('to_date');

        return $data;

    }

    public function getJobCount()
    {

        $inputs = $this->helper_service->getFMDashboardFilters();

        return Job::whereIn('status', ['approved', 'completed'])
            ->where(function ($query) use ($inputs) {
                if (!empty($inputs)) {

                    //For From date
                    if (!empty($inputs['from_date'])) {
                        $query->where('created_at', '>=', $inputs['from_date']);
                    }
                    //For to date
                    if (!empty($inputs['to_date'])) {
                        $query->where('created_at', '<=', $inputs['to_date']);
                    }

                    //For customer_ids
                    $query->whereIn('customer_id', $inputs['customer_ids']);
                }
            })->count();
    }

/**Start* of hr analytics*/
    public function getHRAnalytics()
    {

        /*hired candidates with step 18 completed */
        $data['chart']['label'] = array('Hired', 'In Transit', 'Pending');
        $hired = $this->candidateRepository->getListOfHiredCandidate($inputs = []);
        /*intransist candidates less than step 18 */
        $intransit = $this->candidateRepository->getListOfIntransitCandidate($inputs = []);
        /*pending list */
        $pending = $this->candidateRepository->getListOfPendingCandidate($inputs = []);

        // $data['fcm_hr']['chart']['value'] = array((($hired)?$hired:0), (($intransit)?$intransit:0),(($pending)?$pending:0));

        $data['chart']['value'][0] = ['name' => 'Hired', 'y' => (($hired) ? $hired : 0), 'color' => $this->getColor(0)];
        $data['chart']['value'][1] = ['name' => 'In Transit', 'y' => (($intransit) ? $intransit : 0), 'color' => $this->getColor(1)];
        $data['chart']['value'][2] = ['name' => 'Pending', 'y' => (($pending) ? $pending : 0), 'color' => $this->getColor(2)];

        $data['table']['head'] = array('Status', 'Number');

        $data['table']['body'][0] = ['Hired', (($hired) ? $hired : 0)];
        $data['table']['body'][1] = ['In Transit', (($intransit) ? $intransit : 0)];
        $data['table']['body'][2] = ['Pending', (($pending) ? $pending : 0)];
        $array_sum = (($hired) ? $hired : 0) + (($intransit) ? $intransit : 0) + (($pending) ? $pending : 0);
        $data['table']['body'][3] = ['Total', $array_sum];

        return $data;
    }
/**END** hr analytics*/

    /**Start** Incident Summory Graph  */

    public function getIncidentSummaryData()
    {
        $subjects = $this->incidentReportSubjectRepository->getAllForFM();
        $incident_summory_data = collect($this->incidentReportRepository->getIncidentSummmory($inputs = []));
        $categories = $this->incidentCategoryRepository->getAll();
        //dd($incident_summory_data , $categories);
        $incident_summary = [];
        $label_name = [];
        $graph_summary = [];
        $graph_summary[0]['name'] = "Open";
        $graph_summary[0]['color'] = $this->getColor(0);

        $graph_summary[1]['name'] = "In Progress";
        $graph_summary[1]['color'] = $this->getColor(1);

        $graph_summary[2]['name'] = "Closed";
        $graph_summary[2]['color'] = $this->getColor(2);

        $graph_summary[0]['data'] = [];
        $graph_summary[1]['data'] = [];
        $graph_summary[2]['data'] = [];

        $open_incident_arr = array();
        $in_progress_incident_arr = array();
        $close_incident_arr = array();

        foreach ($categories as $s => $category) {

            $subject_open_exist = 0;
            $subject_inprogress_exist = 0;
            $subject_high_exist = 0;

            $open_count = 0;
            $pending_count = 0;
            $closed_count = 0;

            $open_tooltips = '';
            $pending_tooltips = '';
            $closed_tooltips = '';

            foreach ($incident_summory_data as $i => $incident) {

                if ($incident->incident_report_subject->incident_category_id == $category->id) {

                    if ($incident->incident_status == 1) {
                        $subject_open_exist = 1;
                        $open_count += $incident->incident_status_count;
                        $open_tooltips .= $incident->incident_report_subject->subject . ' : <b>' . $incident->incident_status_count . '</b> <br/>';
                    }

                    if ($incident->incident_status == 2) {
                        $subject_inprogress_exist = 1;
                        $pending_count += $incident->incident_status_count;
                        $pending_tooltips .= $incident->incident_report_subject->subject . ' : <b>' . $incident->incident_status_count . '</b> <br/>';
                    }

                    if ($incident->incident_status == 3) {
                        $subject_high_exist = 1;
                        $closed_count += $incident->incident_status_count;
                        $closed_tooltips .= $incident->incident_report_subject->subject . ' : <b>' . $incident->incident_status_count . ' </b> <br/>';
                    }

                }

                // if($incident->subject_id == $subject->id && $incident->incident_status == 2){
                //     $subject_inprogress_exist = 1;
                //     array_push($graph_summary[1]['data'],$incident->incident_status_count);
                // }

                // if($incident->subject_id == $subject->id && $incident->incident_status == 3){
                //     $subject_high_exist = 1;
                //     array_push($graph_summary[2]['data'],$incident->incident_status_count);
                // }

            }

            if ($subject_open_exist == 0) {
                array_push($open_incident_arr, ['y' => 0, 'incidents' => $open_tooltips]);
                //array_push($graph_summary[0]['data'],0);
            } else {
                array_push($open_incident_arr, ['y' => $open_count, 'incidents' => $open_tooltips]);
                //array_push($graph_summary[0]['data'],$open_count);
            }

            if ($subject_inprogress_exist == 0) {
                // array_push($graph_summary[1]['data'],0);
                array_push($in_progress_incident_arr, ['y' => 0, 'incidents' => $open_tooltips]);
            } else {
                // array_push($graph_summary[1]['data'],$pending_count);
                array_push($in_progress_incident_arr, ['y' => $pending_count, 'incidents' => $pending_tooltips]);
            }
            if ($subject_high_exist == 0) {
                // array_push($graph_summary[2]['data'],0);
                array_push($close_incident_arr, ['y' => $closed_count, 'incidents' => $closed_tooltips]);
            } else {
                // array_push($graph_summary[2]['data'],$closed_count);
                array_push($close_incident_arr, ['y' => $closed_count, 'incidents' => $closed_tooltips]);
            }
            array_push($label_name, $category->name);

        }
        $graph_summary[0]['data'] = $open_incident_arr;
        $graph_summary[1]['data'] = $in_progress_incident_arr;
        $graph_summary[2]['data'] = $close_incident_arr;

        /*           foreach($subjects as $s=>$subject){

        $subject_open_exist = 0;
        $subject_inprogress_exist = 0;
        $subject_high_exist = 0;

        foreach($incident_summory_data as $i=>$incident){

        if($incident->subject_id == $subject->id && $incident->incident_status == 1){
        $subject_open_exist = 1;
        array_push($graph_summary[0]['data'],$incident->incident_status_count);
        }

        if($incident->subject_id == $subject->id && $incident->incident_status == 2){
        $subject_inprogress_exist = 1;
        array_push($graph_summary[1]['data'],$incident->incident_status_count);
        }

        if($incident->subject_id == $subject->id && $incident->incident_status == 3){
        $subject_high_exist = 1;
        array_push($graph_summary[2]['data'],$incident->incident_status_count);
        }

        }

        if($subject_open_exist == 0){
        array_push($graph_summary[0]['data'],0);
        }

        if($subject_inprogress_exist == 0){
        array_push($graph_summary[1]['data'],0);
        }
        if($subject_high_exist == 0){
        array_push($graph_summary[2]['data'],0);
        }
        array_push($label_name,$subject->subject);
        }
         */
        $data['chart']['label'] = $label_name;
        $data['chart']['series'] = $graph_summary;
// dd($data);
        return $data;
    }

/**Start** Incident Priority Graph And Table */
    public function getIncidentPriorityData()
    {

        $priorities = IncidentPriorityLookup::orderBy('id')->select('id', 'value')->get();
        $incident_priority_data = collect($this->incidentReportRepository->getIncidentPriority($inputs = []));

        $incident_priority = array();
        $graph['label'] = [];
        $graph['value'] = [];
        $total = 0;
        $incident_priority_matrix_data = [];
        $open = '';
        $inprogress = '';
        $closed = '';
        foreach ($priorities as $p => $prioritie) {
            $incident_priority[$p] = [];

            $incident_priority[$p][0] = 0;
            $incident_priority[$p][1] = 0;
            $incident_priority[$p][2] = 0;
            $incident_priority[$p][3] = 0;

            if ($p == 0) {
                $color = '#eb5669';
            } elseif ($p == 1) {
                $color = '#f5ae60';
            } elseif ($p == 2) {
                $color = '#8fb15a';
            } else {
                $color = '#fff';
            }
            foreach ($incident_priority_data as $i => $incident) {

                if ($prioritie->id == $incident->priority_id) {
                    if ($incident->incident_status == 1) {
                        $incident_priority[$p][1] = $incident_priority[$p][1] + $incident->incident_status_count;
                        // array_push($incident_priority_chart[$p]['data'],$incident->incident_status_count);
                    }

                    if ($incident->incident_status == 2) {
                        $incident_priority[$p][2] = $incident_priority[$p][2] + $incident->incident_status_count;
                        // array_push($incident_priority_chart[$p]['data'],$incident->incident_status_count);
                    }

                    if ($incident->incident_status == 3) {
                        $incident_priority[$p][3] = $incident_priority[$p][3] + $incident->incident_status_count;
                        // array_push($incident_priority_chart[$p]['data'],$incident->incident_status_count);
                    }
                }

                $incident_priority[$p][0] = $incident_priority[$p][3] + $incident_priority[$p][2] + $incident_priority[$p][1];

            }
            $open .= $incident_priority[$p][1] . ',';
            $inprogress .= $incident_priority[$p][2] . ',';
            $closed .= $incident_priority[$p][3] . ',';

            $total = $total + $incident_priority[$p][0];
            array_push($incident_priority_matrix_data, $incident_priority[$p]);

            // array_push($incident_priority_chart[$p][1],$incident_priority[$p][2]);
            // array_push($incident_priority_chart[$p][2],$incident_priority[$p][3]);
            array_push($graph['label'], $prioritie->value);
            //    array_push($graph['value'],$incident_priority[$p]['total']);
            //    array_push($graph['value'],$incident_priority[$p][0]);
            $graph['value'][$p]['y'] = $incident_priority[$p][0];
            $graph['value'][$p]['color'] = $color;
            $graph['value'][$p]['name'] = $prioritie->value;

        }

        $incident_priority_chart[0]['name'] = 'Open';
        $incident_priority_chart[0]['color'] = $this->getColor(0);
        $incident_priority_chart[0]['data'] = array_map('intval', explode(',', rtrim($open, ',')));
        // $incident_priority_chart[0]['data'] = array_walk(explode(',',rtrim($open,',')),'intval');
        $incident_priority_chart[1]['name'] = 'In Progress';
        $incident_priority_chart[1]['color'] = $this->getColor(1);
        $incident_priority_chart[1]['data'] = array_map('intval', explode(',', rtrim($inprogress, ',')));

        $incident_priority_chart[2]['name'] = 'Closed';
        $incident_priority_chart[2]['color'] = $this->getColor(2);
        $incident_priority_chart[2]['data'] = array_map('intval', explode(',', rtrim($closed, ',')));

        $data['fcm_incident_priority']['matrix']['body'] = $incident_priority_matrix_data;
        $data['fcm_incident_priority']['matrix']['head']['x'] = ['Total', 'Open', 'In Progress', 'Closed'];
        $data['fcm_incident_priority']['matrix']['head']['y'] = $graph['label'];

        // $data['fcm_incident_priority']['chart_old'] = $graph;

        $data['fcm_incident_priority']['chart']['series'] = $incident_priority_chart;
        $data['fcm_incident_priority']['chart']['label'] = $graph['label'];
        $data['total_incident_count'] = $total;

        return $data;

    }
/**END** Incident Priority Graph And Table */

    /**
     * Fetching position base gross and net time and finding total hours_worked
     * get position base data.
     * @param  $from_date, $to_date, $customers(From session).
     * @return array .
     */

    public function getPositionGrossAndNetTime()
    {

        $inputs = $this->helper_service->getFMDashboardFilters();
        $pay_periods = $this->pay_period_repository->getPayperiodIdsInRange($inputs['from_date'], $inputs['to_date']);
        $positions = $this->position_lookup_repository->getPositionBasedOnCPID();
        $gross_data = $this->employee_shift_repository->getPositionWiseGrossTime($pay_periods);
        $net_data = $this->employee_shift_cpid_repository->getPositionWiseNetTime($pay_periods);

        $time = [];
        $gross_value = [];
        $position_array = [];
        $gross_array = [];
        $gross_array['name'] = 'Gross';
        $gross_array['color'] = '#eb5669';
        $gross_array['data'] = [];
        $net_array = [];
        $net_array['name'] = 'Net';
        $net_array['color'] = '#f5ae60';
        $net_array['data'] = [];

        $hours_worked = 0;
        foreach ($positions as $pos_key => $position) {
            array_push($position_array, $position->position);
            $time[$pos_key]['label'] = $position->position;
            $time[$pos_key]['net'] = number_format(0, 2);
            $gross_total = 0;
            $net_total = 0;
            $gross_total_hours = 0;
            $net_total_hours = 0;
/** Getting position's gross time in seconds */
            foreach ($gross_data as $key => $gross) {
                if ($gross->trashed_employee->position_id == $position->id) {
                    $gross_total = $gross_total + $gross->total_regular_hours + $gross->total_overtime_hours + $gross->total_statutory_hours;
                }
            }
            // $time[$pos_key]['gross'] = number_format((intval($gross_total) / 3600),2);
            // $gross_total_hours = number_format((intval($gross_total) / 3600),2);

/** convert position's total gross time seconds to hours and minuts format  */
            //$gross_total_hours = $this->convertSecondToHoursMinuts($gross_total);
            $gross_total_hours = $this->dashboardWidgetRepository->convertSecondToNumberFormat($gross_total);
            // $hours_worked = $hours_worked + $gross_total_hours;

/** Getting position's Net time in seconds */
            foreach ($net_data as $key => $net) {
                if ($net->cpid_lookup_with_trash->position_id == $position->id) {
                    $net_total = $net_total + $net->total_hours;
                }
            }
            // $time[$pos_key]['net'] = number_format((intval($net_total) / 3600),2);
            // $net_total_hours = number_format((intval($net_total) / 3600),2);

/** convert position's total net time seconds to hours and minuts format  */
            //$net_total_hours = $this->convertSecondToHoursMinuts($net_total);
            $net_total_hours = $this->dashboardWidgetRepository->convertSecondToNumberFormat($net_total);

            $time[$pos_key] = [$position->position, (float) $gross_total_hours, (float) $net_total_hours];
            $gross_value[$pos_key]['name'] = $position->position;
            $gross_value[$pos_key]['y'] = (float) $net_total_hours;

/**START* Adding client mentioned  colors to graph  */
            $color = $this->dashboardWidgetRepository->getPositionColor($position->position);
            if ($color) {
                $gross_value[$pos_key]['color'] = $color;
            }
/**END* Adding client mentioned  colors to graph  */

/** Calculation of total hours worked based on net time   */
            $hours_worked = $hours_worked + (float) $net_total;
            // array_push($net_array['data'],(float)$net_total_hours);
            // array_push($gross_array['data'],(float)$gross_total_hours);

        }
        // $data['chart']['series'][0] = $gross_array;
        // $data['chart']['series'][1] = $net_array;

/** Data formating for graph and table    */
        $data['chart']['value'] = $gross_value;
        $data['chart']['label'] = $position_array;
        $data['table']['head'] = array('Position', 'Gross', 'Net');
        $data['table']['body'] = $time;
        $data['hours_worked'] = $this->dashboardWidgetRepository->convertSecondToHoursMinuts($hours_worked);

        return $data;
    }

    /**
     * Finding Timesheet Reconciliation Data and format to HTML (Table View).
     * get position base data.
     * @param $customer_id, $from_date, $to_date.
     * @return json .
     */

    // public function getTimesheetReconciliation($customer_id)
    public function getTimesheetReconciliation($customer_id, Request $request)
    {

        $inputs = $this->helper_service->getFMDashboardFilters();

        if ($request->has('from_date')) {
            $inputs['from_date'] = $request->get('from_date');
        }
        if ($request->has('to_date')) {
            $inputs['to_date'] = $request->get('to_date');
        }

        $pay_periods = $this->pay_period_repository->getPayperiodIdsInRange($inputs['from_date'], $inputs['to_date']);
        $inputs['customer_id'] = $customer_id;
        $inputs['pay_periods'] = $pay_periods;

        $cpid_timesheet_data = $this->dashboardWidgetRepository->setTimesheetReconciliationData($inputs);

        $view = View::make('fmdashboard::partials.reconciliation-chart', ['cpid_timesheet_data' => $cpid_timesheet_data]);
        $html = $view->render();
        return response()->json(array('success' => true, 'content' => $html));
    }

    /**
     * Training Widget
     * Course allocated & completed count table & graph
     */
    public function getCourseAllocatedAndCompletedCount(Request $request)
    {
        $data = [];
        $return = [];
        $inputs = $this->helper_service->getFMDashboardFilters();
        $inputs['course_id'] = $request->course_id;
        // $inputs['user_ids'] = data_get($this->user_repo->allocationUserList($inputs['customer_ids']),'*.id');

        $course_count = $this->training_user_course_allocation_repo->getAllocationAndCompletedCount($inputs);

        $allocation_data_count = 0;
        $completed_data_count = 0;
        $pending = 0;
        if (!empty($course_count) >= 1) {

            foreach ($course_count as $count) {
                if ($count->completed == 0) {
                    $pending = $count->data_count;
                } else {
                    $completed_data_count = $count->data_count;
                }
                $allocation_data_count += $count->data_count;
            }

        }
        // $pending = (int)$allocation_data_count -(int)$completed_data_count;
        $data['table']['head'] = ['Type', 'Count'];
        $data['table']['body'][0] = ['Assigned', $allocation_data_count];
        $data['table']['body'][1] = ['Completed', $completed_data_count];
        $data['table']['body'][2] = ['Pending', $pending];

        $data['chart']['label'] = ['Completed', 'Pending'];

        // $data['chart']['value'][0]['name'] = 'Assigned';
        // $data['chart']['value'][0]['color'] = '#ff5320';
        // $data['chart']['value'][0]['y'] = $allocation_data_count;

        $data['chart']['value'][0]['name'] = 'Pending';
        $data['chart']['value'][0]['color'] = '#eb5669';
        $data['chart']['value'][0]['y'] = $pending;

        $data['chart']['value'][1]['name'] = 'Completed';
        $data['chart']['value'][1]['color'] = '#8fb15a';
        $data['chart']['value'][1]['y'] = $completed_data_count;

        $return['fcm_courses'] = $data;
        return $return;
    }

/**Start* of Job Tickets analytics*/
    public function getJobTickets()
    {
        $completed = 0;
        $reject = 0;

        $data = [];
        $label = [];
        $value = [];
        $body = [];
        $total = 0;
        $job_status_data = $this->job_repo->getJobByStatus();
        foreach ($job_status_data as $key => $job) {
            $status = ucfirst($job->status);
            //For Graph
            array_push($label, $status);
            $value[$key]['name'] = $status;
            $value[$key]['y'] = $job->data_count;
            $color = $this->getColor($key);
            if ($color) {
                $value[$key]['color'] = $color;
            }

            //For Table View
            $body[$key] = [$status, $job->data_count];
            $total += $job->data_count;
        }

        $data['chart']['label'] = $label;
        $data['chart']['value'] = $value;
        $data['table']['head'] = array('Status', 'Number');

        if (sizeof($body) != 0) {
            $data['table']['body'] = $body;
            $data['table']['body'][sizeof($body)] = ['Total', $total];
        } else {
            $data['table']['body'][0] = ['No Data Avaliable'];
        }

        return $data;
    }
/**END** Job Tickets analytics*/

    /**
     * Training Widget --All Course--
     * All Course allocated & completed count table & graph
     */
    public function getAllCourseAllocatedAndCompletedCount()
    {
        $data = [];
        $return = [];
        $inputs = $this->helper_service->getFMDashboardFilters();
        //Fetch allocated customers employess allocated.
        // $inputs['user_ids'] = data_get($this->user_repo->allocationUserList($inputs['customer_ids']),'*.id');
        $course_count = $this->training_user_course_allocation_repo->getAllocationAndCompletedCount($inputs);

        $allocation_data_count = 0;
        $completed_data_count = 0;
        $pending = 0;
        $courses = [];
        $key_value = 0;
        $training_data = [];
        $value = [];
        $table = [];
        $barChartPending = [];
        $barChartCompleted = [];

        if (!empty($course_count) >= 1) {

            foreach ($course_count as $key => $count) {
                $course_key = array_search($count->course_with_trashed->course_title, $courses);

                if (!in_array($count->course_with_trashed->course_title, $courses)) {
                    array_push($courses, $count->course_with_trashed->course_title);

                    $training_data[$key_value]['course'] = $count->course_with_trashed->course_title;
                    $pending = 0;
                    $completed = 0;
                    if ($count->completed == 0) {
                        $pending = $count->data_count;
                    } else {
                        $completed = $count->data_count;
                    }
                    $training_data[$key_value]['pending'] = $pending;

                    $training_data[$key_value]['completed'] = $completed;

                    $training_data[$key_value]['allocation'] = ($pending + $completed);

                    $value[$key_value]['name'] = $count->course_with_trashed->course_title;
                    $value[$key_value]['y'] = ($pending + $completed);
                    $color = $this->getColor($key_value);
                    if ($color) {
                        $value[$key_value]['color'] = $color;
                    }

                    // $table[$key_value] = [$count->course_with_trashed->course_title,($pending + $completed),$pending,$completed];
                    $completed_percentage = $this->calculateCompletedPercentage($training_data[$key_value]['allocation'], $training_data[$key_value]['completed']);
                    $table[$key_value] = [$count->course_with_trashed->course_title, $training_data[$key_value]['allocation'], $training_data[$key_value]['pending'], $training_data[$key_value]['completed'], $completed_percentage];

                    $barChartPending[$key_value] = $training_data[$key_value]['pending'];
                    $barChartCompleted[$key_value] = $training_data[$key_value]['completed'];

                    $key_value++;
                } else {
                    if ($count->completed == 0) {
                        $training_data[$course_key]['pending'] += $count->data_count;
                    } else {
                        $training_data[$course_key]['completed'] += $count->data_count;
                    }
                    $training_data[$course_key]['allocation'] += $count->data_count;
                    $completed_percentage = $this->calculateCompletedPercentage($training_data[$course_key]['allocation'], $training_data[$course_key]['completed']);
                    $value[$course_key]['y'] = $training_data[$course_key]['allocation'];
                    $table[$course_key] = [$count->course_with_trashed->course_title, $training_data[$course_key]['allocation'], $training_data[$course_key]['pending'], $training_data[$course_key]['completed'], $completed_percentage];

                    $barChartPending[$course_key] = $training_data[$course_key]['pending'];
                    $barChartCompleted[$course_key] = $training_data[$course_key]['completed'];

                    $color = $this->getColor($course_key);
                    if ($color) {
                        $value[$course_key]['color'] = $color;
                    }

                }

            }

        }

        $barChart[0]['name'] = 'Pending';
        $barChart[0]['color'] = $this->getColor(1);
        $barChart[0]['data'] = $barChartPending;

        $barChart[1]['name'] = 'Completed';
        $barChart[1]['color'] = $this->getColor(2);
        $barChart[1]['data'] = $barChartCompleted;

//used for barchart
        $data['barChart']['label'] = $courses;
        $data['barChart']['series'] = $barChart;
//end of data
        $data['chart']['label'] = $courses;
        $data['chart']['value'] = $value;
        $data['table']['head'] = ['Course', 'Allocated', 'Pending', 'Completed', 'Completed Percentage '];
        $data['table']['body'] = $table;
        // $data['table']['other'] = $training_data;
        return $data;
    }

    public function calculateCompletedPercentage($total, $value)
    {
        return number_format(($value / $total) * 100, 2);
    }

    public function getColor($index)
    {
        $color_code = '';
        $colors = ['#eb5669', '#f5ae60', '#8fb15a', '#191970', '#808000', '#868e96'];
        if ($index >= 0) {
            if ((sizeof($colors) - 1) >= $index) {
                $color_code = $colors[$index];
            }
        }
        return $color_code;

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('supervisorpanel::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('supervisorpanel::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('supervisorpanel::edit');
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
}
