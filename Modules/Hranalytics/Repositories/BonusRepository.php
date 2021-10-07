<?php

namespace Modules\Hranalytics\Repositories;

use App\Services\HelperService;
use App\Services\LocationService;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Modules\Admin\Models\SpareBonusModelSetting;
use Modules\Hranalytics\Models\BonusSettings;
use Modules\Hranalytics\Models\ScheduleCustomerRequirement;
use Modules\Hranalytics\Models\BonusEmployeeData;
use Modules\Hranalytics\Models\BonusFinalizedData;
use Modules\Hranalytics\Models\EventLogEntry;
use Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts;

class BonusRepository
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    /**
     * @var HelperService
     */
    private $helperService;
    /**
     * @var LocationService
     */
    private $locationService;

    /**
     * Create a new CandidateRepository instance.
     * @param HelperService $helperService
     * @param LocationService $locationService
     */
    public function __construct()
    {
        $this->directory_seperator = "/";
        $this->helperService = new HelperService();
        $this->locationService = new LocationService();
    }

    public function saveBasicData($bonusId)
    {
        $settingData = BonusSettings::find($bonusId);
        $bonusPoolAmount = $settingData->bonus_amount;
        $noticeCap = $settingData->noticecap_percentage;
        if ($settingData->end_date > date("Y-m-d")) {
            $calculationDate = Carbon::now()->format("Y-m-d");
        } else {
            $calculationDate = $settingData->end_date;
        }
        BonusEmployeeData::where("bonus_pool_id", intval($bonusId))
            ->where("rank_day", $calculationDate)->delete();
        if ($settingData) {
            $startDate = $settingData->start_date;
            $endDate = $settingData->end_date;
            $bonusAmount = $settingData->bonus_amount;
            $wageCapPercentage = $settingData->wagecap_percentage;
            $shiftCapPercentage = $settingData->shiftcap_percentage;
            $noticeCapPercentage = $settingData->noticecap_percentage;
            $customerRequirements = ScheduleCustomerRequirement::with(
                [
                    "multifill.latestEventLog"
                ]
            )->select("*")
                ->when($startDate != "", function ($q) use ($startDate, $calculationDate) {
                    // return $q->whereBetween("start_date", [
                    //     $startDate, \Carbon::parse($calculationDate)->endOfDay()->format("Y-m-d")
                    // ]);
                    return $q->whereRaw("( start_date between ? and ?) or (end_date between ? and ?)", [
                        $startDate, $calculationDate, $startDate, $calculationDate
                    ]);
                })

                ->whereHas("multifill", function ($q) {
                    return $q->where("assigned", 1);
                })
                ->whereHas("user")
                ->get();
            $schedules = ScheduleCustomerMultipleFillShifts::where("shift_from", ">=", $startDate)
                ->where(
                    "shift_to",
                    "<=",
                    Carbon::parse($calculationDate)->format("Y-m-d")
                )->pluck("schedule_customer_requirement_id")->toArray();

            $sparesBonusArray = [];
            $employeeShifts = [];
            $employeeRates = [];
            $noticeArrays = [];
            $includedEmployees = [];
            $totalSiteRate = 0;
            $totalAcceptedRate = 0;
            $totalAssignedShifts = 0;
            $reqIdArray = [];
            $noOfShifts = 0;
            $standardNotice = [];
            $standardNotice = 16;
            $totalAdjustmentArray = [];
            foreach ($customerRequirements as $customerRequirement) {
                $filledShifts = $customerRequirement->multifill;
                $reqIdArray[] = $customerRequirement->id;
                foreach ($filledShifts as $filledShift) {
                    if ($filledShift->shift_from <= Carbon::now()->endOfDay()) {
                        if ($filledShift->assigned > 0) {
                            $totalAssignedShifts++;
                        }
                        $noOfShifts++;
                        $totalSiteRate = $totalSiteRate + $customerRequirement->site_rate;
                        $sparesBonusArray[$filledShift->assigned_employee_id]["employee_id"]
                            =
                            $filledShift->assigned_employee_id;
                        $employeeShifts[$filledShift->assigned_employee_id][] = $filledShift->id;

                        if (isset($filledShift->latestEventLog)) {
                            $totalAcceptedRate = $totalAcceptedRate + $filledShift->latestEventLog->accepted_rate;
                            if (!in_array($filledShift->assigned_employee_id, $includedEmployees)) {
                                $includedEmployees[] = $filledShift->assigned_employee_id;
                            }
                            $noticeInDates =
                                $filledShift->latestEventLog->created_at->diffInDays($filledShift->shift_from);
                            if (isset($noticeArrays[$filledShift->assigned_employee_id])) {
                                $noticeArrays[$filledShift->assigned_employee_id] = $noticeArrays[$filledShift->assigned_employee_id] +
                                    $noticeInDates;
                            } else {
                                $noticeArrays[$filledShift->assigned_employee_id] = $noticeInDates;
                            }
                            // $standardNotice[] = $noticeInDates;
                            $employeeRates[$filledShift->assigned_employee_id][$filledShift->id] =
                                $filledShift->latestEventLog->accepted_rate;
                        }
                    }
                }
            }
            // $averageSiteRate = ($totalSiteRate / $totalAssignedShifts);
            $insertDataArray = [];
            $eventLogs = EventLogEntry::whereIn("user_id", $includedEmployees)
                ->get();
            $eventsArray = [];
            $reliabilityArray = [];
            foreach ($eventLogs as $eventLog) {
                $eventScore = (null !== ($eventLog->user->eventlog_score->sortByDesc("id")->first()));
                $reliabilityScore = (null !== ($eventLog->user->eventlog_score->first()) ?
                    $eventLog->user->eventlog_score->sortByDesc("id")->first()->avg_score : '') ?
                    $eventLog->user->eventlog_score->first()->avg_score : '';
                $eventsArray[$eventLog->user_id] = isset($eventsArray[$eventLog->user_id]) ?
                    $eventsArray[$eventLog->user_id] + 1 : 1;
                $reliabilityArray[$eventLog->user_id] = $reliabilityScore;
            }
            $OverallTotalSiteRate = 0;
            if ($totalSiteRate > 0 && $noOfShifts > 0) {
                $OverallTotalSiteRate = floatval($totalSiteRate / $noOfShifts);
            }
            foreach ($includedEmployees as $includedEmployee) {
                if ($includedEmployee != "") {
                    $noOfShiftsTaken = count($employeeRates[$includedEmployee]);
                    $averageWage = $employeeRates[$includedEmployee];
                    $average = array_sum($averageWage) / count($averageWage);
                    $averageNotice = $noticeArrays[$includedEmployee] < 1 ? 1 : $noticeArrays[$includedEmployee];

                    $average_wage_gross_up = ($OverallTotalSiteRate / $average) * 100;
                    $average_notice_gross_up = 0;
                    $noticeCondition = 0;
                    if ($standardNotice > 0 && $averageNotice > 0) {
                        $noticeCondition = ($standardNotice / $averageNotice) * 100;
                    }
                    if ($average > $OverallTotalSiteRate) {
                        $average_notice_gross_up = 100;
                    } else {
                        if ($noticeCondition < 100) {
                            $average_notice_gross_up = 100;
                        } else if ($noticeCondition > $noticeCap) {
                            $average_notice_gross_up = $noticeCap;
                        } else {
                            $average_notice_gross_up = ($standardNotice / $averageNotice) * 100;
                        }
                    }
                    if (isset($reliabilityArray[$includedEmployee])) {
                        $totalAdjustment = (($reliabilityArray[$includedEmployee] / 100)
                            * ($average_wage_gross_up / 100) * ($average_notice_gross_up / 100)) * 100;
                    } else {
                        $totalAdjustment = 0;
                    }

                    $totalAdjustmentArray[$includedEmployee] = $totalAdjustment;
                    $insertDataArray[$includedEmployee] = [
                        "bonus_pool_id" => intval($settingData->id),
                        "user_id" => $includedEmployee,
                        "no_of_shifts_taken" => $noOfShiftsTaken,
                        "no_of_calls_made" => isset($eventsArray[$includedEmployee]) ?
                            $eventsArray[$includedEmployee] : 0,
                        "average_wage" => $average,
                        "average_wage_gross_up" => $average_wage_gross_up,
                        "average_notice" => $averageNotice,
                        "average_notice_gross_up" => floatval($average_notice_gross_up),
                        "reliability_score" => isset($reliabilityArray[$includedEmployee]) ?
                            intval($reliabilityArray[$includedEmployee]) : 0,
                        "total_adjustment" => floatval($totalAdjustment),
                        "adjusted_bonus" => 0,
                        "unadjusted_bonus" => 0,
                        "rank" => 0,
                        "created_by" => null !== (\Auth::user()) ? \Auth::user()->id : 0,
                        "created_at" => Carbon::now()->format("Y-m-d h:i:s"),
                        "updated_at" => Carbon::now()->format("Y-m-d h:i:s")
                    ];
                }
            }

            $perShiftAmount = 0;
            if ($settingData->bonus_amount > 0 && $noOfShifts > 0) {
                $perShiftAmount = $settingData->bonus_amount / $totalAssignedShifts;
            }
            $totalUnadjustedBonus = 0;
            $unadjustedEmployeeArray = [];
            foreach ($totalAdjustmentArray as $employeeId => $value) {
                if ($employeeId != "") {
                    $perEmployeeTotalAdjustment = $value;
                    $shiftsTaken = count($employeeRates[$employeeId]);
                    $perEmployeeUnAdjustedBonus = (($perEmployeeTotalAdjustment / 100) * $shiftsTaken) * $perShiftAmount;
                    $totalUnadjustedBonus += $perEmployeeUnAdjustedBonus;
                    $insertDataArray[$employeeId]["unadjusted_bonus"] = $perEmployeeUnAdjustedBonus;
                    $unadjustedEmployeeArray[$employeeId] = $perEmployeeUnAdjustedBonus;
                }
            }
            if ($totalUnadjustedBonus > 0) {
                $calculation = $bonusPoolAmount / $totalUnadjustedBonus;
                foreach ($totalAdjustmentArray as $employeeId => $value) {
                    $adjustedBonus = $unadjustedEmployeeArray[$employeeId] * $calculation;
                    $insertDataArray[$employeeId]["adjusted_bonus"] = $adjustedBonus;
                    if ($employeeId == 2538) {
                        // dd();
                    }
                    # code...
                }
            }
            array_multisort(array_map(function ($element) {
                return $element['adjusted_bonus'];
            }, $insertDataArray), SORT_DESC, $insertDataArray);
            $i = 0;
            foreach ($insertDataArray as $key => $value) {
                $i++;
                $insertDataArray[$key]["rank"] = $i;
            }
            BonusEmployeeData::insert([
                "bonus_pool_id" => intval($bonusId),
                "rank_data" => $insertDataArray,
                'average_site_rate' => ($totalSiteRate > 0 && $noOfShifts > 0) ? $totalSiteRate / $noOfShifts : 0,
                "rank_day" => $calculationDate,
                "average_accepted_rate" => ($totalAcceptedRate > 0 && $noOfShifts > 0) ? $totalAcceptedRate / $noOfShifts : 0,
                "per_shift_amount" => $perShiftAmount,
                "created_at" => Carbon::now()->format("Y-m-d h:i:s "),
                "updated_at" => Carbon::now()->format("Y-m-d h:i:s ")
            ]);
            return $insertDataArray;
        } else {
            return [];
        }
    }

    public function getUserBonusData()
    {
        $userId = \Auth::user()->id;
        $bonusData = [];
        $activeBonusProgram = BonusSettings::where("active", 1)->first();
        $colorMapping = SpareBonusModelSetting::find(1);
        $ytd = BonusFinalizedData::where("user_id", \Auth::user()->id)->sum("adjusted_bonus");
        if ($activeBonusProgram) {
            $bonusData = BonusEmployeeData::where("bonus_pool_id", intval($activeBonusProgram->id))->first();
            $rankData = $bonusData->rank_data;
            $noOfEmployees = 0;
            foreach ($rankData as $key => $data) {
                $noOfEmployees++;
                if ($data["user_id"] == $userId) {
                    $bonusData = $data;
                    $bonusData["average_wage_gross_up"] = round($bonusData["average_wage_gross_up"] / 100, 2);
                    $bonusData["average_notice_gross_up"] = round($bonusData["average_notice_gross_up"] / 100, 2);
                    $bonusData["total_adjustment"] = round($bonusData["total_adjustment"] / 100, 2);
                    $bonusData["adjusted_bonus"] = round($bonusData["adjusted_bonus"], 2);
                    $bonusData["bonus_multiplier"] = round($bonusData["adjusted_bonus"] / 100, 2);
                    $bonusData["unadjusted_bonus"] = round($bonusData["unadjusted_bonus"], 2);
                    $bonusData["ytd"] = $ytd;
                    if ($bonusData["average_wage_gross_up"] < 1) {
                        $bonusData["average_wage_gross_up_color"] = "red";
                    } else if ($bonusData["average_wage_gross_up"] == 1) {
                        $bonusData["average_wage_gross_up_color"] = "yellow";
                    } else if ($bonusData["average_wage_gross_up"] > 1) {
                        $bonusData["average_wage_gross_up_color"] = "green";
                    }

                    if ($bonusData["average_notice_gross_up"] < 1) {
                        $bonusData["average_notice_gross_up_color"] = "red";
                    } else if ($bonusData["average_notice_gross_up"] == 1) {
                        $bonusData["average_notice_gross_up_color"] = "yellow";
                    } else if ($bonusData["average_notice_gross_up"] > 1) {
                        $bonusData["average_notice_gross_up_color"] = "green";
                    }


                    if (!isset($colorMapping)) {
                        if ($bonusData["reliability_score"] > 90) {
                            $bonusData["reliability_score_color"] = "green";
                            $bonusData["reliability_score_fontcolor"] = "white";
                        } else if ($bonusData["reliability_score"] >= 50) {
                            $bonusData["reliability_score_color"] = "yellow";
                            $bonusData["reliability_score_fontcolor"] = "black";
                        } else if ($bonusData["reliability_score"] < 50) {
                            $bonusData["reliability_score_fontcolor"] = "white";
                            $bonusData["reliability_score_color"] = "red";
                        }
                        if ($bonusData["rank"] > 10) {
                            $bonusData["rank_color"] = "red";
                            $bonusData["rank_fontcolor"] =
                                "#fff";
                            $bonusData["rank_text"] = "Significant improvement required !";
                        } else if ($bonusData["rank"] <= 10 && $bonusData["rank"] > 3) {
                            $bonusData["rank_color"] = "green";
                            $bonusData["rank_text"] = "Congratulations you are on the Top 10";
                        } else if ($bonusData["rank"] <= 3) {
                            $bonusData["rank_color"] = "green";
                            $bonusData["rank_text"] = "Congratulations Your Rank is " . $bonusData["rank"];
                        }
                    } else {
                        if ($bonusData["reliability_score"] >= $colorMapping->reliability_safe_score) {
                            $bonusData["reliability_score_color"] = $colorMapping->reliability_safe_score_color_code;
                            $bonusData["reliability_score_fontcolor"] = $colorMapping->reliability_safe_score_font_color_code;
                        } else if (
                            $bonusData["reliability_score"] >= $colorMapping->reliability_grace_period_in_days
                            && $bonusData["reliability_score"] < $colorMapping->reliability_safe_score
                        ) {
                            $bonusData["reliability_score_color"] = $colorMapping->reliability_grace_period_color_code;
                            $bonusData["reliability_score_fontcolor"] = $colorMapping->reliability_grace_period_font_color_code;
                        } else if ($bonusData["reliability_score"] < $colorMapping->reliability_grace_period_in_days) {
                            $bonusData["reliability_score_color"] = $colorMapping->reliability_alert_period_color_code;
                            $bonusData["reliability_score_fontcolor"] = $colorMapping->reliability_alert_period_font_color_code;
                        }

                        if ($bonusData["rank"] > $colorMapping->reliability_rank_average_level) {
                            $bonusData["rank_color"] = "red";
                            $bonusData["rank_fontcolor"] =
                                "#fff";
                            $bonusData["rank_text"] = $colorMapping->schedule_below_average_rank_message;
                        } else if (
                            $bonusData["rank"] <= $colorMapping->reliability_rank_average_level
                            && $bonusData["rank"] > $colorMapping->reliability_rank_top_level
                        ) {
                            $bonusData["rank_color"] =
                                $colorMapping->reliability_rank_average_level_color_code;
                            $bonusData["rank_fontcolor"] =
                                $colorMapping->reliability_rank_average_level_font_color_code;
                            $bonusData["rank_text"] = $colorMapping->schedule_average_rank_message;
                        } else if ($bonusData["rank"] <= $colorMapping->reliability_rank_top_level) {
                            $bonusData["rank_color"] =
                                $colorMapping->reliability_rank_top_level_color_code;
                            $bonusData["rank_fontcolor"] =
                                $colorMapping->reliability_rank_top_level_font_color_code;
                            $bonusData["rank_text"] = $colorMapping->schedule_top_rank_message;
                        }
                    }
                }
            }
        }
        if ($noOfEmployees > 0) {
            $bonusData["noOfEmployees"] = $noOfEmployees;
        }
        return $bonusData;
    }

    public function processFinalData($poolId)
    {
        $dataArray = [];
        $bonusEmployeeData = BonusEmployeeData::where("bonus_pool_id", $poolId)
            ->orderBy("rank_day", "desc")->first();
        $bonusSetting = BonusSettings::find($poolId);
        $perShiftAmount = 0;
        $bonusPoolAmount = $bonusSetting->bonus_amount;

        if ($bonusEmployeeData) {
            $perShiftAmount = $bonusEmployeeData->per_shift_amount;
            $bonusPoolAmount = $bonusSetting->bonus_amount;
            BonusFinalizedData::where("bonus_pool_id", $poolId)->update([
                "unadjusted_bonus" => \DB::raw("((total_adjustment/100)*no_of_shifts_taken)*" . $perShiftAmount)
            ]);
            $totalUnAdjusted = BonusFinalizedData::where("bonus_pool_id", $poolId)->sum("unadjusted_bonus");
            if ($totalUnAdjusted > 0) {
                $calculation = $bonusPoolAmount / $totalUnAdjusted;
                BonusFinalizedData::where("bonus_pool_id", $poolId)->update([
                    "adjusted_bonus" => \DB::raw("(unadjusted_bonus)*" . $calculation)
                ]);
            }
            $bonusSetting->active = 2;
            $bonusSetting->save();
        }
    }
}
