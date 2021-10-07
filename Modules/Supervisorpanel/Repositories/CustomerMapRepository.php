<?php

namespace Modules\Supervisorpanel\Repositories;

use Auth;
use Carbon\Carbon;
use Modules\Admin\Models\Template;
use Modules\Admin\Models\TemplateQuestionsCategory;
use Modules\Admin\Models\TemplateSetting;
use Modules\Admin\Models\TemplateSettingRules;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Supervisorpanel\Models\CustomerPayperiodTemplate;
use Modules\Supervisorpanel\Models\CustomerReport;
use Modules\Supervisorpanel\Repositories\GuardTourRepository;
use Illuminate\Support\Arr;

class CustomerMapRepository
{

    protected $customer_repository;
    protected $customer_employee_allocation_repository;
    protected $employee_allocation_repository;
    private $question_category_average;

    public function __construct(CustomerRepository $customer_repository, CustomerEmployeeAllocationRepository $customer_employee_allocation_repository, EmployeeAllocationRepository $employee_allocation_repository, PayPeriodRepository $pay_period_respository, GuardTourRepository $guard_tour_repository) //CustomerReportRepository $customerReportRepository

    {
        $this->customer_repository = $customer_repository;
        $this->customer_employee_allocation_repository = $customer_employee_allocation_repository;
        $this->employee_allocation_repository = $employee_allocation_repository;
        $this->pay_period_respository = $pay_period_respository;
        $this->question_category_average = array();
        $this->guard_tour_repository = $guard_tour_repository;
        // $this->customerReportRepository = $customerReportRepository;
    }

    public function getCustomerMapDetails(
        $current_template,
        $stc,
        $request = null,
        $no_user_details = false,
        $customer_id = null
    ) {

        $customers = array();
        $customers_fetch = array();
        $customer_score = array();
        $score_sort_arr = array();
        $current_template_id = ($current_template->id) ?? null;
        $customer_rating = config('supervisorpanel.customer_rating');
        $customer_rating_color = config('supervisorpanel.customer_rating_color');
        //get accessible customers
        if ($stc == "stc") {
            $customers_fetch = $this->customer_employee_allocation_repository->getAllocatedStcCustomers(Auth::user());
        } elseif ($stc == "guard_tour") {
            //$guard_tour_enabled = 1;
            /*$customer_arr_perm = $this->customer_employee_allocation_repository->getAllocatedPermanentCustomers(Auth::user());
            $customer_arr_stc = $this->customer_employee_allocation_repository->getAllocatedStcCustomers(Auth::user());
            $customers_fetch = array_unique(array_merge($customer_arr_perm, $customer_arr_stc));*/
            /*$customers_fetch = $this->customer_employee_allocation_repository->getAllAllocatedGuardTourCustomerId(Auth::user(), $guard_tour_enabled);*/
            $customers_fetch = $this->customer_employee_allocation_repository->getGuardTourCustomers(Auth::user());
        } elseif (isset($customer_id)) {
            $customers_fetch = [$customer_id];
        } else {
            $customers_fetch_arr = $this->customer_employee_allocation_repository->getAllocatedPermanentCustomers(Auth::user());
            $customers_fetch = $this->customer_repository->getAllShowSiteStatusCustomerId($customers_fetch_arr);
        }

        if (($request != null ) && (($request->get('area_manager') != null) || ($request->get('supervisor') != null)) ) {
            $no_user_details = false;
        }else{
            $no_user_details = true;
        }  

        //get customer list;
        $customers = $this->customer_repository->getCustomerMap($customers_fetch, $current_template_id, $no_user_details);

        $payperiod_update_limit = TemplateSetting::first()->pluck('last_update_limit');
        $current_payperiod = $this->pay_period_respository->getCurrentPayperiod();
        $allowed_payperiods = $this->pay_period_respository->getLastNPayperiod($payperiod_update_limit[0])->pluck('id');

        $allowed_payperiods_arr = $allowed_payperiods->toArray();
        $year_to_date_arr = $this->pay_period_respository->yearToDatePayperiod();
        if (isset($current_payperiod->id)) {
            array_push($year_to_date_arr, $current_payperiod->id);
        }
        if (isset($current_payperiod->id)) {
            array_push($allowed_payperiods_arr, $current_payperiod->id);
        }
        foreach ($customers as $key => $each_customer) {

            $last_updated = null;
            $score_arr = [];
            $last_report_key = count($each_customer->customerPayperiodTemplate) - 1;
            $last_report_payperiod = ($each_customer->customerPayperiodTemplate[$last_report_key]->payperiod_id) ?? null;
            //dd($last_report_key, $last_report_payperiod);
            if (isset($each_customer->customerPayperiodTemplate[$last_report_key])) {
                $last_updated = $each_customer->customerPayperiodTemplate[$last_report_key]->customerReport->first()->updated_at;
            }
            $cust = $this->getPayperiodAvgReport($each_customer->id, $year_to_date_arr);
            $average_score_arr_1 = (!empty($cust['score'])) ? $cust['score'] : array('total' => 0);
            $score_class_1 = (!empty($cust['color_class'])) ? $cust['color_class'] : array('total' => 'black');
            if ($cust == false) {
                $default_color = $this->getDefaultColor();
                $average_score_arr_1 = ["total" => SURVEY_DEFAULT_SCORE];
                $score_class_1 = ["total" => $default_color];
            }

            if (isset($each_customer->customerPayperiodTemplate[$last_report_key]) && in_array($last_report_payperiod, $allowed_payperiods_arr) && isset($current_template_id)) {

                $score_arr = $this->calculateScoreArr($each_customer, $last_report_key);
                $average_score_arr = (!empty($score_arr)) ? $this->getAverageScore($score_arr) : array('total' => 0);

                $score_class = (!empty($score_arr)) ? $this->getColorClassForRule($average_score_arr) : array('total' => 'gray'); //if no categories with average available

            } else {
                if ($stc == "guard_tour") {
                    $default_color = "";
                    $average_score_arr = ["total" => SURVEY_DEFAULT_SCORE];
                    $score_class = ["total" => $default_color];
                } else {

                    $default_color = $this->getDefaultColor();
                    $average_score_arr = ["total" => SURVEY_DEFAULT_SCORE];
                    $score_class = ["total" => $default_color];
                }
            }
            $last_update_date = isset($last_updated) ? Carbon::parse($last_updated)->toDateString() : "--";
            // cases for stc
            if ($stc == "stc") {
                $stc_score = ($each_customer->ratingDetails->first()->rating_id) ?? SURVEY_DEFAULT_SCORE;
                $stc_color_class = isset($each_customer->ratingDetails->first()->rating_id) ? $customer_rating_color[$each_customer->ratingDetails->first()->rating_id] : $default_color;
                $average_score_arr['total'] = $stc_score;
                $score_class = ["total" => $stc_color_class];
                $last_update_date = isset($each_customer->ratingDetails->first()->updated_at) ? Carbon::parse($each_customer->ratingDetails->first()->updated_at)->toDateString() : "--";
            }
            // cases for guard_tour
            if ($stc == "guard_tour") {
                $stc_score = ($each_customer->ratingDetails->first()->rating_id) ?? SURVEY_DEFAULT_SCORE;
                $average_score_arr['total'] = $stc_score;
                $last_update_date = isset($each_customer->ratingDetails->first()->updated_at) ? Carbon::parse($each_customer->ratingDetails->first()->updated_at)->toDateString() : "--";
                $guard_tour_duration = $each_customer->guard_tour_duration;
                $shiftPayperiods = $each_customer->employeeShiftPayperiods;
                if (isset($guard_tour_duration) && (!empty($shiftPayperiods)) && ($guard_tour_duration != 0) && (!$shiftPayperiods->isEmpty())) {
                    $recent_shift = $this->guard_tour_repository->getLatestShift($shiftPayperiods);
                    $result = $this->guard_tour_repository->getGuardTourCount($recent_shift);
                    if (intval($result['expected']) <= $result['actual'] && intval($result['expected']) != 0) {
                        $score_class = ["total" => 'green'];
                    } else {
                        $score_class = ["total" => 'blue'];
                    }
                } else {
                    $score_class = ["total" => 'blue'];
                }
            }
            $customer_arr = $each_customer->toArray();
            if ($no_user_details) {
                $customer_score[$key]['customer']['details'] = Arr::except($customer_arr, ["customer_payperiod_template", "rating_details"]);
            } else {
                $customer_score[$key]['customer']['details'] = Arr::except($customer_arr, ["employee_latest_customer_supervisor", "employee_latest_customer_area_manager", "customer_payperiod_template", "rating_details"]);
                $customer_score[$key]['customer']['areamanager'] = $this->customer_repository->getManagerDetailsArr($each_customer, "area_manager");

                $customer_score[$key]['customer']['supervisor'] = $this->customer_repository->getManagerDetailsArr($each_customer, "supervisor");
            }
            $customer_score[$key]['customer']['rating_details'] = $each_customer->ratingDetails->toArray();
            $customer_score[$key]['last_update'] = $last_update_date;
            $customer_score[$key]['score_details']['score'] = $average_score_arr;
            $customer_score[$key]['score_details']['color_class'] = $score_class;
            $customer_score[$key]['score_details']['score_1'] = $average_score_arr_1;
            $customer_score[$key]['score_details']['color_class_1'] = $score_class_1;
            $score_sort_arr['actual'][$key] = $average_score_arr['total'];
            $score_sort_arr['ytd'][$key] = $average_score_arr_1['total'];
        }

        $customer_score = $this->getFilter($request, $customer_score, $score_sort_arr);
        $sort = null;
        if ($request != null) {
            $sort = $request->get('sort_order');
            $sort_param = $request->get('sort_param');
        }
        if (!empty($customer_score['customer_score'])) {
            if ($sort != null) {
                if ($sort_param != null && $sort == 0) {
                    array_multisort($customer_score['score_sort_arr'][$sort_param], SORT_NUMERIC, SORT_ASC, $customer_score['customer_score']);
                } else if ($sort_param != null && $sort == 1) {
                    array_multisort($customer_score['score_sort_arr'][$sort_param], SORT_NUMERIC, SORT_DESC, $customer_score['customer_score']);
                } else {
                    array_multisort($customer_score['score_sort_arr']['actual'], SORT_NUMERIC, SORT_ASC, $customer_score['customer_score']);
                }
            } else {
                array_multisort($customer_score['score_sort_arr'], SORT_NUMERIC, SORT_ASC, $customer_score['customer_score']);
            }
        } else {

            $customer_score['customer_score'] = array();
        }

        return ['customer_score' => $customer_score['customer_score'], 'customer_rating' => $customer_rating, 'customer_rating_color' => $customer_rating_color];
    }

    public function getQuestionCategoryAvg($category_id)
    {
        $question_category_obj = null;
        if (isset($this->question_category_average[$category_id])) {
            $question_category_obj = $this->question_category_average[$category_id];
        } else {
            $question_category = TemplateQuestionsCategory::withTrashed()->select('description', 'average')->find($category_id);
            $this->question_category_average[$category_id] = $question_category;
            $question_category_obj = $question_category;
        }

        return $question_category_obj;
    }

    public function getAverageScore($score_arr)
    {
        foreach ($score_arr as $category => $scores) {
            $category_avg[$category] = array_sum($scores) / count($scores);
        }

        $category_avg = ($category_avg) ?? array(0);
        $category_avg['total'] = array_sum($category_avg) / count($category_avg);
        return $category_avg;
    }

    public function getColorClassForRule($average_score_arr)
    {
        foreach ($average_score_arr as $category => $scores) {
            $colorObj = TemplateSettingRules::with('color')
                ->where('min_value', '<=', $scores)
                ->where('max_value', '>=', $scores)
                ->first();
            $avg_clr_class[$category] = ($colorObj->color->color_class_name) ?? $this->getDefaultColor();
        }
        return $avg_clr_class;
    }

    public function getDefaultColor()
    {
        $defaultColorObj = TemplateSetting::with('color')->first();
        return ($defaultColorObj->color->color_class_name) ?? "";
    }

    /**
     * Function to filter
     *
     * @param request,$allocated_customers
     * @return response
     */

    public function getFilter($request, $allocated_customers, $score_sort_arr)
    {
        $filtered_allocated_customers = array();
        $project_number_filter = true;
        $city_filter = true;
        $industry_sector_filter = true;
        $region_filter = true;
        $area_manager_filter = true;
        $supervisor_filter = true;
        $status_filter = true;
        $sort_filter = true;
        if ($request != null) {

            $project_number = $request->get('project_number');
            $city = $request->get('city');
            $industry_sector = $request->get('industry_sector');
            $region = $request->get('region');
            $area_manager = $request->get('area_manager');
            $supervisor = $request->get('supervisor');
            $status = $request->get('status');
            $sort = $request->get('sort_order');
            $sort_param = $request->get('sort_param');
        } else {
            $filtered_allocated_customer['customer_score'] = $allocated_customers;
            $filtered_allocated_customer['score_sort_arr'] = array();
            foreach ($allocated_customers as $key => $value) {
                $filtered_allocated_customer['score_sort_arr'][] = $value['score_details']['score']['total'];
            }

            return $filtered_allocated_customer;
        }

        $filter_value = array($project_number, $area_manager, $status, $city, $region, $industry_sector, $supervisor);
        if (count(array_filter($filter_value)) > 0) {
            foreach ($allocated_customers as $single_allocated_customer) {

                if ($project_number != null) {
                    $project_number_filter = ($single_allocated_customer['customer']['details']['project_number'] == $project_number);
                }
                if ($city != null) {
                    $city_filter = ($single_allocated_customer['customer']['details']['city'] == $city);
                }
                if ($industry_sector != null) {
                    $industry_sector_filter = ($single_allocated_customer['customer']['details']['industry_sector_lookup_id'] == $industry_sector);
                }
                if ($region != null) {
                    $region_filter = ($single_allocated_customer['customer']['details']['region_lookup_id'] == $region);
                }
                if ($area_manager != null) {
                    if (!empty($single_allocated_customer['customer']['areamanager'])) {
                        $area_manager_filter = ($single_allocated_customer['customer']['areamanager']['id'] == $area_manager);
                    } else {
                        $area_manager_filter = false;
                    }
                }
                if ($supervisor != null) {
                    if (!empty($single_allocated_customer['customer']['supervisor'])) {
                        $supervisor_filter = ($single_allocated_customer['customer']['supervisor']['id'] == $supervisor);
                    } else {
                        $supervisor_filter = false;
                    }
                }
                if ($status != null) {
                    if (!empty($single_allocated_customer['score_details']['color_class']['total'])) {
                        $status_filter = (strtolower($single_allocated_customer['score_details']['color_class']['total']) == strtolower($status));
                    } else {
                        $status_filter = false;
                    }
                }
                if (!in_array(false, array($project_number_filter, $city_filter, $industry_sector_filter, $region_filter, $area_manager_filter, $supervisor_filter, $status_filter))) {
                    $filtered_customers = $single_allocated_customer;
                    array_push($filtered_allocated_customers, $filtered_customers);
                }
            }
            if ($sort != null) {
                $filtered_allocated_customer['customer_score'] = $filtered_allocated_customers;
                $filtered_allocated_customer['score_sort_arr'] = array();
                foreach ($filtered_allocated_customers as $key => $value) {
                    $filtered_allocated_customer['score_sort_arr']['ytd'][] = $value['score_details']['score_1']['total'];
                    $filtered_allocated_customer['score_sort_arr']['actual'][] = $value['score_details']['score']['total'];
                }

                return $filtered_allocated_customer;
            } else {
                $filtered_allocated_customer['customer_score'] = $filtered_allocated_customers;
                $filtered_allocated_customer['score_sort_arr'] = array();
                foreach ($filtered_allocated_customers as $key => $value) {
                    $filtered_allocated_customer['score_sort_arr'][] = $value['score_details']['score']['total'];
                }
            }

            return $filtered_allocated_customer;
        } else {
            if ($sort != null) {
                $filtered_allocated_customer['customer_score'] = $allocated_customers;
                $filtered_allocated_customer['score_sort_arr'] = array();
                foreach ($allocated_customers as $key => $value) {
                    $filtered_allocated_customer['score_sort_arr']['ytd'][] = $value['score_details']['score_1']['total'];
                    $filtered_allocated_customer['score_sort_arr']['actual'][] = $value['score_details']['score']['total'];
                }
            } else {
                $filtered_allocated_customer['customer_score'] = $allocated_customers;
                $filtered_allocated_customer['score_sort_arr'] = array();
                foreach ($allocated_customers as $key => $value) {
                    $filtered_allocated_customer['score_sort_arr'][] = $value['score_details']['score']['total'];
                }
            }
            return $filtered_allocated_customer;
        }
    }

    /**
     *  To return payperiod average report
     *  @param  $customer_id
     *  @return  array
     */
    public function getPayperiodAvgReport($customer_id, $payperiod, $customerTemplatePayperiod = null)
    {
        $current_categories = array();
        $customer_result = array();
        $average_score_arr = array();
        $score_class = array();
        $current_template_id = Template::select('id')
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->where('active', true)
            ->pluck('id')
            ->first();

        if ($customerTemplatePayperiod == null) {
            $qry = CustomerPayperiodTemplate::select('id')
                ->whereIn('payperiod_id', $payperiod)
                ->where('template_id', $current_template_id);
            if (is_array($customer_id) && !empty($customer_id)) {
                $qry->whereIn('customer_id', $customer_id);
            } elseif (!empty($customer_id)) {
                $qry->where('customer_id', $customer_id);
            }
            $customerTemplatePayperiod = $qry->orderby('created_at', 'desc')
                ->pluck('id')
                ->toArray();
        }
        if (empty(!$customerTemplatePayperiod)) {
            $customer_reports = CustomerReport::with('templateFormWithTrashed')
                ->whereIn('customer_payperiod_template_id', $customerTemplatePayperiod)
                ->get();
            $score_arr = [];
            $i = 1;
            foreach ($customer_reports as $eachval) {
                $has_avg = $this->getQuestionCategoryAvg($eachval->templateFormWithTrashed->question_category_id)->average;
                $question_type = $this->getQuestionCategoryAvg($eachval->templateFormWithTrashed->question_category_id)->description;
                if (strtolower($has_avg) == "yes" && $eachval->score !== null) {
                    $score_arr[$question_type] = ($score_arr[$question_type]) ?? array();
                    array_push($score_arr[$question_type], $eachval->score);
                }
                $i++;
            }

            $average_score_arr = (!empty($score_arr)) ? $this->getAverageScore($score_arr) : array('Total' => 0);
            $score_class = (!empty($score_arr)) ? $this->getColorClassForRule($average_score_arr) : array('Total' => 'gray');

            $customer_result['score'] = $average_score_arr;
            $customer_result['color_class'] = $score_class;
            return $customer_result;
        } else {
            return false;
        }
    }

    /**
     *  To return score array
     *  @param  $customer
     *  @param  last_report_key
     *  @return  array
     */
    public function calculateScoreArr($customer, $last_report_key)
    {
        $score_arr = [];
        $current_payperiod_template = $customer->customerPayperiodTemplate[$last_report_key];
        foreach ($current_payperiod_template->customerReport as $each_answer) {
            $last_updated = $each_answer->updated_at;
            $has_avg = $this->getQuestionCategoryAvg($each_answer->templateFormWithTrashed->question_category_id)->average;
            $question_type = $this->getQuestionCategoryAvg($each_answer->templateFormWithTrashed->question_category_id)->description;
            if (strtolower($has_avg) == "yes" && $each_answer->score !== null) {
                $score_arr[$question_type] = ($score_arr[$question_type]) ?? array();
                array_push($score_arr[$question_type], $each_answer->score);
            }
        }
        return $score_arr;
    }
}
