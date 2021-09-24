<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Admin\Repositories\RegionLookupRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Models\Customer;
use Modules\Supervisorpanel\Models\MonitorDashboard;
use Modules\Supervisorpanel\Repositories\IncidentReportRepository;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Illuminate\Support\Carbon;
use Modules\Admin\Repositories\CustomerTypeRepository;


class MonitorDashboardController extends Controller {
    
    protected $regionLookupRepository,$customerEmployeeAllocationRepository,$customerTypeRepository;

    public function __construct(
        RegionLookupRepository $regionLookupRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        IncidentReportRepository $incidentReportRepository,
        SchedulingRepository $schedulingRepository,
        CustomerTypeRepository $customerTypeRepository

    )
    {
        $this->regionLookupRepository = $regionLookupRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->incidentReportRepository = $incidentReportRepository;
        $this->schedulingRepository = $schedulingRepository;
        $this->customerTypeRepository = $customerTypeRepository;

    }

    public function index() {
        $lookups['regionLookup'] = $this->regionLookupRepository->getList();
        $customer_type = $this->customerTypeRepository->getList();
        $customerIds = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
     //   $customers = Customer::select('id','project_number','customer_type_id')->orderBy('project_number')->whereIn('id',$customerIds)->groupBy('customer_type_id')->get();
        $all_customers = Customer::select('id','project_number','customer_type_id','region_lookup_id')->orderBy('project_number')->get()->groupBy(function($item) {
            return $item->customer_type_id;
        });

        $customers = $all_customers->toArray();
      //  dd($customers);
        return view('monitor-dashboard', compact('customers','lookups','customer_type'));
    }

    public function getAllIncidents($id = 0){

       $incidents =  $this->incidentReportRepository->getIncidentReportByDate(Carbon::today(),$id);
       return $incidents;
    }

    public function getCustomerIncident($customer_id){
          $result = $this->incidentReportRepository->getIncidentReportByCustomerId($customer_id);
          return response()->json(array('success' => true,'result' =>$result));
    }

    public function getAllNoShow($id=0){
        $noshows =  $this->schedulingRepository->fetchScheduleComplianceByPayperiods(0,500,'2021-06-17','2021-06-17',null,null,null,null,null,0,$id);
        return $noshows;
    }

    public function getCustomerNoShow($customer_id){
        settype($customer_id, 'array');
        $result = $this->schedulingRepository->fetchScheduleComplianceByPayperiods(0,500, '2021-06-17' ,'2021-06-17', null, $customer_id);
        return response()->json(array('success' => true,'result' =>$result));
   }

    public function viewDashboard($id = null)
    {
        $lookups['regionLookup'] = $this->regionLookupRepository->getList();
        $customer_type = $this->customerTypeRepository->getList();
        $customerIds = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
        $all_customers = Customer::select('id','project_number','customer_type_id','region_lookup_id')
        ->when(($id !== null), function ($query) use ($id) {
            $query->where('region_lookup_id', $id);
         })
        ->orderBy('project_number')->get()->groupBy(function($item) {
            return $item->customer_type_id;
        });


        $customers = $all_customers->toArray();
        $region_lookup_id = $id;
        return view('monitor-dashboard', compact('customers','lookups','customer_type','region_lookup_id'));
    }


}