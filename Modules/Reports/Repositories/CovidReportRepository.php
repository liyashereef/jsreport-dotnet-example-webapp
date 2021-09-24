<?php

namespace Modules\Reports\Repositories;

use App\Exports\HealthScreenExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\Models\DailyHealthSchedules;
use Modules\Admin\Models\ShiftModule;
use Modules\Admin\Models\ShiftModuleEntry;
use Modules\Admin\Models\ShiftModuleField;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Reports\Emails\HealthScreenReportEmail;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;

class CovidReportRepository
{
    protected $employeeShiftRepository, $shiftModuleModel;

    public function __construct(
        EmployeeShiftRepository $employeeShiftRepository,
        ShiftModule $shiftModuleModel,
        ShiftModuleField $shiftModuleField,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    )
    {
        $this->employeeShiftRepository = $employeeShiftRepository;
        $this->shiftModuleModel = $shiftModuleModel;
        $this->shiftModuleField = $shiftModuleField;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }

    /**
     * get covid report
     * @param $request customerId start date end date
     * @return array
     */
    public function getCovidReport(
        $argStartDate,
        $argEndDate,
        $argCustomerId = null,
        $argAreaManager = null,
        $argEmployees = null,
        $table = false
    )
    {
        if ($argCustomerId !== null && !is_array($argCustomerId)) {
            $argCustomerId = array($argCustomerId);
        }

        //cache
        $cachePrams[] = ($argStartDate !== null) ? $argStartDate : "";
        $cachePrams[] = ($argStartDate !== null) ? $argEndDate : "";
        $cachePrams[] = ($argCustomerId !== null) ? implode("", $argCustomerId) : "";
        $cachePrams[] = ($argAreaManager !== null) ? implode("", $argAreaManager) : "";
        $cachePrams[] = ($argEmployees !== null) ? implode("", $argEmployees) : "";
        $cacheStr = implode($cachePrams);
        $userId = '';
        if (isset(\Auth::user()->id)) {
            $userId = \Auth::user()->id;
        }
        $cacheStr = $cacheStr . $userId . $table;
        $cacheId = md5($cacheStr);
        try {
            if (\Cache::has('covidReport' . $cacheId)) {
                return \Cache::get('covidReport' . $cacheId);
            }
        } catch (\Exception $e) {
            \Log::error($e);
        }

        // Shift Module Names
        $healthScan = 'Health Scan';
        $healthScreen = 'Health Screen';
        $selfAttest = 'Self Attest';

        // Covid DropdownIds
        $selectedDropdown = [34, 41];

        // Query parameters
        $startDate = $argStartDate;
        $endDate = $argEndDate;

        // if only start or end date is given, filter for single day
        if ($endDate === null && $startDate !== null) {
            $endDate = $startDate;
        } else if ($startDate === null && $endDate !== null) {
            $startDate = $endDate;
        }

        $start = date("Y-m-d H:i:s", strtotime($startDate . " 00:00:00"));
        $end = date("Y-m-d H:i:s", strtotime($endDate . " 23:59:59"));
        $customerIdFilter = null;

        // customer filter array preparation
        if (null !== ($argCustomerId)) {
            $customerIdFilter = array_map('intval', $argCustomerId);
        }

        // area manager filter array preparation
        if (null !== ($argAreaManager)) {
            // find customer allocated for area manager
            $areaCustomerAllocation = $this->customerEmployeeAllocationRepository
                ->getAllocatedCustomerId($argAreaManager);
            if (null !== $customerIdFilter) {
                $customerIdFilter = array_intersect($customerIdFilter, $areaCustomerAllocation);
            } else {
                $customerIdFilter = $areaCustomerAllocation;
            }
        }

        $customerId = [];
        $shiftModuleId = [];

        // To get customerids, shift module ids, field_ids, question array
        $shiftModuleQuery = $this->shiftModuleModel
            // Module name based filtering
            ->where('module_name', 'like', '%' . $healthScan . '%')
            ->orWhere('module_name', 'like', '%' . $healthScreen . '%')
            ->orWhere('module_name', 'like', '%' . $selfAttest . '%');
        // Customer Id based filtering
        if (!empty($customerIdFilter)) {
            $shiftModuleQuery->whereIn('customer_id', $customerIdFilter);
        }
        $shiftModuleId = $shiftModuleQuery->pluck('id');

        //employee filter from request
        $employeeId = $argEmployees;

        // get all shifts with given parameters
        $employeeShiftUsers = $this->employeeShiftRepository->getEmployeeShiftDetailsByDate(
            $startDate,
            $endDate,
            $customerIdFilter,
            $employeeId
        );
        $employeeShift = data_get(data_get($employeeShiftUsers, "*.employee_shift_payperiods.*.shifts"), "*.*");
        $employeeShiftId = data_get($employeeShift, "*.id");
        if ($employeeShiftId === null) {
            return [];
        }
        // get field ids of the fetched modules with covid-19 and covid-19 consent
        $shiftModuleFieldId = ShiftModuleField::whereIn('module_id', $shiftModuleId)
            ->whereIn('dropdown_id', $selectedDropdown)->pluck('id');

        // get userid and entry id of entries with answer "no/yes" where shift start date in filter values and field ids
        $shiftModuleEntry = \DB::table('shift_module_entries')
            ->selectRaw(
                "SUBSTRING_INDEX(group_concat(id),',',1) id,
                            SUBSTRING_INDEX(group_concat(shift_id),',',1) shift_id, 
                            shift_start_date,
                            created_by,
                            group_concat(field_value) field_value")
            ->where(function ($q) {
                $q->where('field_value', 'like', 'No');
                $q->orWhere('field_value', 'like', 'Yes');
            })
            ->whereIn('module_id', $shiftModuleId)
            ->whereIn('field_id', $shiftModuleFieldId);
        if ($customerIdFilter !== null) {
            $shiftModuleEntry->whereIn('customer_id', $customerIdFilter);
        }
        if ($startDate !== null) {
            $shiftModuleEntry->whereBetween('shift_start_date', [$start, $end]);
        }
        $shiftModuleEntry->when(($employeeId !== null), function ($q) use ($employeeId) {
            $q->whereIn('created_by', $employeeId);
        });
        $shiftModuleEntry->groupBy(['shift_start_date', 'created_by']);
        $shiftModuleEntry = $shiftModuleEntry->get();
        $shiftModuleEntryId = data_get($shiftModuleEntry, "*.id");
        $shiftId = data_get($shiftModuleEntry, "*.shift_id");
        $answeredYesEntryArr = [];
        //screening passed entries
        $answeredYesEntry = $shiftModuleEntry->filter(function ($value, $key) {
            return (strpos($value->field_value, "Yes") !== false);
        });
        $answeredYesEntryArr = data_get($answeredYesEntry, "*.id");

        // get shifts whose start time and shift id not in shift module entries
        $nonComplaintShift = EmployeeShift::whereIn('id', $employeeShiftId)
            ->whereNotIn('id', $shiftId)
            ->with('shift_payperiod.trashed_customer', 'shift_payperiod.trashed_user.employee')
            ->orderby('start')
            ->get();

        // prepare array for non complaint shift entries
        $nonComplaintArr = [];
        $nonComplaintArr = $this->prepareData($nonComplaintShift);

        $shiftModuleEntries = ShiftModuleEntry::whereIn('id', $shiftModuleEntryId)
            ->with('customer', 'createdUser.employee', 'type')
            ->get();

        // initialise with shifts that not submitted any entry
        $each = $nonComplaintArr;

        // Add values to array for complaint shift entries
        $each = $this->prepareData($shiftModuleEntries, $nonComplaintArr, $answeredYesEntryArr);
        if ($table) {
            $each = data_get($each, "*.*");
        }

        try {
            \Cache::put('covidReport' . $cacheId, $each, 5);
        } catch (\Exception $e) {
            \Log::error($e);
        }

        return $each;
    }

    public function prepareData(
        $shiftModuleEntries,
        $nonComplaintArr = null,
        $answeredYesEntryArr = null
    )
    {
        if (null !== $nonComplaintArr) {
            $shift = false;
            $each = $nonComplaintArr;
        } else {
            $shift = true;
            $each = array();
        }
        foreach ($shiftModuleEntries as $entryKey => $entryVal) {
            if ($shift) {
                $shiftStartDate = Carbon::parse($entryVal->start)->format('d-m-Y');
                $shiftStartTime = Carbon::parse($entryVal->start)->format('h:i A');
                $shiftFullTime = $entryVal->start;
                $employeeNumber = $entryVal->shift_payperiod->trashed_user->employee->employee_no;
                $user = $entryVal->shift_payperiod->trashed_user;
                $customer = $entryVal->shift_payperiod->trashed_customer;
                $screeningPassed = "No";
                $screeningCompleted = "No";
                $screeningCompletedDate = "--";
                $location = "--";
                $shiftArrKey = $customer->project_number . "-" . $employeeNumber . "-" . Carbon::parse($entryVal->start)->format('d-m-y-H-i');
            } else {
                $shiftStartDate = Carbon::parse($entryVal->shift_start_date)->format('d-m-Y');
                $shiftStartTime = Carbon::parse($entryVal->shift_start_date)->format('h:i A');
                $shiftFullTime = $entryVal->shift_start_date;
                $employeeNumber = $entryVal->createdUser->employee->employee_no;
                $user = $entryVal->createdUser;
                $customer = $entryVal->customer;
                if (in_array($entryVal->id, $answeredYesEntryArr)) {
                    $screeningPassed = "No";
                } else {
                    $screeningPassed = "Yes";
                }
                $screeningCompleted = "Yes";
                $screeningCompletedDate = Carbon::parse($entryVal->created_at)->format('d-M-y h:i A');
                if ($entryVal->type->field_type == 2) {
                    $location = $entryVal->field_value;
                } else {
                    $location = null;
                }
                $shiftArrKey = $customer->project_number . "-" . $employeeNumber . "-" . Carbon::parse($entryVal->shift_start_date)->format('d-m-y-H-i');
            }
            $each[$shiftArrKey][$employeeNumber]['project_number'] = $customer->project_number;
            $each[$shiftArrKey][$employeeNumber]['project_name'] = $customer->client_name;
            $each[$shiftArrKey][$employeeNumber]['area_manager'] = ($customer->employeeLatestCustomerAreaManager->areaManager->fullName) ?? "--";
            $each[$shiftArrKey][$employeeNumber]['employee_number'] = $user->employee->employee_no;
            $each[$shiftArrKey][$employeeNumber]['employee_name'] = $user->full_name;
            $each[$shiftArrKey][$employeeNumber]['phone'] = $user->employee->phone;
            $each[$shiftArrKey][$employeeNumber]['email'] = $user->email;
            $each[$shiftArrKey][$employeeNumber]['date'] = $shiftFullTime;
            $each[$shiftArrKey][$employeeNumber]['area_manager'] = ($customer->employeeLatestCustomerAreaManager->areaManager->fullName) ?? "--";
            $each[$shiftArrKey][$employeeNumber]['sign_in'] = $shiftStartTime;
            $each[$shiftArrKey][$employeeNumber]['screening_passed'] = $screeningPassed;
            $each[$shiftArrKey][$employeeNumber]['screening_completed'] = $screeningCompleted;
            $each[$shiftArrKey][$employeeNumber]['covid_screen_submit'] = $screeningCompletedDate;
            $each[$shiftArrKey][$employeeNumber]['location'] = $location;
        }
        return $each;
    }

    public function getCovidGraphReport($request)
    {
        $report = $this->getCovidReport(
            $request->get("startDate"),
            $request->get("endDate"),
            $request->get("customer_id"),
            $request->get("area_manager"),
            $request->get("employees")
        );

        $days = $yes = $no = $total = $yesMap = $noMap = $totalMap = [];

        $graphDate = [];

        if (!empty($report)) {
            foreach ($report as $date => $individualReport) {
                foreach ($individualReport as $key => $value) {
                    $date = Carbon::parse($value['date'])->format('d-m-Y');
                    $yesMap[$date] = ($yesMap[$date]) ?? 0;
                    $noMap[$date] = ($noMap[$date]) ?? 0;
                    $totalMap[$date] = ($totalMap[$date]) ?? 0;
                    if (isset($value['screening_passed']) && $value['screening_passed'] == 'Yes') {
                        if (isset($yesMap[$date])) {
                            $yesMap[$date]++;
                        }
                    } else {
                        if (isset($noMap[$date])) {
                            $noMap[$date]++;
                        }
                    }
                    $totalMap[$date]++;
                }
            }
            $graphDate = array_keys($totalMap);
        }

        $yes = array_values($yesMap);
        $no = array_values($noMap);
        $total = array_values($totalMap);

        return json_encode(['series' => $graphDate, 'data' => ['yes' => $yes, 'no' => $no, 'total' => $total]]);
    }

    public function sendDailyHealthReport()
    {
        $folder_name = "Report";
        $date = date('Y-m-d', strtotime(carbon::now()));
        $file_name = "healthscreen_" . $date . ".xlsx";
        $startDate = date('Y-m-d', (strtotime('-1 day', strtotime(date("Y-m-d")))));
        $endDate = date('Y-m-d');
        $data = $this->getCovidReport($startDate, $endDate);
        if (Excel::store(new HealthScreenExport(2020, $data), $folder_name . '/' . $file_name)) {
            Log::channel('reportLog')->info("Excel report completed");
            $path = "app/Report/" . $file_name;
            $filePath = storage_path($path);
            $assignedUsers = DailyHealthSchedules::with("user")->get();
            foreach ($assignedUsers as $users) {
                try {
                    $this->sendNotification($filePath, $users->user->email, $file_name);
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        } else {
            Log::channel('reportLog')->info("Excel report not completed");
            throw new \Exception("Excel report not completed and File not created");
        }
    }

    /**
     * To send notificaion to candidates
     *
     * @param $candidate_id
     * @param $filename
     * @return void
     */

    public function sendNotification($filepath, $email, $file_name)
    {
        Log::channel('reportLog')->info("Mail Function enetering" . $filepath);
        Log::channel('reportLog')->info("Mail Path");
        $to = $email;
        $mail = Mail::to($to);
        $send = $mail->send(new HealthScreenReportEmail('mail.healthscreennotification', $filepath, $file_name));
        Log::channel('reportLog')->info("mail Send");
        if (Mail::failures()) {
            Log::channel('fileDeleteJobLog')->info(Mail::failures());
        } else {
            Log::channel('fileDeleteJobLog')->info("file deletion starts");
            // FileDelete::dispatch($filepath)->delay(now()->addDay(1));
            Log::channel('fileDeleteJobLog')->info("file deletion sucess");
        }
    }
}
