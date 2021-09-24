<?php

namespace Modules\Timetracker\Http\Controllers\API\v1;

use App\Repositories\MailQueueRepository;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\DepartmentMappingRequest;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\DepartmentMaster;
use Modules\Admin\Models\DepartmentEmployees;
use Modules\Admin\Repositories\DepartmentMasterRepository;
use Modules\Hranalytics\Models\EmployeeFeedback;
use Modules\Admin\Models\WhistleblowerStatusLookup;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Illuminate\Support\Facades\Log;

class EmployeeFeedbackController extends Controller
{
    protected  $mailQueueRepository, $repository;
    public function __construct(
        DepartmentMasterRepository $repository,
        MailQueueRepository $mailQueueRepository
    ) {
        $this->repository = $repository;
        $this->MailQueueRepository = $mailQueueRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */


    public function getDepartments(Request $request)
    {
        return response()->json($this->repository->getDepartments());
    }

    public function submitEmployeeFeedback(Request $request)
    {
        $initialStatus = null;
        $applicationStatus = WhistleblowerStatusLookup::where("inital_status", 1)->first();
        if ($applicationStatus) {
            $initialStatus = $applicationStatus->id;
        }
        $customerId = $request->customer;
        $customerDetails = Customer::find($customerId);

        $subject = $request->subject;
        $message = $request->message;
        $department = $request->addressto;
        $allocatedEmailIds = [];
        if ($department) {
            $departmentDetail = DepartmentMaster::find($department);
            $departmentMapping = DepartmentEmployees::where("department_master_id", $department)->get();
            $areaManagerRequired = $departmentDetail->allocated_regionalmanager;
            $supervisorRequired = $departmentDetail->allocated_supervisor;
            if ($areaManagerRequired == 1) {
                $areaManagers = CustomerEmployeeAllocation::with('user')
                    ->where("customer_id", $customerId)->whereHas("areaManager")->get();
                foreach ($areaManagers as $areaManager) {
                    if (isset($areaManager->user)) {
                        if ($areaManager->user->email != null) {
                            $allocatedEmailIds[] = $areaManager->user->email;
                        }
                    }
                    // $allocatedEmailIds[] = $areaManager->user->alternate_email;
                }
            }
            if ($supervisorRequired == 1) {
                $siteSupervisors = CustomerEmployeeAllocation::with('user')
                    ->where("customer_id", $customerId)->whereHas("supervisor")->get();
                foreach ($siteSupervisors as $siteSupervisor) {
                    if (isset($siteSupervisor->user)) {
                        if ($siteSupervisor->user->email != null) {
                            $allocatedEmailIds[] = $siteSupervisor->user->email;
                        }
                    }

                    // $allocatedEmailIds[] = $siteSupervisor->user->alternate_email;
                }
            }

            foreach ($departmentMapping as $userArray) {
                if (isset($userArray->user)) {
                    if ($userArray->user->email != null) {
                        $allocatedEmailIds[] = $userArray->user->email;
                    }
                }
                // $allocatedEmailIds[] = $userArray->user->alternate_email;
            }
        }
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $ratingId = $request->ratingId;
        $logData = EmployeeFeedback::create([
            "customer_id" => $customerId,
            "subject" => $subject,
            "message" => $message,
            "department_id" => $department,
            "rating_id" => $ratingId,
            "status" => $initialStatus,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "created_by" => \Auth::user()->id
        ]);

        if ($logData) {
            if (count($allocatedEmailIds) > 0) {
                $helper_variable = [
                    "{project}" => $customerDetails->project_number . "-" . $customerDetails->client_name,
                    "{projectNumber}" => $customerDetails->project_number,
                    "{client}" => $customerDetails->client_name,
                    "{receiverFullName}" => ""
                ];
                foreach ($allocatedEmailIds as $key => $allocatedEmailId) {
                    try {
                        $this->MailQueueRepository
                            ->prepareMailTemplate(
                                "employee_feedback_notification_email",
                                $customerId,
                                $helper_variable,
                                'Modules\Hranalytics\Models\EmployeeFeedback',
                                0,
                                0,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                $allocatedEmailId
                            );
                    } catch (\Exception $e) {
                        Log::channel('Employee Feedback')->info("Error: " . $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile());
                    }
                }
            }
            $return["code"] = 200;
            $return["success"] = true;
            $return["message"] = "Success";
            return response()->json(
                $return
            );
        } else {
            $return["code"] = 401;
            $return["success"] = false;
            $return["message"] = "Warning";
            return response()->json(
                $return
            );
        }
    }
}
