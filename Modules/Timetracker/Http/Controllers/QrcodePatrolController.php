<?php

namespace Modules\Timetracker\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Auth;
use Modules\Timetracker\Repositories\QrcodeLocationRepository;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Timetracker\Jobs\QrpatrolDailyActivityReport;
class QrcodePatrolController extends Controller
{

    public function __construct(User $userModel, EmployeeAllocationRepository $employeeAllocationRepository, QrcodeLocationRepository $qrcodeRepository, CustomerRepository $customerRepository, UserRepository $userRepository, CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository)
    {
        $this->qrcodeRepository = $qrcodeRepository;
        $this->customerRepository = $customerRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->userRepository = $userRepository;
        $this->usermodel = $userModel;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
    }

    function list() {
        $user = \Auth::user();
        if ($user->can('view_all_qrcode_data')) {
            $employeeLookup = $this->userRepository->getUserLookup(null,['admin','super_admin'],null,true,null,true)
            ->orderBy('first_name', 'asc')->get();
            $customer_details_arr = $this->customerRepository->getProjectsDropdownList('all');
        }else if($user->can('view_allocated_qrcode_data')){
            $employees = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id);
            $employeeLookup = $this->usermodel
            ->whereIn('id',$employees)->get();
            $customer_details_arr = $this->customerRepository->getProjectsDropdownList('allocated');
        }else{
            $employeeLookup = [];
            $customer_details_arr = [];
        }
        return view('timetracker::qrcode-patrol-trip', compact('employeeLookup', 'customer_details_arr'));
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $client_id = $request->get('client_id')?:null;
        $employee_id = $request->get('employee_id')?:null;
        $fromdate = $request->get('fromdate');
        $todate = date("Y-m-d", strtotime("+1 day", strtotime($request->get('todate'))));
        $limit = null;
        $trips = $this->qrcodeRepository->index($limit, $fromdate, $todate, $client_id, $employee_id);
        return datatables()->of($trips)->toJson();
    }
    public function qrcodeMapView($qrcodeshift_id)
    {
        $coordinates = $this->qrcodeRepository->getCoordinates($qrcodeshift_id);
        return view('timetracker::qrcode-patrol-trip-map', compact('coordinates'));
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('timetracker::create');
    }

    /**
     *To get qr patrol details
     */
    public function getQrpatrolDetails($shiftid)
    {
        return $this->qrcodeRepository->getQrpatrolList($shiftid);
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
        return view('timetracker::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('timetracker::edit');
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
   /**
     * Get Qr patrol daily activity report
     * @return Response
     */
    public function qrPatroldailyActivity()
    {
        QrpatrolDailyActivityReport::dispatch();

    }
}
