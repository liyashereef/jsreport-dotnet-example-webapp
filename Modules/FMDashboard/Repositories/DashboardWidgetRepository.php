<?php

namespace Modules\FMDashboard\Repositories;

use App\Services\HelperService;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\CpidCustomerAllocations;
use Modules\Admin\Models\PositionLookup;
use Modules\Admin\Repositories\PositionLookupRepository;
use Modules\FMDashboard\Models\FmDashboardWidget;
use Modules\FMDashboard\Repositories\DashboardWidgetUserRepository;
use Modules\Timetracker\Repositories\EmployeeShiftCpidRepository;

class DashboardWidgetRepository
{

    protected $model;
    protected $dashboardWidgetUserRepository, $positionLookupRepository, $employeeShiftCpidRepository;

    public function __construct(FmDashboardWidget $model,
        DashboardWidgetUserRepository $dashboardWidgetUserRepository,
        PositionLookupRepository $positionLookupRepository,
        EmployeeShiftCpidRepository $employeeShiftCpidRepository) {
        $this->model = $model;
        $this->dashboardWidgetUserRepository = $dashboardWidgetUserRepository;
        $this->position_lookup_repository = $positionLookupRepository;
        $this->employee_shift_cpid_repository = $employeeShiftCpidRepository;
        $this->helper_service = new HelperService();
    }

    public function getAllUserCanView()
    {
        $allowableWidgets = [];
        $user = Auth::user();
        $widgets = $this->model->all();

        foreach ($widgets as $widget) {
            //check user have the permision
            if ($user->can($widget->permission)) {
                array_push($allowableWidgets, $widget);
            }
        }

        return $allowableWidgets;
    }

    public function canSeeWidget($widgetPermission)
    {
        $user = Auth::user();
        if ($user->can($widgetPermission)) {
            $widget = $this->model->where('permission', '=', $widgetPermission)->first();
            if (is_object($widget)) {
                $userWidgetsIds = $this->dashboardWidgetUserRepository->getAllWidgetIdsOfCurrentUser();
                if (in_array($widget->id, $userWidgetsIds)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Finding Timesheet Reconciliation Data
     * get position base data
     * @param $customer_id, $pay_periods
     * @return Array.
     */
    public function setTimesheetReconciliationData($inputs, $widgetRequest = false)
    {
        //Fetching all cpid allocated positions
        $positions = null;
        if ($widgetRequest) {
            $customerId = isset($inputs['customer_id']) ? $inputs['customer_id'] : null;
            if ($customerId != null) {
                $cpidCustomerAllocation = CpidCustomerAllocations::whereHas('cpid_lookup')->with('cpid_lookup')->where('customer_id', $customerId)->get();
                if (!empty($cpidCustomerAllocation)) {
                    $positionArray = $cpidCustomerAllocation->map(function ($item) {
                        return $item->cpid_lookup->position_id;
                    });

                    $positions = PositionLookup::whereHas('CpidLookUpWithTrashed')->whereIn('id', $positionArray)->get();
                }
            }
        } else {
            $positions = $this->position_lookup_repository->getPositionBasedOnCPID();
        }
        //Fetching all employee shift cpids records with filter.
        $timesheet_data = $this->employee_shift_cpid_repository->getTimesheetReconciliationData($inputs);
        // Creating an array of record based on position.
        $reconciliation_data = [];
        $time_chart['series'] = [];
        $time_chart['label'] = [];
        $pay_chart['series'] = [];
        $pay_chart['label'] = [];
        $billable_chart['series'] = [];
        $billable_chart['label'] = [];
        if (sizeof($timesheet_data) >= 1) {
            foreach ($positions as $pos_key => $position) {
                //Declaring default values
                $reconciliation_data[$pos_key] = [];
                $reconciliation_data[$pos_key]['position'] = $position->position;
                $reconciliation_data[$pos_key]['cpid'] = '';
                $reconciliation_data[$pos_key]['net_reguler_hours'] = 0;
                $reconciliation_data[$pos_key]['net_ot_hours'] = 0;
                $reconciliation_data[$pos_key]['net_stat_hours'] = 0;

                $reconciliation_data[$pos_key]['reguler_pay_per_hours'] = 0;
                $reconciliation_data[$pos_key]['ot_pay_per_hours'] = 0;
                $reconciliation_data[$pos_key]['stat_pay_per_hours'] = 0;

                $reconciliation_data[$pos_key]['reguler_pay'] = 0;
                $reconciliation_data[$pos_key]['ot_pay'] = 0;
                $reconciliation_data[$pos_key]['stat_pay'] = 0;

                $reconciliation_data[$pos_key]['reguler_bill'] = 0;
                $reconciliation_data[$pos_key]['ot_bill'] = 0;
                $reconciliation_data[$pos_key]['stat_bill'] = 0;

                $reconciliation_data[$pos_key]['billable_ot'] = 0;
                $reconciliation_data[$pos_key]['absorved_ot'] = 0;

                $time_chart['series'][$pos_key]['name'] = $position->position;
                $time_chart['series'][$pos_key]['data'] = [0, 0, 0];

                $pay_chart['series'][$pos_key]['name'] = $position->position;
                $pay_chart['series'][$pos_key]['data'] = [0, 0, 0];

                $billable_chart['series'][$pos_key]['name'] = $position->position;
                $billable_chart['series'][$pos_key]['data'] = [0, 0, 0];

                $net_ot_hours = 0;
                $billable_overtime_hours = 0;
                $reguler_pay_total = 0;
                $ot_pay_total = 0;
                $stat_pay_total = 0;
                foreach ($timesheet_data as $timesheet) {
                    //Checking position id and cpid position
                    if ($timesheet->cpid_lookup_with_trash->position_id == $position->id) {

                        $reconciliation_data[$pos_key]['cpid'] = $timesheet->cpid_lookup_with_trash->cpid;
                        $reconciliation_data[$pos_key]['reguler_pay_per_hours'] = $timesheet->cpid_rates_with_trash->p_standard;
                        $reconciliation_data[$pos_key]['ot_pay_per_hours'] = $timesheet->cpid_rates_with_trash->p_overtime;
                        $reconciliation_data[$pos_key]['stat_pay_per_hours'] = $timesheet->cpid_rates_with_trash->p_holiday;

                        /**
                         * work_hour_type_id 1 = Reguler
                         * work_hour_type_id 2 = OT
                         * work_hour_type_id 2 = stat
                         */
                        //For Getting REGULER values
                        if ($timesheet->work_hour_type_id == 1) {

                            $reconciliation_data[$pos_key]['net_reguler_hours'] += $this->convertSecondToNumberFormat($timesheet->total_hours);
                            $reguler_pay_total += $timesheet->total_pay;
                            // $reconciliation_data[$pos_key]['reguler_pay'] = number_format($reguler_pay_total, 2);
                            $reconciliation_data[$pos_key]['reguler_pay'] = number_format((float)$reguler_pay_total, 2, '.', '');
                            $reconciliation_data[$pos_key]['reguler_bill'] = $this->calculateAmountBy($timesheet->cpid_rates_with_trash->b_standard, $reconciliation_data[$pos_key]['net_reguler_hours']);

                            //For Getting OT values
                        } elseif ($timesheet->work_hour_type_id == 2) {

                            $net_ot_hours = $net_ot_hours + $timesheet->total_hours;
                            $reconciliation_data[$pos_key]['net_ot_hours'] += $this->convertSecondToNumberFormat($timesheet->total_hours);
                            $ot_pay_total += $timesheet->total_pay;
                            // $reconciliation_data[$pos_key]['ot_pay'] = number_format($ot_pay_total, 2);
                            $reconciliation_data[$pos_key]['ot_pay'] = number_format((float) $ot_pay_total, 2, '.', '');
                            $reconciliation_data[$pos_key]['ot_bill'] = $this->calculateAmountBy($timesheet->cpid_rates_with_trash->b_overtime, $reconciliation_data[$pos_key]['net_ot_hours']);

                        } else {
                            //For Getting STAT values
                            $reconciliation_data[$pos_key]['net_stat_hours'] += $this->convertSecondToNumberFormat($timesheet->total_hours);
                            $stat_pay_total += $timesheet->total_pay;
                            // $reconciliation_data[$pos_key]['stat_pay'] = number_format($stat_pay_total, 2);
                            $reconciliation_data[$pos_key]['stat_pay'] = number_format((float) $stat_pay_total, 2, '.', '');
                            $reconciliation_data[$pos_key]['stat_bill'] = $this->calculateAmountBy($timesheet->cpid_rates_with_trash->b_holiday, $reconciliation_data[$pos_key]['net_stat_hours']);

                        }
                        //For billable OT. shift_payperiod submitted employee position must equals to cpid's allocated position.
                        if ($timesheet->employee_shift_payperiod->trashed_employee->position_id == $timesheet->cpid_lookup_with_trash->position_id) {
                            if (!empty($timesheet->employee_shift_payperiod->billable_overtime_hours)) {
                                $billable_overtime_hours = $this->helper_service->strTimeToSeconds($timesheet->employee_shift_payperiod->billable_overtime_hours);
                            }
                        }
                    }
                }

                $time_chart['label'] = ['Reg Hours', 'OT Hours', 'Stat Hours'];
                // $time_chart['series'][$pos_key]['data'][0] = $reconciliation_data[$pos_key]['net_reguler_hours'];
                // $time_chart['series'][$pos_key]['data'][1] = $reconciliation_data[$pos_key]['net_ot_hours'];
                // $time_chart['series'][$pos_key]['data'][2] = $reconciliation_data[$pos_key]['net_stat_hours'];
                $time_chart['series'][$pos_key]['data'][0] = floatval(number_format((float) $reconciliation_data[$pos_key]['net_reguler_hours'], 2, '.', ''));
                $time_chart['series'][$pos_key]['data'][1] = floatval(number_format((float) $reconciliation_data[$pos_key]['net_ot_hours'], 2, '.', ''));
                $time_chart['series'][$pos_key]['data'][2] = floatval(number_format((float) $reconciliation_data[$pos_key]['net_stat_hours'], 2, '.', ''));

                $pay_chart['label'] = ['Reg Pay', 'OT Pay', 'Stat Pay'];
                $pay_chart['series'][$pos_key]['data'][0] = floatval($reconciliation_data[$pos_key]['reguler_pay']);
                $pay_chart['series'][$pos_key]['data'][1] = floatval($reconciliation_data[$pos_key]['ot_pay']);
                $pay_chart['series'][$pos_key]['data'][2] = floatval($reconciliation_data[$pos_key]['stat_pay']);

                //absorved_ot calculation.
                if ($billable_overtime_hours != 0) {
                    $reconciliation_data[$pos_key]['billable_ot'] = $this->convertSecondToNumberFormat($billable_overtime_hours);
                    $reconciliation_data[$pos_key]['absorved_ot'] = $reconciliation_data[$pos_key]['net_ot_hours'] - $reconciliation_data[$pos_key]['billable_ot'];
                }

                $billable_chart['label'] = ['Billable OT', 'Non Billable OT', 'Non Billable Stat'];
                $billable_chart['series'][$pos_key]['data'][0] = floatval(number_format((float)$reconciliation_data[$pos_key]['billable_ot'], 2, '.', ''));
                $billable_chart['series'][$pos_key]['data'][1] = floatval(number_format((float)$reconciliation_data[$pos_key]['absorved_ot'], 2, '.', ''));
                $billable_chart['series'][$pos_key]['data'][2] = floatval(0);

/**START* Adding client mentioned  colors to graph  */
                $color = $this->getPositionColor($position->position);
                if ($color) {
                    $time_chart['series'][$pos_key]['color'] = $color;
                    $pay_chart['series'][$pos_key]['color'] = $color;
                    $billable_chart['series'][$pos_key]['color'] = $color;
                }

/**END* Adding client mentioned  colors to graph  */

                // If timesheet data not avaliable, Unset an array key.
                if ($reconciliation_data[$pos_key]['net_reguler_hours'] == 0 &&
                    $reconciliation_data[$pos_key]['net_ot_hours'] == 0 &&
                    $reconciliation_data[$pos_key]['net_stat_hours'] == 0) {
                    unset($reconciliation_data[$pos_key]);
                }

            }
        }

        $data['reconciliation_data'] = $reconciliation_data;
        $data['reconciliation_time_chart'] = $time_chart;
        $data['reconciliation_pay_chart'] = $pay_chart;
        $data['reconciliation_billable_chart'] = $billable_chart;

        return $data;

    }

    public function convertSecondToNumberFormat($timeSeconds)
    {

        $hours = floor($timeSeconds / 3600);
        $minutes = floor(($timeSeconds / 60) % 60);

        if (!empty($minutes)) {
            return number_format($hours + ($minutes / 60), 4);
        }

        return "$hours.00";

    }

    public function convertSecondToHoursMinuts($timeSeconds)
    {
        // $timeHr = ($timeSeconds/(60 *60));
        // return number_format($timeHr,2);
        $hours = floor($timeSeconds / 3600);
        $minutes = floor(($timeSeconds / 60) % 60);
        // $seconds = $timeSeconds % 60;
        // echo "$hours:$minutes:$seconds";
        return "$hours.$minutes";

    }

    public function calculateAmountBy($rate, $timeHr)
    {
        // $timeHr = ($timeSeconds/(60 *60));
        return number_format(($rate * $timeHr), 2);
    }

    public function getPositionColor($positionName)
    {
        if ($positionName == 'Site Supervisor') {
            $color = '#eb5669';
        } elseif ($positionName == 'Shift Leader') {
            $color = '#f5ae60';
        } elseif ($positionName == 'Security Guard') {
            $color = '#8fb15a';
        } elseif ($positionName == 'MST Guard') {
            $color = '#191970';
        } elseif ($positionName == 'MST Supervisor') {
            $color = '#808000';
        } elseif ($positionName == 'Mobile Patrol') {
            $color = '#868e96';
        } else {
            $color = '';
        }
        return $color;
    }
}
