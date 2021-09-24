<?php

namespace Modules\Timetracker\Repositories;

use App\User;
use Auth;
use Modules\Admin\Models\ShiftTiming;
use Modules\Timetracker\Models\EmployeeAvailability;
use Modules\Timetracker\Models\EmployeeUnavailability;

class EmployeeAvailabilityRepository
{

    public $ShiftTimingModel;
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */

    /**
     * Create a new EmployeeAvailabilityRepository instance.
     *
     * @param  \App\Models\Notification $Notification
     */
    public function __construct(EmployeeAvailability $employeeAvailability, EmployeeUnavailability $EmployeeUnavailability, ShiftTiming $ShiftTimingModel)
    {
        $this->employee_availibility_model = $employeeAvailability;
        $this->EmployeeUnavailability = $EmployeeUnavailability;
        $this->ShiftTimingModel = $ShiftTimingModel;
    }

    public function getShiftTimings()
    {
        $shifttimingarray = [];
        $shifttimings = $this->ShiftTimingModel->get();

        foreach ($shifttimings as $shifts) {
            # code...
            $shifttimingarray[$shifts->id]["shift_name"] = $shifts->shift_name;
            $shifttimingarray[$shifts->id]["from"] = $shifts->from;
            $shifttimingarray[$shifts->id]["to"] = $shifts->to;
        }

        return $shifttimingarray;
    }
    public function getEmployeeshift($employeeid)
    {
        $availablearray = [];
        $availability = $this->employee_availibility_model->where('employee_id', $employeeid)->get();
        $i = 0;
        $workdays = config('globals.array_shift_day');
        foreach ($workdays as $key => $value) {
            $availablearray[$key] = [];
        }
        foreach ($availability as $available) {
            $week_day = $available->week_day;
            $shift_timing_id = $available->shift_timing_id;
            //$availablearray=array_combine([$week_day,$shift_timing_id],$availablearray);
            //$availablearray[$week_day] = array_push((array)$availablearray[$week_day],$shift_timing_id);
            if (isset($availablearray[$week_day])) {
                array_push($availablearray[$week_day], $shift_timing_id);
            } else {
                $availablearray[$week_day] = [$shift_timing_id];
            }
            //$i++;
        }
        return $availablearray;
    }

    public function saveUnAvailability($employeeid, $fromdate, $todate, $comments)
    {
        $saved = $this->EmployeeUnavailability->updateOrCreate([
            "employee_id" => $employeeid,
            "from" => $fromdate,
            "to" => $todate,
            "comments" => $comments,
            "updated_at" => \Carbon\Carbon::now(),
            "created_by" => Auth::user()->id,
        ]);

        if ($saved->id > 0) {
            return response()->json(['success' => 'saved'], 200);
        } else {
            return response()->json(['error' => 'Some error'], 404);
        }
    }

    public function removeUnAvailability($employeeid, $unavailabilityid)
    {
        $saved = $this->EmployeeUnavailability->where('id', $unavailabilityid)->delete();
        if ($saved == 1) {
            return response()->json(['success' => 'saved'], 200);
        } else {
            return response()->json(['error' => 'Some error'], 404);
        }
    }
    public function getUnAvailability($employeeid)
    {
        $returnarray = [];
        $unavailablearray = $this->EmployeeUnavailability->where('employee_id', $employeeid)->where('from', '>=', date("Y-m-d"))->get();
        $i = 0;
        foreach ($unavailablearray as $unavailable) {

            $returnarray[$i]["id"] = $unavailable->id;
            $returnarray[$i]["from"] = date("M d Y", strtotime($unavailable->from));
            $returnarray[$i]["to"] = date("M d Y", strtotime($unavailable->to));
            $returnarray[$i]["comments"] = $unavailable->comments;
            $i++;
        }
        return $returnarray;
    }
    public function getNonAvailability($employeeid)
    {
        $availablearray = [];
        $workingarray = [];
        $availability = $this->employee_availibility_model->where('employee_id', $employeeid)->get();
        $i = 0;
        $workdays = config('globals.array_shift_day');
        $shiftsarray = $this->getShiftTimings();
        foreach ($workdays as $key => $value) {
            $workingarray[$key] = [];
            //print_r($this->getShiftTimings());
            foreach ($shiftsarray as $skey => $svalue) {
                //array_push($workingarray[$key],$skey);
                $workingarray[$key][$skey] = $skey;
            }

        }

        foreach ($workdays as $key => $value) {
            $availablearray[$key] = [];
        }
        foreach ($availability as $available) {
            $week_day = $available->week_day;
            $shift_timing_id = $available->shift_timing_id;
            unset($workingarray[$week_day][$shift_timing_id]);
            if (isset($availablearray[$week_day])) {
                array_push($availablearray[$week_day], $shift_timing_id);
            } else {
                $availablearray[$week_day] = [$shift_timing_id];
            }
        }

        return $workingarray;
    }

    public function getUseravailability()
    {
        return $this->employee_availibility_model->select('shift_timing_id', 'week_day')->where('employee_id', Auth::user()->id)->get();
    }

    public function setEmployeeavailable($Workdays, $empavailabilityarray)
    {

        $currentavailabilitycustomarray = [];
        $currentavailability = $this->getUseravailability();
        foreach ($currentavailability as $availability) {
            $shift = $availability->shift_timing_id;
            $weekdayidentifier = $availability->week_day;
            $currentavailabilitycustomarray[$shift . $weekdayidentifier] = $shift . $weekdayidentifier;

        }

        $shifts = $this->ShiftTimingModel->all()->pluck('id');

        try {
            foreach ($Workdays as $key => $value) {

                $days = $key;

                $daysText = $value;
                $weekday = $key;

                if (isset($empavailabilityarray[$key])) {

                    $shiftarray = $empavailabilityarray[$key];

                    foreach ($shifts as $k => $v) {
                        $empshift = $v;

                        if (!in_array($empshift, $shiftarray)) {
                            $deleteavailability = $this->employee_availibility_model->where(["employee_id" => Auth::user()->id, "week_day" => $days, "shift_timing_id" => $empshift]);
                            $deleteavailability->delete();
                        }

                    };
                    foreach ($shiftarray as $shiftval) {

                        $allshifts = $shiftval . $days;

                        //echo PHP_EOL;
                        if (isset($empavailabilityarray[$days])) {
                            $saveavailabilty = $this->employee_availibility_model->updateOrCreate(['employee_id' => Auth::user()->id, 'week_day' => $days, 'shift_timing_id' => $shiftval], ['employee_id' => Auth::user()->id, 'week_day' => $days, 'shift_timing_id' => $shiftval, 'updated_at' => \Carbon\Carbon::now(), "created_by" => Auth::user()->id]);
                        } else {

                        }

                    }
                } else {
                    $deleteavailability = $this->employee_availibility_model->where(["employee_id" => Auth::user()->id, "week_day" => $weekday]);
                    $deleteavailability->delete();
                }

            }

        } catch (\Throwable $th) {
            throw $th;

        }

    }

    /*
     * fetch Availability last update date
     */
    public function getLastUpdatedDataByUser($userId)
    {
        $result = ['last_updated_date' => null, 'last_updated_user' => null];
        if (!empty($userId)) {
            $employeeAvailability = EmployeeAvailability::withTrashed()->where('employee_id', $userId)
                ->latest('updated_at')->first();
            $employeeUnAvailability = EmployeeUnavailability::withTrashed()->where('employee_id', $userId)
                ->latest('updated_at')->first();

            if (!empty($employeeUnAvailability) && !empty($employeeAvailability)) {
                if ($employeeUnAvailability->updated_at > $employeeAvailability->updated_at) {
                    $result = [
                        'last_updated_date' => $employeeUnAvailability->updated_at->format('l, M d, Y @h:i A'),
                        'last_updated_user' => $employeeUnAvailability->createdUser ? $employeeUnAvailability->createdUser->full_name : '',
                    ];
                } else {
                    $result = [
                        'last_updated_date' => $employeeAvailability->updated_at->format('l, M d, Y @h:i A'),
                        'last_updated_user' => $employeeAvailability->createdUser ? $employeeAvailability->createdUser->full_name : '',
                    ];
                }
            } elseif (!empty($employeeUnAvailability)) {
                $result = [
                    'last_updated_date' => $employeeUnAvailability->updated_at->format('l, M d, Y @h:i A'),
                    'last_updated_user' => $employeeUnAvailability->createdUser ? $employeeUnAvailability->createdUser->full_name : '',
                ];
            } elseif (!empty($employeeAvailability)) {
                $result = [
                    'last_updated_date' => $employeeAvailability->updated_at->format('l, M d, Y @h:i A'),
                    'last_updated_user' => $employeeAvailability->createdUser ? $employeeAvailability->createdUser->full_name : '',
                ];
            }
        }

        return $result;
    }

}
