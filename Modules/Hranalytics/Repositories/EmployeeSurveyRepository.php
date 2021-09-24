<?php

namespace Modules\Hranalytics\Repositories;

use App\Services\HelperService;
use Auth;
use Illuminate\Support\Arr;
use Modules\Admin\Http\Requests\Request;
use Modules\Hranalytics\Models\EmployeeSurveyTemplate;
use Modules\Hranalytics\Models\EmployeeSurveyQuestion;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\Hranalytics\Models\EmployeeSurveyEntry;
use Modules\Hranalytics\Models\EmployeeSurveyAnswer;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;

class EmployeeSurveyRepository
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */

    public function __construct(
        UserRepository $userRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        HelperService $helperService
    ) {

        $this->helperService = $helperService;
        $this->userRepository = $userRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }

    public function getEmployeeSurveyList($customers = null)
    {
        $clientAllocation = $this->customerEmployeeAllocationRepository->getAllocatedCustomerId([\Auth::user()->id]);
        if (\Auth::user()->hasAnyPermission(["super_admin", "view_all_employee_surveys"])) {
            $surveys = EmployeeSurveyTemplate::with('employeesurveycustomerallocation')->when(!empty($customers), function ($query) use ($customers) {
                return $query->whereIn("customer_id", $customers);
            })->orderBy('survey_name')->get();
        } elseif (\Auth::user()->hasAnyPermission(["view_allocated_employee_surveys"])) {
            $surveys = EmployeeSurveyTemplate::with('employeesurveycustomerallocation')->when(!empty($customers), function ($query) use ($clientAllocation) {
                return $query->whereIn("customer_id", $clientAllocation);
            })->orderBy('survey_name')->get();
        }

        return $surveys;
    }
}
