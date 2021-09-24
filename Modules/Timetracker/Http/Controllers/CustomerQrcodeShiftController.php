<?php

namespace Modules\Timetracker\Http\Controllers;

use Carbon\Carbon;
use DataTables;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Timetracker\Repositories\CustomerQrcodeRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Models\User;

class CustomerQrcodeShiftController extends Controller
{
    protected $customerQrcodeRepository;
    public function __construct(CustomerRepository $customerRepository, UserRepository $userRepository, User $userModel, CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository, EmployeeAllocationRepository $employeeAllocationRepository,
    CustomerQrcodeRepository $customerQrcodeRepository)
    {
        $this->customerqrcodeRepository = $customerQrcodeRepository;
        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository;
        $this->usermodel = $userModel;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        $current = Carbon::now();

        $enddate = $current->toDateString();
        $startdate = $current->addDays(-2)->toDateString();
        $user = \Auth::user();
        if ($user->can('view_all_customer_qrcode_summary')) {
            $user_list = $this->userRepository->getUserLookup(null,['admin','super_admin'],null,true,null,true)
            ->orderBy('first_name', 'asc')->get();
            $customer_details_arr = $this->customerRepository->getProjectsDropdownList('all');
        }else if($user->can('view_allocated_customer_qrcode_summary')){
            $employees = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id);
            $user_list = $this->usermodel
            ->whereIn('id',$employees)->get();
            $customer_details_arr = $this->customerRepository->getProjectsDropdownList('allocated');
        }else{
            $user_list = [];
            $customer_details_arr = [];
        }
        return view('timetracker::customer-qrcode-shifts', compact('startdate', 'enddate', 'user_list', 'customer_details_arr'));
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */

    public function getList($startdate = null, $enddate = null, $emp_id = null, $client_id = null )
    {
        $list = $this->customerqrcodeRepository->list($startdate, $enddate, $emp_id, null, $client_id);
        return DataTables::eloquent($list)
            ->setTransformer(function ($item) {
                return [
                    'id' => (int) $item->id,
                    'created_date' => date('F d, Y', strtotime($item->created_at)),
                    'created_at' => strtotime($item->created_at),
                    'checkpoint' => $item->checkpoint,
                    'employee_details' => $item->employeeDetails,
                    'employee_lastname' => $item->employeeDetails,
                    'employee_no' => $item->shifts->shift_payperiod->trashed_user->trashedEmployee->employee_no,
                    'customer_details' => $item->shifts->shift_payperiod->trashed_customer->client_name,
                    'total_count' => $item->total_count,
                    'expected_attempts' => $item->expected_attempts,
                    'missed_count_percentage' => (float) number_format($item->missed_count_percentage, 2, '.', ','),
                ];
            })
            ->toJson();
    }
}
