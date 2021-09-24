<?php

namespace Modules\FMDashboard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Hranalytics\Repositories\CandidateRepository;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\FMDashboard\Repositories\DashboardWidgetUserRepository;
use Modules\Admin\Models\Customer;
use Modules\FMDashboard\Repositories\DashboardWidgetRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseRepository;

class FacilityManagementDashboardController extends Controller
{
    protected $customer_report_repository;
    protected $customer_map_repository;
    protected $dashboardWidgetUserRepository;
    protected $dashboardWidgetRepository;

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(
        CustomerReportRepository $customer_report_repository,
        CustomerMapRepository $customer_map_repository,
        CandidateRepository $candidateRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        DashboardWidgetUserRepository $dashboardWidgetUserRepository,
        DashboardWidgetRepository $dashboardWidgetRepository,
        TrainingCourseRepository $trainingCourseRepository
        )
    {
        $this->customer_report_repository = $customer_report_repository;
        $this->customer_map_repository = $customer_map_repository;
        $this->candidateRepository = $candidateRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->dashboardWidgetUserRepository = $dashboardWidgetUserRepository;
        $this->dashboardWidgetRepository = $dashboardWidgetRepository;
        $this->training_course_repo = $trainingCourseRepository;
    }

    public function index($stc = null, Request $request)
    {
       
        session()->put('from_date', date('Y-m-d', strtotime('-30 days')));
        session()->put('to_date', date('Y-m-d'));

        /*start of customers List*/
        $latest_template = $this->customer_report_repository->getLatestTemplate();
        $customers_arr_per = $this->customer_map_repository->getCustomerMapDetails($latest_template, $stc, $request);
        $customers_arr_temp = $this->customer_map_repository->getCustomerMapDetails($latest_template, $stc = 'stc', $request);
        $customers_arr = array_merge_recursive($customers_arr_temp, $customers_arr_per);

        $shift_flag = 0;
        $customer_score = $customers_arr['customer_score'];
        $customer_score_temp = $customers_arr_temp['customer_score'];
        $customer_score_per = $customers_arr_per['customer_score'];

        $permenentCustomerIds = $this->customerEmployeeAllocationRepository->getAllocatedPermanentCustomers(\Auth::user());
        $permenentCustomers =  Customer::orderBy('client_name')->findMany($permenentCustomerIds);
        //temperory customers
        $stcCustomersIds = $this->customerEmployeeAllocationRepository->getAllocatedStcCustomers(\Auth::user());
        $stcCustomers =  Customer::orderBy('client_name')->findMany($stcCustomersIds);

        /*end of customers List*/
        $dashboardWidgets = $this->dashboardWidgetRepository->getAllUserCanView();
        $userWidgetsArray = $this->dashboardWidgetUserRepository->getAllWidgetIdsOfCurrentUser();
        $customerIds = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user()); 
        $customers = Customer::orderBy('client_name')->findMany($customerIds);

        // Course Listing
        $courses = $this->training_course_repo->getList();
  
        return view('fmdashboard::dashboard',[
                    'customer_score_temp' => $customer_score_temp,
                    'dashboardWidgets' => $dashboardWidgets,
                    'userWidgetsArray' => $userWidgetsArray,
                    'customer_score_per' => $customer_score_per,
                    'customer_score' =>$customer_score,
                    'permenentCustomers' => $permenentCustomers,
                    'stcCustomers' => $stcCustomers,
                    'stc' => $stc,
                    'request' => $request,
                    'shift_flag' => $shift_flag,
                    'customers' => $customers,
                    'dashboardWidgetRepository' => $this->dashboardWidgetRepository,
                    'courses'=>$courses
        ]);

    }

    public function syncWidgetConfig(Request $request)
    {
        $dashboardWidgets =  $request->input('dashboard_widget');
        $this->dashboardWidgetUserRepository->syncSections($dashboardWidgets);
        //todo ::implement
        return response()->json([
            'success' => true
        ]);

    }

    public function timesheetReconciliation($customer_id){
        $html = "
        <div class='row'>
         <div class='col-xs-12 col-sm-12 col-md-12'>
          <div class='row'>
           <table>
           <thead>
            
            <tr>
               
               <th  style='text-align:center;' width='7%' class='position' scope='col'>Position</th>
               
               <th  style='text-align:center;'  width='7%' class='position' scope='col'>CPID</th>
               
                       
               <th  style='text-align:center;' width='7%' class='position' scope='col'>Net Regular Hours</th>
              
               <th  style='text-align:center;' width='7%' class='position' scope='col'>Net Overtime Hours</th>
               
               <th  style='text-align:center;' width='7%' class='position' scope='col'>Net Stat Hours</th>
               
               <th  style='text-align:center;' width='7%' class='position' scope='col'>Regular Pay</th>
               
               <th  style='text-align:center;' width='7%' class='position' scope='col'>OT Pay</th>
               
               <th  style='text-align:center;' width='7%' class='position' scope='col'>Stat Pay</th>
               
               <th  style='text-align:center;' width='7%' class='position' scope='col'>Regualar Bill</th>
               
               <th  style='text-align:center;' width='7%' class='position' scope='col'>Overtime Bill</th>
               
               <th  style='text-align:center;' width='7%' class='position' scope='col'>Stat Bill</th>
               
               <th  style='text-align:center;' width='7%' class='position' scope='col'>Billable OT</th>
               
               <th  style='text-align:center;' width='7%' class='position' scope='col'>Absorbed OT</th>
              
          </tr>
        </thead>
       <tbody>";
        $html .= "<tr>
                   <td style='text-align:center;'  class='position' scope='row'>1</td>
                   <td style='text-align:center;'  class='position' scope='row'>2</td>
            
                   <td style='text-align:center;' class='position' scope='row'>4</td>
                   <td style='text-align:center;' class='position' scope='row'>5</td>
                   <td style='text-align:center;' class='position' scope='row'>6</td>
                   <td style='text-align:center;' class='position' scope='row'>7</td>
                   <td style='text-align:center;' class='position' scope='row'>8</td>
                   <td style='text-align:center;' class='position' scope='row'>9</td>
                   <td style='text-align:center;' class='position' scope='row'>10</td>
                   <td style='text-align:center;' class='position' scope='row'>11</td>
                   <td style='text-align:center;' class='position' scope='row'>12</td>
                   <td style='text-align:center;' class='position' scope='row'>13</td>
                   <td style='text-align:center;' class='position' scope='row'>14</td>
                   </tr>";



        $html .= "</tbody></table> </div></div>
             
         </div>
     ";

        return response()->json(array('success' => true, 'content' => $html));

    }
}
