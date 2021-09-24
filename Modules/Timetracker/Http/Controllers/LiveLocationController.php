<?php

namespace Modules\Timetracker\Http\Controllers;

use App\Services\HelperService;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Customer;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;
use Modules\Timetracker\Repositories\LiveLocationRepository;
use stdClass;

class LiveLocationController extends Controller
{

    protected $customerEmployeeAllocationRepository, $helper_service;
    protected $employeeShiftRepository, $liveLocationRepository;

    public function __construct(
        EmployeeShiftRepository $employeeShiftRepository,
        LiveLocationRepository $liveLocationRepository,
        HelperService $helper_service,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        UserRepository $userRepository,
        User $user_model,
        EmployeeAllocationRepository $employeeAllocationRepo

    ) {
        $this->helper_service = $helper_service;
        $this->employee_shift_repository = $employeeShiftRepository;
        $this->live_location_repository = $liveLocationRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->user_repository = $userRepository;
        $this->user_model = $user_model;
        $this->employee_allocation_repository = $employeeAllocationRepo;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('timetracker::index');
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

    public function getAllActiveCoordinates($searchKey)
    {
        // dd($searchKey);
        $userIds = [];
        $inputs['user_ids'] = [];
        $shift_type_id = SHIFT_TYPE_REGULER_ARRAY;
        if (isset($searchKey['shift_type_flag']) && $searchKey['shift_type_flag'] == 2) {
            $shift_type_id = SHIFT_TYPE_MSP_ARRAY;
        }
        if (isset($searchKey['customerIds'])) {
            $customerIds = array_map('intval', $searchKey['customerIds']);
        } else {
            $customerIds = null;
        }

        if (isset($searchKey['user_id']) && $searchKey['user_id'] != null) {
            $inputs['user_ids'] = array_map('intval', $searchKey['user_id']);
        } else {
            // $userIds = $this->employee_shift_repository->getActiveShiftEmployes($customerIds, $shift_type_id, null);
            // $inputs['user_ids'] = array_unique($userIds);
            $inputs['user_ids'] = $this->getAllocatedUserIds($shift_type_id, $customerIds);
        }
        $inputs['shift_type_id'] = $shift_type_id;
        //dd($searchKey,$shift_type_id,$inputs,$inputs['user_ids']);

        return $this->live_location_repository->getShiftLiveCoordinates($inputs);
    }

    public function getEmployeeLiveCoodinates(Request $request)
    {
        $inputs = $request->all();
        $records = $this->getAllActiveCoordinates($inputs);
        $content = $this->prepaireEmployeeLiveCoordinatesData($records);
        return response()->json([
            'success' => true,
            'content' => $content,
        ], 200);
    }

    public function prepaireEmployeeLiveCoordinatesData($records)
    {
        $resultArr = [];
        foreach ($records as $record) {
            $itemObject = new stdClass();
            $itemObject->phone = '';
            $itemObject->cell_no = '';
            $itemObject->employee_work_email = '';
            $itemObject->image = '';
            $itemObject->employee_rating = '';
            $itemObject->image = '';
            $itemObject->client_name = '';
            $itemObject->supervisor_first_name = '';
            $itemObject->supervisor_last_name = '';
            $itemObject->supervisor_phone = '';
            $itemObject->supervisor_cell_no = '';
            $itemObject->area_manager_first_name = '';
            $itemObject->area_manager_last_name = '';
            $itemObject->area_manager_phone = '';
            $itemObject->area_manager_cell_no = '';
            $itemObject->first_name = '';
            $itemObject->last_name = '';

            $itemObject->pending_dispatch_request = $record->pending_dispatch_request ? $record->pending_dispatch_request : '';
            $itemObject->is_idle = $record->is_idle;
            $itemObject->latitude = $record->latitude ? $record->latitude : '';
            $itemObject->longitude = $record->longitude ? $record->longitude : '';
            $itemObject->address = $record->address ? $record->address : '';
            $itemObject->city = $record->city ? $record->city : '';
            $itemObject->postal_code = $record->postal_code ? $record->postal_code : '';

            if ($record->user) {
                $itemObject->first_name = $record->user->first_name ? $record->user->first_name : '';
                $itemObject->last_name = $record->user->last_name ? $record->user->last_name : '';

                if ($record->user->employee) {
                    $itemObject->phone = $record->user->employee->phone ? $record->user->employee->phone : '';
                    $itemObject->cell_no = $record->user->employee->cell_no ? $record->user->employee->cell_no : '';
                    $itemObject->employee_work_email = $record->user->employee->employee_work_email ? $record->user->employee->employee_work_email : '';
                    $itemObject->image = $record->user->employee->image ? $record->user->employee->image : '';
                    $itemObject->employee_rating = $record->user->employee->employee_rating ? $record->user->employee->employee_rating : '';
                }
            }

            if ($record->employee_shift->shift_payperiod && $record->employee_shift->shift_payperiod->customer) {
                $itemObject->client_name = $record->employee_shift->shift_payperiod->customer->client_name ? $record->employee_shift->shift_payperiod->customer->client_name : '';

                if ($record->employee_shift->shift_payperiod->customer->employeeLatestCustomerSupervisor && $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerSupervisor->supervisor) {
                    $itemObject->supervisor_first_name = $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerSupervisor->supervisor->first_name ? $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerSupervisor->supervisor->first_name : '';
                    $itemObject->supervisor_last_name = $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerSupervisor->supervisor->last_name ? $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerSupervisor->supervisor->last_name : '';

                    if ($record->employee_shift->shift_payperiod->customer->employeeLatestCustomerSupervisor->supervisor->employee) {
                        $itemObject->supervisor_phone = $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerSupervisor->supervisor->employee->phone ? $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerSupervisor->supervisor->employee->phone : '';
                        $itemObject->supervisor_cell_no = $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerSupervisor->supervisor->employee->cell_no ? $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerSupervisor->supervisor->employee->cell_no : '';
                    }
                }

                if ($record->employee_shift->shift_payperiod->customer->employeeLatestCustomerAreaManager && $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerAreaManager->areaManager) {
                    $itemObject->area_manager_first_name = $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerAreaManager->areaManager->first_name ? $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerAreaManager->areaManager->first_name : '';
                    $itemObject->area_manager_last_name = $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerAreaManager->areaManager->last_name ? $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerAreaManager->areaManager->last_name : '';

                    if ($record->employee_shift->shift_payperiod->customer->employeeLatestCustomerAreaManager->areaManager->employee) {
                        $itemObject->area_manager_phone = $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerAreaManager->areaManager->employee->phone ? $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerAreaManager->areaManager->employee->phone : '';
                        $itemObject->area_manager_cell_no = $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerAreaManager->areaManager->employee->cell_no ? $record->employee_shift->shift_payperiod->customer->employeeLatestCustomerAreaManager->areaManager->employee->cell_no : '';
                    }
                }
            }

            $resultArr[] = $itemObject;
        }
        return $resultArr;
    }

    /**
     * Display Map based on Live Shift Location.
     * @return Response
     */
    public function liveShiftLocations(Request $request, $shift_type = null)
    {
        if (!isset($shift_type)) {
            $shift_type = 1;
        } else {
            $shift_type = (int) $shift_type;
        }

        $shift_type_id = SHIFT_TYPE_REGULER_ARRAY;
        if (isset($shift_type) && $shift_type == 2) {
            $shift_type_id = SHIFT_TYPE_MSP_ARRAY;
        }
        $stcCustomers = [];
        $permanentCustomers = [];

        if (Auth::user()->hasAnyPermission(['view_all_live_location', 'admin', 'super_admin'])) {
            //All permenent customers
            $permanentCustomers = Customer::select("id", "project_number", "client_name")
                ->where('stc', 0)->orderBy('client_name')->get();

            //All temperory customers
            $stcCustomers = Customer::select("id", "project_number", "client_name")
                ->where('stc', 1)->orderBy('client_name')->get();
        } elseif (Auth::user()->can(['view_allocated_live_location']) && !Auth::user()->hasAnyPermission(['admin', 'super_admin'])) {

            //Allocated permenent customers
            $permanentCustomerIds = $this->customerEmployeeAllocationRepository->getAllocatedPermanentCustomers(\Auth::user());
            $permanentCustomers = Customer::select("id", "project_number", "client_name")->orderBy('client_name')->findMany($permanentCustomerIds);
            unset($permanentCustomerIds);

            //Allocated temperory customers
            $stcCustomersIds = $this->customerEmployeeAllocationRepository->getAllocatedStcCustomers(\Auth::user());
            $stcCustomers = Customer::select("id", "project_number", "client_name")->orderBy('client_name')->findMany($stcCustomersIds);
            unset($stcCustomersIds);
        }

        // $userIds = $this->employee_shift_repository->getActiveShiftEmployes(null, $shift_type_id, null);
        // $users = User::whereIn('id', $userIds)->get();

        $users = $this->getActiveShiftEmployees($shift_type_id, null, ['id', 'first_name', 'last_name']);
        unset($shift_type_id);

        return view(
            'timetracker::live-shift-location-map',
            [
                'users' => $users,
                'shift_type_flag' => $request->shift_type,
                'permenentCustomers' => $permanentCustomers,
                'stcCustomers' => $stcCustomers,
            ]
        );
    }

    public function getAllocatedUserIds($shift_type_id, $customer_id)
    {
        $userIds = [];
        if (is_null($customer_id)) {
            $userIds = $this->employee_shift_repository->getActiveShiftEmployes(null, $shift_type_id, null);
        } else {
            $userIds = $this->employee_shift_repository->getActiveShiftEmployes($customer_id, $shift_type_id, null);
        }

        if (Auth::user()->can('view_allocated_live_location') && !Auth::user()->can('view_all_live_location')) {
            $allocatedUserIds = $this->employee_allocation_repository->getEmployeeIdAssigned(Auth::user()->id)->toArray();
            $userIds = array_intersect($allocatedUserIds, $userIds);
            unset($allocatedUserIds);
        }
        return array_unique($userIds);
    }

    public function getActiveShiftEmployees($shift_type_id, $customer_id, $selectedFieldsArray = [])
    {
        $userIds = $this->getAllocatedUserIds($shift_type_id, $customer_id);

        if (!empty($selectedFieldsArray)) {
            $query = $this->user_model->select($selectedFieldsArray);
        } else {
            $query = $this->user_model;
        }
        return $query->whereActive(true)
            ->whereIn('id', $userIds)
            ->orderBy('first_name')
            ->get();
    }

    public function listAllActiveShiftEmployees(Request $request)
    {
        $shift_type_id = SHIFT_TYPE_REGULER_ARRAY;
        if (isset($request->shift_type_id) && $request->shift_type_id == 2) {
            $shift_type_id = SHIFT_TYPE_MSP_ARRAY;
        }

        return response()->json([
            'success' => true,
            'content' => $this->getActiveShiftEmployees($shift_type_id, $request->customer_id, ['id', 'first_name', 'last_name']),
        ], 200);
    }

}
