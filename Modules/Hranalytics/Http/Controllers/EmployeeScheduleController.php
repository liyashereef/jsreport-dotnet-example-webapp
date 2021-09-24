<?php

namespace Modules\Hranalytics\Http\Controllers;

use App\Services\HelperService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\ScheduleShiftTimingsRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Hranalytics\Http\Requests\EmployeeUnavailabilityRequest;
use Modules\Timetracker\Models\EmployeeAvailability;
use Modules\Timetracker\Models\EmployeeUnavailability;
use Modules\Timetracker\Repositories\EmployeeAvailabilityRepository;

class EmployeeScheduleController extends Controller
{
    protected $userrepository, $shifttimings, $helperService, $employeeAllocationRepository;

    public function __construct(EmployeeAvailabilityRepository $employeeAvailabilityRepository, UserRepository $userrepository, EmployeeAllocationRepository $employeeAllocationRepository, ScheduleShiftTimingsRepository $shifttimings, HelperService $helperService)
    {
        $this->userrepository = $userrepository;
        $this->shifttimings = $shifttimings;
        $this->helperService = $helperService;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
        $this->employeeAvailabilityRepository = $employeeAvailabilityRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('hranalytics::index');
    }

    public function entryform(Request $request)
    {
        $shiftarray = config("globals.array_shift_day");
        $shiftcount = count($shiftarray);
        $array_shifts = $this->shifttimings->getAll();
        $rowlength = count($array_shifts);
        return view("hranalytics::employee-schedule-entry", compact('shiftarray', 'array_shifts', 'shiftcount', 'rowlength'));
    }

    public function employeeList($type)
    {
        $id_arr = [];
        if ($type == 'nav-availability-tab') {
            if (\Auth::user()->can('view_all_employee_availability')) {
                $id_arr = $this->userrepository->getAllUsersID();
            } else if (\Auth::user()->can('view_allocated_employee_availability')) {
                $id_arr = $this->employeeAllocationRepository->getEmployeeAssigned(\Auth::user()->id)->pluck('user_id')->toArray();
            }
        } else {
            if (\Auth::user()->can('view_all_employee_unavailability')) {
                $id_arr = $this->userrepository->getAllUsersID();
            } else if (\Auth::user()->can('view_allocated_employee_unavailability')) {
                $id_arr = $this->employeeAllocationRepository->getEmployeeAssigned(\Auth::user()->id)->pluck('user_id')->toArray();
            }
        }

        // $employeeslist = $this->userrepository->getUsersDropdownList($id_arr, ['admin', 'super_admin']);
        $role_except = ['admin', 'super_admin', 'client'];
        $user_details_arr = User::with('employee')->whereActive(true);
        $user_details_arr->whereIn('id', $id_arr);

        $user_details_arr->when(($role_except != null), function ($user_details_arr) use ($role_except) {
            $user_details_arr->with('roles');
            $user_details_arr->whereHas('roles', function ($user_details_arr) use ($role_except) {
                $user_details_arr->whereNotIn('name', $role_except);
            });
        });
        $user_details_arr = $user_details_arr->orderBy('first_name')->get();

        $users_arr = array();
        foreach ($user_details_arr as $key => $users) {
            $emp_no = $users->employee['employee_no'] ? ' (' . $users->employee['employee_no'] . ')' : '';
            $name = $users['full_name'] . $emp_no;
            $id = $users['id'];
            $users_arr[] = array('id' => $id, 'name' => $name);
        }
        return response()->json(array('success' => true, 'employee_id' => $users_arr));
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
        try {
            DB::beginTransaction();
            $check_box = json_decode($request->checkbox_values, true);
            $data = array();
            $data['employee_id'] = $request->employee_id;
            EmployeeAvailability::where('employee_id', $request->employee_id)->delete();
            foreach ($check_box as $key => $item) {
                $shift_string = explode('-', $key);
                $shift = trim($shift_string[0]);
                $shift_id = $this->shifttimings->getId($shift);
                $data['shift_timing_id'] = $shift_id;
                $data['week_day'] = $item;
                $data['created_by'] = \Auth::user()->id;
                EmployeeAvailability::create($data);
            }
            DB::commit();
            $lastUpdatedData = $this->employeeAvailabilityRepository->getLastUpdatedDataByUser($request->employee_id);
            return response()->json(array('success' => true, 'employee_id' => $request->employee, 'last_updated_data' => $lastUpdatedData));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }

/**
 * Show the specified resource.
 * @return Response
 */
    public function getSchedule(Request $request)
    {
        try {
            DB::beginTransaction();
            $flag = false;
            $shiftarray_name = array();
            $availability = EmployeeAvailability::where('employee_id', $request->employee_id)->get();
            $shiftarray = config("globals.array_shift_day");
            foreach ($availability as $key => $value) {
                $array_shifts_name = $this->shifttimings->getName($value->shift_timing_id);
                $shiftarray_name[] = strtolower($array_shifts_name . '-' . $shiftarray[$value->week_day]);
            }
            if (\Auth::user()->can('update_all_employee_availability')) {
                $id_arr = $this->userrepository->getAllUsersID();
                $flag = in_array($request->employee_id, $id_arr) ? true : false;
            } else if (\Auth::user()->can('update_allocated_employee_availability')) {
                $id_arr = $this->employeeAllocationRepository->getEmployeeAssigned(\Auth::user()->id)->pluck('user_id')->toArray();
                $flag = in_array($request->employee_id, $id_arr) ? true : false;

            }
            $lastUpdatedData = $this->employeeAvailabilityRepository->getLastUpdatedDataByUser($request->employee_id);
            DB::commit();
            return response()->json(array('success' => true, 'data' => $shiftarray_name, 'flag' => $flag, 'last_updated_data' => $lastUpdatedData));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function unavailablility(EmployeeUnavailabilityRequest $request)
    {
        $data['employee_id'] = $request->employee_id;
        $data['from'] = $request->from;
        $data['to'] = $request->to;
        $result = EmployeeUnavailability::create($data);
        $lastUpdatedData = $this->employeeAvailabilityRepository->getLastUpdatedDataByUser($request->employee_id);
        return response()->json(array('success' => true, 'employee_id' => $result->employee_id, 'last_updated_data' => $lastUpdatedData));
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
