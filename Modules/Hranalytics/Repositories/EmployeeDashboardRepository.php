<?php

namespace Modules\Hranalytics\Repositories;

use App\Services\HelperService;
use Modules\Admin\Models\User;
use Auth;
use Modules\Admin\Models\EmployeeComplianceReports;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Modules\Client\Models\ClientEmployeeFeedback;
use Modules\Hranalytics\Models\UserRating;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Models\StcReportingTemplateRule;
use Modules\Admin\Models\DocumentExpiryColorSettings;
use Modules\Admin\Models\SiteSettings;

class EmployeeDashboardRepository
{
    protected $schedulingRepository, $userRepository;
    public function __construct(
        SchedulingRepository $schedulingRepository,
        UserRepository $userRepository
    ) {
        $this->user_courses = new TrainingUserCourseAllocationRepository();
        $this->schedulingRepository = $schedulingRepository;
        $this->userRepository = $userRepository;
    }

    public function getEmployeeComplianceDashboardData()
    {
        $returnArray = [];
        $employeeComplianceReports = EmployeeComplianceReports::with(
            ["EmployeeMobileDashboard" => function ($q) {
                return $q->when(Auth::user()->id > 0, function ($qry) {
                    // return $qry->selectRaw("(select count(*) from 
                    // employee_mobile_dashboards where user_id=? and report_id=employee_mobile_dashboards.id) as exludeCount", [\Auth::user()->id]);
                    return $qry->where("user_id", '=', Auth::user()->id);
                });
            }]
        )->where("active", 1)->get();

        foreach ($employeeComplianceReports as $employeeComplianceReport) {
            if (
                $employeeComplianceReport->EmployeeMobileDashboard() == null
                || count($employeeComplianceReport->EmployeeMobileDashboard) < 1
            ) {
                $helpArray = [];
                $reportData = $this->getProcessCompliancedata($employeeComplianceReport->report_name, $helpArray);
                if (count($reportData) > 0) {
                    $returnArray[] = [
                        "id" => $employeeComplianceReport->id,
                        "report_name" => $employeeComplianceReport->report_name,
                        "display_name" => $employeeComplianceReport->display_name,
                        "display_data" => $reportData[0],
                        "help_array" => $reportData[1]

                    ];
                }
            }
        }
        return $returnArray;
    }

    public function getProcessCompliancedata($moduleName, $helpArray)
    {
        $data = [];
        if ($moduleName == "schedule_compliance") {
            $tolerance = SiteSettings::find(1);
            $documentExpiryColorSettings = DocumentExpiryColorSettings::find(1);
            $gracePeriod = $documentExpiryColorSettings->schedule_grace_period_days;
            $alertPeriod = $documentExpiryColorSettings->schedule_alert_period_days;

            $schedule_grace_period_color_code = $documentExpiryColorSettings->schedule_grace_period_color_code;
            $schedule_grace_period_font_color_code = $documentExpiryColorSettings->schedule_grace_period_font_color_code;

            $schedule_alert_color_code = $documentExpiryColorSettings->schedule_alert_color_code;
            $schedule_alert_period_font_color_code = $documentExpiryColorSettings->schedule_alert_period_font_color_code;

            $shiftStartTimeTolerance = $tolerance->shift_end_time_tolerance;
            $helpArray["schedule_compliance"]["green"] = "You are not late/early out";
            $helpArray["schedule_compliance"]["yellow"] = "You are late/early out with the given concession period";
            $helpArray["schedule_compliance"]["red"] = "You have exceeded late show /early out period";
            $helpArray["schedule_compliance"]["black"] = "No show";
            $today = date('Y-m-d', strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 day"));
            //$today = date("Y-m-d");
            $timedateBeforeOneYear = date('Y-m-d', strtotime(date("Y-m-d", strtotime($today)) . "-1 years"));

            $scheduleCompData = $this->schedulingRepository->fetchScheduleComplianceByPayperiods(
                1,
                1000,
                $timedateBeforeOneYear,
                $today,
                null,
                null,
                [\Auth::user()->id],
                null,
                null,
                1
            );
            $totalNoOfShifts = 0;
            $onTimeShifts = 0;
            $totalEarlyShifts = 0;
            $noShow = 0;
            $lateShow = 0;
            foreach ($scheduleCompData["records"] as $key => $schedData) {
                if ($schedData["late_in_minutes"] == 0 && $schedData["noShow"] === false) {
                    //dump("early", $schedData);
                    $onTimeShifts++;
                } else if ($schedData["late_in_minutes"] > 0 && $schedData["noShow"] === false) {
                    //dump("late", $schedData);
                    // if ($shiftStartTimeTolerance >= $schedData["late_in_minutes"]) {
                    //     $onTimeShifts++;
                    // } else {

                    // }
                    $lateShow++;
                }
                if ($schedData["early_out_minutes"] > 0 && $schedData["noShow"] === false) {
                    $totalEarlyShifts++;
                }
                if ($schedData["noShow"] === true) {
                    $noShow++;
                }
                $totalNoOfShifts++;
            }
            if ($totalNoOfShifts == 0 || $totalEarlyShifts == 0) {
                $fullShift = 0;
            } else {
                $fullShift = ($totalEarlyShifts / $totalNoOfShifts) * 100;
            }
            if ($lateShow == 0 || $totalNoOfShifts == 0) {
                $onTime = 0;
            } else {
                $onTime = (($lateShow / $totalNoOfShifts) * 100);
            }

            if ($noShow > 0) {
                $noShow = ($noShow / $totalNoOfShifts) * 100;
            }


            $onTimeColor = "text-align:center;background:green;color:#fff";
            $noShowColor = "text-align:center;";
            if ($noShow > 0) {
                $noShowColor = "text-align:center;background:#000;color:#fff";
            }
            if ($totalNoOfShifts == 0) {
                $onTimeColor = "text-align:center;";
            } else if ($onTime > 0 && $onTime <= $gracePeriod) {
                $onTimeColor = "text-align:center;background:" . $schedule_grace_period_color_code . ";color:" . $schedule_grace_period_font_color_code;
            } else if ($onTime > $gracePeriod) {
                $onTimeColor = "text-align:center;background:" . $schedule_alert_color_code . ";color:" . $schedule_alert_period_font_color_code;
            }
            $fullShiftColor = "text-align:center;background:green;color:#fff";
            if ($totalNoOfShifts == 0) {
                $fullShiftColor = "text-align:center;";
            } elseif ($fullShift > 0 && $fullShift <= $gracePeriod) {
                $fullShiftColor = "text-align:center;background:" . $schedule_grace_period_color_code . ";color:" . $schedule_grace_period_font_color_code;
            } else if ($fullShift > $gracePeriod) {
                $fullShiftColor = "text-align:center;background:" . $schedule_alert_color_code . ";color:" . $schedule_alert_period_font_color_code;
            }

            $data = [
                [
                    "Total Shifts", $totalNoOfShifts, "Total number of shifts",
                    "text-align:center"
                ],
                [
                    "Late show", round($onTime, 2) . " %", "Number of times user was not present on time for the shift.",
                    $onTimeColor
                ],
                [
                    "Early checkout", round($fullShift, 2) . " %", "Number of times user checked out early",
                    $fullShiftColor
                ],
                [
                    "No Show", round($noShow, 2) . " %", "Number of times user was not present for the shift.",
                    $noShowColor
                ]
            ];
        } else if ($moduleName == "training_compliance") {
            $recommendedCount = $this->user_courses->getRecommendedCourseCount();
            $completedCount = $this->user_courses->getCompletedCount();
            $completedMandatoryCount = $this->user_courses->getMandatoryCompletedCount();
            $overDueCount = $this->user_courses->getOverDueCountCount();
            $totalMandatoryCourseLibrary = $this->user_courses->getMandatoryCourseLibraryCount();
            $totalCourseLibrary = $totalMandatoryCourseLibrary + $recommendedCount;

            $complianceColor = "text-align:center;color:#000";
            //dd($totalMandatoryCourseLibrary, $completedMandatoryCount);
            //$compliance = $totalMandatoryCourseLibrary - $completedMandatoryCount;
            $compliance = 0;
            if ($completedMandatoryCount > 0) {
                $compliance = round($completedMandatoryCount / $totalMandatoryCourseLibrary, 2) * 100;
            }
            if ($compliance >= 0 &&  $compliance <= 10 && $overDueCount < 1) {
                if ($totalMandatoryCourseLibrary > 0) {
                    $complianceColor = "text-align:center;background:red;color:#fff";
                }
            } else if ($compliance < 100 && $overDueCount > 0) {
                $complianceColor = "text-align:center;background:red;color:#fff";
            } else if ($compliance > 10 && $compliance < 100) {
                $complianceColor = "text-align:center;background:yellow;color:#000";
            }
            if ($compliance >= 100) {
                $complianceColor = "text-align:center;background:green;color:#fff";
            }

            $helpArray["training_compliance"]["green"] = "You are not completed courses assigned on expected time";
            $helpArray["training_compliance"]["yellow"] = "You have pending courses";
            $helpArray["training_compliance"]["red"] = "You have overdue courses";
            // $recent_achievements = $this->user_courses->getRecentAchivements();
            $data = [
                [
                    "Total Course Assigned", $totalCourseLibrary, "Mandatory + Recommended Courses",
                    "text-align:center;"
                ],
                [
                    "Mandatory Course Assigned", $totalMandatoryCourseLibrary, "Course assigned to the user",
                    "text-align:center;"
                ],
                [
                    "Recommended Courses", $recommendedCount, "Recommended courses",
                    "text-align:center;"
                ],
                [
                    "Courses Completed", $completedCount, "Completed Courses (Mandatory + Recommended)",
                    "text-align:center;"
                ],
                [
                    "Mandatory Compliance", $compliance . " %", "Percentage of Mandatory Compliance",
                    $complianceColor
                ]
            ];
        } else if ($moduleName == "performance_reviews") {
            $id = Auth::user()->id;
            $employeeRatings = UserRating::with('user', 'userRating', 'policyDetails')->where('employee_id', $id)->orderBy('created_at', 'desc')->get();
            $clientRatings = ClientEmployeeFeedback::where('user_id', $id)->with(['createdUser', 'userRating'])->get();
            $noOfEmployeeRating = 0;
            $empRatingScore = 0;

            foreach ($employeeRatings as $employeeRating) {
                $empRatingScore = $empRatingScore + $employeeRating->userRating->score;
                $noOfEmployeeRating++;
            }

            $noOfClientRating = 0;
            $clientRatingScore = 0;
            foreach ($clientRatings as $clientRating) {
                $clientRatingScore = $clientRatingScore + $clientRating->userRating->score;
                $noOfClientRating++;
            }
            $color = "text-align:center;background:green;color:#fff";
            $averageEmployeeRating = 0;
            if ($empRatingScore != 0 && $noOfEmployeeRating != 0) {
                $averageEmployeeRating = number_format($empRatingScore / $noOfEmployeeRating, 2, '.', '');
            }
            $averageClientRating = 0;
            if ($clientRatingScore != 0 && $noOfClientRating != 0) {
                $averageClientRating = number_format($clientRatingScore / $noOfClientRating, 2, '.', '');
            }
            if ($averageEmployeeRating > 0 && $averageClientRating > 0) {
                $overAll = ($averageEmployeeRating + $averageClientRating) / 2;
            } else {
                $overAll = ($averageEmployeeRating + $averageClientRating);
            }
            if (($overAll) == 0) {
                $color = "text-align:center;";
            } else if (($overAll) <= 2) {
                $color = "text-align:center;background:red;color:#fff";
            } else if ($overAll <= 3.5) {
                $color = "text-align:center;background:yellow;color:#000";
            } else if ($overAll <= 5) {
                $color = "text-align:center;background:green;color:#fff";
            } else if ($overAll < 1) {
                $color = "text-align:center;";
            }

            $helpArray["performance_reviews"]["green"] = "You are not completed courses assigned on expected time";
            $helpArray["performance_reviews"]["yellow"] = "Range between 2 and 3.5";
            $helpArray["performance_reviews"]["red"] = "Range greater than 3.5";


            $data = [
                [
                    "Manager", $averageEmployeeRating, "Performance review by Manager",
                    "text-align:center;"
                ],
                [
                    "Client", $averageClientRating, "Performance review by Client",
                    "text-align:center;"
                ],
                [
                    "Overall Score", number_format($overAll, 2, '.', ''), "Overall Performance",
                    $color
                ]
            ];
        } else if ($moduleName == "spares_compliance") {
            $userDetails = User::with(["eventlog", "eventlog_score"])->find(Auth::user()->id);
            $value = [];
            $value['prev_attempt'] = (null !== ($userDetails->eventlog_score->first())) ? $userDetails->eventlog_score->first()->prev_attempt : '';
            $value['avg_score'] = (null !== ($userDetails->eventlog_score->first())) ? $userDetails->eventlog_score->first()->avg_score : '';
            $value['acceped_shifts'] = (null !== ($userDetails->eventlog_score->first())) ? $userDetails->eventlog_acceptedshifts->count() : '';
            $avg_score = $value['avg_score'];
            $template_setting_rules = StcReportingTemplateRule::whereRaw('? between min_value and max_value', [$avg_score])
                ->first();
            $backgroundColor = "green";
            $backColor = "white";
            $color = "";

            if (isset($template_setting_rules)) {
                $backgroundColor = $template_setting_rules->color->color_class_name;
                $helpArray["spares_compliance"][$template_setting_rules->color->color_class_name] = "You range between " . $template_setting_rules->min_value . " and " . $template_setting_rules->max_value;
                if ($template_setting_rules->color->color_class_name == "yellow") {
                    $backColor = "#000000";
                }
            }
            if ($value['prev_attempt'] > 0) {
                $color = "text-align:center;background:" . $backgroundColor . ";color:" . $backColor;
            }

            $data = [
                [
                    "Number Of Attempts", $value['prev_attempt'], "Total number of attempts",
                    "text-align:center;"
                ],
                [
                    "Accepted Shifts", $value['acceped_shifts'], "Number of times user accepted shift",
                    "text-align:center;"
                ],
                [
                    "Reliability Score", $value['avg_score'], "Score to identify users willingness to work",
                    $color
                ]
            ];
        } else if ($moduleName == "license_compliance") {
            $pageVariablesArr = User::with(["securityClearanceUser", "userCertificate"])
                ->find(Auth::user()->id);
            $userCertificates = $pageVariablesArr->userCertificate;
            //$userCertificates = $certificates;
            $documentExpiryColorSettings = DocumentExpiryColorSettings::find(1);
            $gracePeriod = $documentExpiryColorSettings->grace_period_in_days;
            $alertPeriod = $documentExpiryColorSettings->alert_period_in_days;
            $data = [];
            foreach ($userCertificates as $userCertificate) {
                try {
                    $expiresOn = $userCertificate->expires_on;
                    $Certificate = $userCertificate->certificateMaster->certificate_name;
                    $now = strtotime(date(date("Y-m-d")));
                    //dd($now, $expiresOn);
                    $expiresOnDate = strtotime($expiresOn);
                    $datediff = $expiresOnDate - $now;
                    $differenceDates = 0;
                    $differenceDates = round($datediff / (60 * 60 * 24));


                    $color = "text-align:center;background:green;color:#fff";
                    $helpArray["license_compliance"]["green"] = "Safe period";

                    if ($differenceDates <= $gracePeriod && $differenceDates > $alertPeriod) {
                        $color = "text-align:center;background:" . $documentExpiryColorSettings->grace_period_color_code . ";color:" . $documentExpiryColorSettings->grace_period_font_color_code;
                        $helpArray["license_compliance"][$documentExpiryColorSettings->grace_period_color_code] = "You are in grace range";
                    } else if ($differenceDates <= $alertPeriod && $differenceDates >= 0) {
                        $color = "text-align:center;background:" . $documentExpiryColorSettings->alert_period_color_code . ";color:" . $documentExpiryColorSettings->alert_period_font_color_code;
                        $helpArray["license_compliance"][$documentExpiryColorSettings->alert_period_color_code] = "You are in alert range";
                    } else if ($differenceDates < 0) {
                        $color = "text-align:center;background:" . $documentExpiryColorSettings->overdue_period_color_code . ";color:" . $documentExpiryColorSettings->overdue_period_font_color_code;
                        $helpArray["license_compliance"]["black"] = "Exceeded expected date range";
                    }
                    $data[] = [
                        $Certificate, $differenceDates, "License expires within given number of dates",
                        $color
                    ];
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        } else if ($moduleName == "clearences") {
            $pageVariablesArr = User::with(["securityClearanceUser", "userCertificate"])
                ->find(Auth::user()->id);
            $userCertificates = $pageVariablesArr->securityClearanceUser;
            //$userCertificates = $certificates;
            $documentExpiryColorSettings = DocumentExpiryColorSettings::find(1);
            $gracePeriod = $documentExpiryColorSettings->grace_period_in_days;
            $alertPeriod = $documentExpiryColorSettings->alert_period_in_days;
            $data = [];
            foreach ($userCertificates as $userCertificate) {
                try {
                    $expiresOn = $userCertificate->valid_until;
                    $certificate = $userCertificate->securityClearanceLookups->security_clearance;
                    $now = strtotime(date(date("Y-m-d")));
                    $expiresOndate = strtotime($expiresOn);
                    $dateDiff = $expiresOndate - $now;
                    $color = "text-align:center;background:green;color:#fff";
                    $differenceDates = 0;

                    $differenceDates = round($dateDiff / (60 * 60 * 24));
                    $helpArray["clearences"]["green"] = "Safe period";
                    if ($differenceDates <= $gracePeriod && $differenceDates > $alertPeriod) {
                        $color = "text-align:center;background:" . $documentExpiryColorSettings->grace_period_color_code . ";color:" . $documentExpiryColorSettings->grace_period_font_color_code;
                        $helpArray["clearences"][$documentExpiryColorSettings->grace_period_color_code] = "You are in grace range";
                    } else if ($differenceDates <= $alertPeriod && $differenceDates >= 0) {
                        $color = "text-align:center;background:" . $documentExpiryColorSettings->alert_period_color_code . ";color:" . $documentExpiryColorSettings->alert_period_font_color_code;
                        $helpArray["clearences"][$documentExpiryColorSettings->alert_period_color_code] = "You are in alert range";
                    } else if ($differenceDates < 0) {
                        $color = "text-align:center;background:" . $documentExpiryColorSettings->overdue_period_color_code . ";color:" . $documentExpiryColorSettings->overdue_period_font_color_code;
                        $helpArray["clearences"]["black"] = "Exceeded expected date range";
                    }
                    $data[] = [
                        $certificate, $differenceDates,
                        "Clearance expires within given number of dates",
                        $color
                    ];
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }

        return [$data, $helpArray];
    }
}
