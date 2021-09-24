<?php

namespace Modules\Timetracker\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use Session;
use Carbon\Carbon;

use Illuminate\Routing\Controller;
use Modules\Admin\Models\Customer;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Timetracker\Models\SatelliteTrackingSetting;
use Modules\Timetracker\Repositories\MobileSecurityPatrolFenceDataRepository;
use Modules\Timetracker\Repositories\MobileSecurityPatrolRepository;
use Modules\Timetracker\Repositories\TripRepository;
use Modules\Timetracker\Repositories\MobileSecurityPatrolFenceSummaryRepository;
use Modules\Timetracker\Models\MobileSecurityPatrolFenceData;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
class MobileSecurityPatrolController extends Controller
{
    /**
     * The Repository instance.
     *
     * @var \Modules\Timetracker\Repositories\TimetrackerRepository
     */
    protected $timetrackerRepository;
    protected $tripRepository;
    protected $mobileSecurityPatrolFenceDataRepository;
    protected $customerEmployeeAllocationRepository;
    protected $mobileSecurityPatrolFenceSummaryRepository;
    protected $customerRepository;

    /**
     * Create Repository instance.
     *
     * @param  \Modules\Timetracker\Repositories\MobileSecurityPatrolRepository $timetrackerRepository
     * @return void
     */
    public function __construct(
        MobileSecurityPatrolRepository $mobileSecurityPatrolRepository,
        TripRepository $tripRepository,
        MobileSecurityPatrolFenceDataRepository $mobileSecurityPatrolFenceDataRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        MobileSecurityPatrolFenceSummaryRepository $mobileSecurityPatrolFenceSummaryRepository,
        CustomerRepository $customerRepository,
        UserRepository $userRepository,
        User $userModel,
        EmployeeAllocationRepository $employeeAllocationRepository
    ) {
        $this->mobileSecurityPatrolRepository = $mobileSecurityPatrolRepository;
        $this->tripRepository = $tripRepository;
        $this->mobileSecurityPatrolFenceDataRepository = $mobileSecurityPatrolFenceDataRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->mobileSecurityPatrolFenceSummaryRepository = $mobileSecurityPatrolFenceSummaryRepository;
        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository;
        $this->usermodel = $userModel;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $fromdate = null;
        $todate = null;
        if($request->get("fromdate")){
            $fromdate = $request->get("fromdate");
        }
        if($request->get("todate")){
            $todate = date('Y-m-d', strtotime($request->get("todate") . ' +1 day'));
        }

        if(session::get('previousfrom')!=null){

            if($fromdate!= session::get('previousfrom')){
                session::put('previousfrom',$fromdate);
                \Cache::forget('key'.Auth::user()->id);

            }
        }else
        {
            session::put('previousfrom',$fromdate);
        }

        if(session::get('previousto')!=null){
            if($todate!= session::get('previousto')){
                session::put('previousto',$todate);
                \Cache::forget('key'.Auth::user()->id);

            }
        }else
        {
            session::put('previousto',$todate);
        }

        if (\Cache::has('key'.Auth::user()->id)) {
            $tripcollection = \Cache::get('key'.Auth::user()->id);
        } else {
            $tripcollection = $this->tripRepository->index($limit=null,$fromdate,$todate);
            \Cache::add('key'.Auth::user()->id, $tripcollection, 10);
        }

        return datatables()->of($tripcollection)->toJson();




    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function list()
    {
        \Cache::forget('key'.Auth::user()->id);
        return view('timetracker::mobile-patrol-trip');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function mapView($trip_id)
    {
        $coordinates = $this->tripRepository->getCoordinates($trip_id);
        return view('timetracker::mobile-patrol-trip-map', compact('coordinates'));
    }

    public function tripDetailsView($trip_id)
    {
        $tripDetails = $this->tripRepository->getTripDetails($trip_id);
        return view('timetracker::mobile-patrol-trip-details', compact('tripDetails'));
    }
    /**
     * Display Map based on Shift.
     * @return Response
     */
    public function fullshift_mapView($shift_id)
    {

        $coordinates = $this->tripRepository->getTripPatrol($shift_id);
        return view('timetracker::mobile-patrol-shift-map', compact('coordinates'));
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function mobilesecuritypatrol()
    {
        return view('timetracker::mobile-patrol-trip');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function mobilepatrol()
    {
        if (\Auth::user()->hasAnyPermission(['view_all_mobile_security_patrol','admin', 'super_admin'])) {
            $customer_details_arr = $this->customerRepository->getProjectsDropdownList('all');
        }
        else{
            $customer_details_arr = $this->customerRepository->getProjectsDropdownList('allocated');
        }
        return view('timetracker::mobile-patrol',compact('customer_details_arr'));
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function mobilepatrollist(Request $request)
    {
        $clientId = $request->get('client_id');
        $trips =  $this->mobileSecurityPatrolRepository->listPatrol($clientId);

        return datatables()->of($trips)->toJson();
    }

    /**
     * Geofence view
     */
    public function geofence()
    {
        $user = \Auth::user();
        if ($user->hasAnyPermission(['view_all_satellite_tracking','admin', 'super_admin'])) {
            $employeeLookup = $this->userRepository->getUserLookup(null,['admin','super_admin'],null,true,null,true)
            ->orderBy('first_name', 'asc')->get();
            $customer_details_arr = $this->customerRepository->getProjectsDropdownList('all');
        }else {
            $employees = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id);
            $employeeLookup = $this->usermodel
            ->whereIn('id',$employees)->get();
            $customer_details_arr = $this->customerRepository->getProjectsDropdownList('allocated');
        }
        $key = "satellitetracking-".Auth::user()->id;
        \Cache::forget($key);
        return view('timetracker::msp.geofence-list',compact('employeeLookup','customer_details_arr'));
    }

    /**
     * Geofence listing
     */
    public function geofenceList(Request $request)
    {
        $client_id = $request->get('client_id')?:null;
        $employee_id = $request->get('employee_id')?:null;
        $fromdate = $request->get('fromdate');
        $todate = date("Y-m-d",strtotime("+1 day",strtotime($request->get('todate'))));
        $cacheddata = $request->get('cacheddata');
        $msp = $this->mobileSecurityPatrolFenceDataRepository->getGeofenceList($fromdate,$todate,$cacheddata,$client_id,$employee_id);
       return datatables()->of($msp)->toJson();
    }

    public function geofenceDataList(Request $request)
    {
        //->addselect(\DB::raw("select title from `geo_fences` where id='".$request->fenceid."' "))

        $fencedetails = \DB::table('geo_fences')->select('title')->where('id',$request->fenceid)->first();
        $title = $fencedetails->title;

        $fencedata[0] = MobileSecurityPatrolFenceData::select('*')->where(['shift_id'=>$request->shiftid,'fence_id'=>$request->fenceid])->get();
        $fencedata[1] = $title;
        return $fencedata;
    }
    //ui route
    public function geofenceCustomerSummary()
    {
        return view('timetracker::msp.geofence-customer-summary');
    }

    public function geofenceCustomerSummaryDesign()
    {

        $customerIds = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
        $customers = Customer::orderBy('client_name')->findMany($customerIds);

        $permenentCustomerIds = $this->customerEmployeeAllocationRepository
            ->getAllocatedPermanentCustomers(\Auth::user());
        $permenentCustomers =  Customer::orderBy('client_name')->findMany($permenentCustomerIds);
        //temperory customers
        $stcCustomersIds = $this->customerEmployeeAllocationRepository->getAllocatedStcCustomers(\Auth::user());
        $stcCustomers =  Customer::orderBy('client_name')->findMany($stcCustomersIds);

        $customerExtraInfo = [];
        foreach ($customers as $customer) {
            $manInfo = $this->customerRepository->getCustomerWithMangers($customer->id);
            $manInfo['customerId'] = $customer->id;
            $manInfo['details'] = [];
            $customerExtraInfo[] = $manInfo;
        }
        //marker configurations
        $markerConfigurations = SatelliteTrackingSetting::all()
            ->map(function ($item) {
                return $item->only(['min', 'max', 'color']);
            })->toJson();

        return view('timetracker::msp.dashboard', [
            'permenentCustomers' => $permenentCustomers,
            'stcCustomers' => $stcCustomers,
            'customers' => $customers,
            'markerConfigurations' => $markerConfigurations,
            'customerExtraInfo' => $customerExtraInfo
        ]);
    }

    public function getSatelliteTrackingDashboardMapData(Request $request)
    {

        $inputs = $request->all();
        if (!$request->filled('customerIds')) {
            $inputs['customerIds'] = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
        }
        if (!$request->filled('startDate')) {
            $inputs['startDate'] = null;
        }
        if (!$request->filled('endDate')) {
            $inputs['endDate'] = null;
        }

        //fetch data.
        $data['map_data'] = $this->mobileSecurityPatrolFenceSummaryRepository->getDashboardMapData($inputs);
        return $data;
    }
    public function getSatelliteTrackingDashboardMapDatachildrows(Request $request){
        $fenceid = $request->fenceid;
        $startdate = $request->startdate;

        $enddate = $request->enddate;
        $fencedataresults = MobileSecurityPatrolFenceData::select('*')
                            ->where([['fence_id',$fenceid],['created_at','>=',$startdate],['created_at','<=',$enddate]])
                            ->get();
        $array = [];
        $i=0;
        foreach ($fencedataresults as $fencedata) {
            $shiftid=0;
            $employeeno = null;
            $employeename = 0;
            $date=null;
            $shiftstart="";$shiftend="";
            $difference="";$start_coordinate=null;$latitude=null;$longitude=null;
            $id = $fencedata->id;
            $shiftid = $fencedata->shift_id;

            $shiftoriginalstart = $fencedata->shift->start;
            $shiftoriginalend = $fencedata->shift->end;

            $employee = $fencedata->shift->shift_payperiod->trashed_employee;
            if($fencedata->shift->shift_payperiod->trashed_employee){
                $employeeno = $employee->employee_no;
            }
            else{
                $employeeno = "";
            }

            $userdetail = $fencedata->shift->shift_payperiod->trashed_user;
            if($userdetail){
                $employeename = $userdetail->first_name." ".$userdetail->last_name;
            }else{
                $employeename = "";
            }

            if($fencedata->time_entry!=""){
                $timeentry = $fencedata->time_entry;
                $timeexit = $fencedata->time_exit;
                $date = date("d-M-y",strtotime($timeentry));
                $shiftstart = date("g:iA",strtotime($timeentry));
                $shiftend = date("g:iA",strtotime($timeexit));

                $start  = new Carbon($timeentry);
                $end    = new Carbon($timeexit);
                $difference = $start->diff($end)->format('%H:%I:%S');;
            }
            else{
                $timeentry = $shiftoriginalstart;
                $timeexit = $shiftoriginalend;

                $shiftstart = date("g:iA",strtotime($timeentry));
                $shiftend = date("g:iA",strtotime($timeexit));
                $start  = new Carbon($timeentry);
                $end    = new Carbon($timeexit);
                $difference = "00:00:00";;
            }
            $start_coordinate = $fencedata->start_coordinate;
            if($start_coordinate){
                $latitude = $start_coordinate->latitude;
                $longitude = $start_coordinate->longitude;
            }

            $array[$i]=[$id,$shiftid,$employeeno,$employeename,$date,$shiftstart,$shiftend,
                        $difference,$fencedata->start_coordinate_id,$latitude,$longitude];
                        $i++;
        }
        return json_encode($array);
    }
    public function getSatelliteTrackingDashboardTableData(Request $request)
    {
        $inputs = $request->all();
        if (!$request->filled('customerIds')) {
            $inputs['customerIds'] = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
        }
        if (!$request->filled('startDate')) {
            $inputs['startDate'] = null;
        }
        if (!$request->filled('endDate')) {
            $inputs['endDate'] = null;
        }

        //fetch data.
        $table_data = $this->mobileSecurityPatrolFenceSummaryRepository->getDashboardTableData($inputs);
        //convert data table format.
        return datatables()->of($table_data)->addIndexColumn()->toJson();
    }

    /**
     *To get summary child list
     */
    public function getGeoSummary($shiftid)
    {
       return  $this->mobileSecurityPatrolFenceDataRepository->getGeoSummaryList($shiftid);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        // return view('timetracker::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    { }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        //return view('timetracker::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        //return view('timetracker::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    { }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    { }
}
