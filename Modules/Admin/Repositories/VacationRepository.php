<?php

namespace Modules\Admin\Repositories;

use DB;
use File;
use Config;
use Carbon\Carbon;
use Modules\Admin\Models\User;
use App\Services\HelperService;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\TimeOffRequestTypeLookup;

class VacationRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct()
    {
        // $this->model = new VacationEntitlement();
    }

    public function basicRun()
    {
        $timeOffTypes = TimeOffRequestTypeLookup::get();
        $date = \Carbon::now(); // for the last quarter requirement
        $quarterStart = $date->startOfQuarter(); // the actual start of quarter method
        $quarterNo = intval(floor(intval($quarterStart->format("m")) / 3) + 1);
        $nextCroneDate = \Carbon::parse($date->endOfQuarter())->addDays(1); // the actual
        // dd($quarterNo, $nextCroneDate);

        foreach ($timeOffTypes as $timeOffType) {
            dump($timeOffType->timeOffSettings);
        }
    }

    public function initialRun()
    {
        $managementRoles = ManagementRoles::get()->pluck("role_id")->toArray();
        $userEntitlement = VacationEntitlement::select("*", \DB::raw("(less_than_years*12) as yearInMonth"))
            ->where("management", 0)
            ->get();

        $managementEntitlement = VacationEntitlement::select("*", \DB::raw("(less_than_years*12) as yearInMonth"))
            ->where("management", 1)
            ->get();
        $entitlementArray = [];
        $entitlementArrayManagement = [];
        $finalEmployeeArray = [];
        $lastArray = [];
        $lastUser = "";
        foreach ($userEntitlement as $entitlement) {
            $entitlementArray[$entitlement->yearInMonth] = [
                "id" => $entitlement->id,
                "quarterly_leaveentitlement" => $entitlement->quarterly_leaveentitlement
            ];
        }


        $pluckData =
            $userEntitlement->pluck("yearInMonth");

        foreach ($managementEntitlement as $entitlement) {
            $entitlementArrayManagement[$entitlement->yearInMonth] = [
                "id" => $entitlement->id,
                "quarterly_leaveentitlement" => $entitlement->quarterly_leaveentitlement
            ];
        }
        $pluckDataManagement =
            $managementEntitlement->pluck("yearInMonth");

        $users = User::whereHas("roles", function ($q) {
            return $q->whereNotIn("name", [
                "super_admin", "admin", "client"
            ]);
        })->whereHas("employee")->whereIn("active", [0, 1])->get();
        $today = \Carbon::now();
        $countCheck = 0;
        foreach ($users as $user) {
            $roles = 0;
            if (isset($user->roles)) {
                $roles = ($user->roles)[0]->id;
            }
            $userId = $user->id;

            $totalLeaveForEmployee = 0;;
            $employee_doj = $user->employee->employee_doj;

            $diff_in_months = $today->diffInMonths($employee_doj);

            if ($employee_doj != "") {
                if ($diff_in_months < 4) {
                    $nextExpectedMonth = Carbon::parse($employee_doj)->addMonth(4);

                    $finalEmployeeArray[$userId] = [
                        "user_id" => $userId,
                        "entitled_vacation" => 0,
                        "date_of_join" => $employee_doj,
                        "initial_vacation_cron_date" => Carbon::now(),
                        "last_vacation_cron_date" => Carbon::now(),
                        "quarterly_vacation_update_date" => $nextExpectedMonth
                    ];
                }
                // echo $userId . "-" . $diff_in_months . "-" . $employee_doj . "<br/>";
                for ($i = 1; $i <= $diff_in_months; $i++) {

                    $loopedMonth = Carbon::parse($employee_doj)->addMonth($i);
                    // echo $userId . "-" . $loopedMonth . "<br/>";
                    if ($i % 4 == 0) {
                        if (in_array($roles, $managementRoles)) {
                            $filterCollectionIndex = $pluckDataManagement->filter(function ($item) use ($i) {
                                return $item >= $i;
                            })->first();
                        } else {
                            $filterCollectionIndex = $pluckData->filter(function ($item) use ($i) {
                                return $item >= $i;
                            })->first();
                        }

                        $employeeQuarterLeave = isset($entitlementArray[$filterCollectionIndex]) ?
                            $entitlementArray[$filterCollectionIndex]["quarterly_leaveentitlement"] : 0;
                        // dd($filterCollectionIndex, $entitlementArray[$filterCollectionIndex]);
                        $totalLeaveForEmployee = $totalLeaveForEmployee + $employeeQuarterLeave;
                        // echo "<p>" . $employeeQuarterLeave . " -  Quarter " . $i / 4 . "</p>";
                        $nextExpectedMonth = Carbon::parse($employee_doj)->addMonth($i + 2);
                        $finalEmployeeArray[$userId] = [
                            "user_id" => $userId,
                            "entitled_vacation" => $totalLeaveForEmployee,
                            "initial_vacation_cron_date" => Carbon::now(),
                            "last_vacation_cron_date" => Carbon::now(),
                            "quarterly_vacation_update_date" => $nextExpectedMonth
                        ];
                    }
                }
            }
        }
        foreach ($finalEmployeeArray as $key => $value) {
            try {
                DB::beginTransaction();
                Employee::where(
                    "user_id",
                    $value["user_id"]
                )->update([
                    "entitled_vacation" => $value["entitled_vacation"],
                    "initial_vacation_cron_date" => $value["initial_vacation_cron_date"],
                    "last_vacation_cron_date" => $value["last_vacation_cron_date"],
                    "quarterly_vacation_update_date" => $value["quarterly_vacation_update_date"]
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
            }
        }
    }
    public function dailyRun()
    {
        $timeOffArray = [];
        $requestTypeArray = [];
        $timeOffTypes = TimeOffRequestTypeLookup::whereHas('timeOffSettings', function ($q) {
            return $q->where("active", 1);
        })->get();
        foreach ($timeOffTypes as $timeOffType) {
            $timeOffArray[$timeOffType->id] = $timeOffType->id;
            foreach ($timeOffType->timeOffSettings as $timeOffSetting) {
                $requestTypeArray[] = [
                    'time_off_type_id' => $timeOffType->id,
                    'time_off_setting_id' => $timeOffSetting->id,
                    'min_experience' => floatval($timeOffSetting->min_experience),
                    'no_of_leaves' => $timeOffSetting->no_of_leaves,
                    'time_off_request_type_id' => $timeOffSetting->time_off_request_type_id,
                    // 'accrual_day',
                    // 'accrual_month',
                    'reset_term' => $timeOffSetting->reset_term,
                    'reset_day' => $timeOffSetting->reset_day,
                    'carry_forward' => $timeOffSetting->carry_forward,
                    'carry_forward_percentage' => $timeOffSetting->carry_forward_percentage,
                    'carry_forward_expires_in_month' => $timeOffSetting->carry_forward_expires_in_month,
                    'encashment_percentage' => $timeOffSetting->encashment_percentage,
                ];
            }
        }
        $timeOffArray = array_values($timeOffArray);
        $users = User::where("active", 1)->whereHas("employee", function ($q) {
            return $q->whereNotNull("employee_doj");
        })->get();
        $today = \Carbon::now();
        $dbCollection = collect($requestTypeArray);
        foreach ($users as $user) {
            $userId = $user->id;
            $employeeDoj = $user->employee->employee_doj;
            $diffInMonths = $today->diffInMonths($employeeDoj);
            $filterData = $dbCollection->filter(function ($item) use ($diffInMonths) {
                return (data_get($item, 'min_experience') <= $diffInMonths);
            })->sortByDesc("min_experience")->first();
            // dd($filterData);
        }
    }
    public function dailyRunOldlogic()
    {
        $managementRoles = ManagementRoles::get()->pluck("role_id")->toArray();

        $userEntitlement = VacationEntitlement::select("*", \DB::raw("(less_than_years*12) as yearInMonth"))
            ->where("management", 0)
            ->get();
        $managementEntitlement = VacationEntitlement::select("*", \DB::raw("(less_than_years*12) as yearInMonth"))
            ->where("management", 1)
            ->get();
        $entitlementArray = [];
        $entitlementArrayManagement = [];

        $finalEmployeeArray = [];
        $lastArray = [];
        $lastUser = "";
        foreach ($userEntitlement as $entitlement) {
            $entitlementArray[$entitlement->yearInMonth] = [
                "id" => $entitlement->id,
                "quarterly_leaveentitlement" => $entitlement->quarterly_leaveentitlement
            ];
        }
        $pluckData =
            $userEntitlement->pluck("yearInMonth");

        foreach ($managementEntitlement as $entitlement) {
            $entitlementArrayManagement[$entitlement->yearInMonth] = [
                "id" => $entitlement->id,
                "quarterly_leaveentitlement" => $entitlement->quarterly_leaveentitlement
            ];
        }
        $pluckDataManagement =
            $managementEntitlement->pluck("yearInMonth");

        $users = User::whereHas("roles", function ($q) {
            return $q->whereNotIn("name", [
                "super_admin", "admin", "client"
            ]);
        })->whereHas("employee", function ($q) {
            return $q->where("quarterly_vacation_update_date", Carbon::now()->format("Y-m-d"));
        })->whereIn("active", [1])->get();
        $today = \Carbon::now();
        $countCheck = 0;
        foreach ($users as $user) {
            $roles = 0;
            if (isset($user->roles)) {
                $roles = ($user->roles)[0]->id;
            }
            $userId = $user->id;

            $totalLeaveForEmployee = 0;;
            $employee_doj = $user->employee->employee_doj;

            $diff_in_months = $today->diffInMonths($employee_doj);

            if ($employee_doj != "") {
                $nextExpectedMonth = Carbon::parse($user->employee->quarterly_vacation_update_date)->addMonth(4);
                if (in_array($roles, $managementRoles)) {
                    $filterCollectionIndex = $pluckDataManagement->filter(function ($item) use ($i) {
                        return $item >= $i;
                    })->first();
                } else {
                    $filterCollectionIndex = $pluckData->filter(function ($item) use ($diff_in_months) {
                        return $item >= $diff_in_months;
                    })->first();
                }

                $employeeQuarterLeave = isset($entitlementArray[$filterCollectionIndex]) ?
                    $entitlementArray[$filterCollectionIndex]["quarterly_leaveentitlement"] : 0;
                $finalEmployeeArray[$userId] = [
                    "user_id" => $userId,
                    "vacation_balance" => $user->employee->entitled_vacation,
                    "entitled_vacation" => $employeeQuarterLeave + $user->employee->entitled_vacation,
                    "date_of_join" => $employee_doj,
                    "last_vacation_cron_date" => Carbon::now(),
                    "quarterly_vacation_update_date" => $nextExpectedMonth
                ];
            }
        }
        foreach ($finalEmployeeArray as $key => $value) {
            try {
                DB::beginTransaction();
                Employee::where(
                    "user_id",
                    $value["user_id"]
                )->update([
                    "entitled_vacation" => $value["entitled_vacation"],
                    "last_vacation_cron_date" => $value["last_vacation_cron_date"],
                    "quarterly_vacation_update_date" => $value["quarterly_vacation_update_date"]
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
            }
        }
    }

    public function getAll()
    {
        return $this->model->select(['id', 'title', 'less_than_years', 'quarterly_leaveentitlement', 'management', 'created_at', 'updated_at'])->get();
    }

    /**
     * Display details of single Skill Name
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created Security Clearance in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($request)
    {
        if (isset($request['id']) && !empty($request['id'])) {
            $data = [
                'title' => $request['title'],
                'less_than_years' => $request['less_than_years'],
                'quarterly_leaveentitlement' => $request['quarterly_leaveentitlement'],
                'management' => (isset($request['management'])) ? 1 : 0,
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now()
            ];
        } else {
            $data = [
                'title' => $request['title'],
                'less_than_years' => $request['less_than_years'],
                'quarterly_leaveentitlement' => $request['quarterly_leaveentitlement'],
                'management' => (isset($request['management'])) ? 1 : 0,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now()
            ];
        }
        return $this->model->updateOrCreate(array('id' => $request['id']), $data);
    }

    /**
     * Remove the Skill from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
