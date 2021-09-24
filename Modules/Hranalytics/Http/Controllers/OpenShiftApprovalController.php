<?php

namespace Modules\Hranalytics\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Customer;
use Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts;
use Modules\Hranalytics\Repositories\OpenShiftApprovalRepository;
use Modules\Hranalytics\Repositories\ScheduleCustomerMultipleFillShiftsRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;

class OpenShiftApprovalController extends Controller
{
    /**
     * Repository instance.
     *
     * @var \App\Repositories\OpenShiftApprovalRepository

     *
     */
    protected $openShiftApprovalRepository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Repositories\OpenShiftApprovalRepository $openShiftApprovalRepository

     * @return void
     */
    public function __construct(CustomerRepository $customerRepository, CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository, OpenShiftApprovalRepository $openShiftApprovalRepository, ScheduleCustomerMultipleFillShiftsRepository $scheduleCustomerMultipleFillShiftsRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->repository = $openShiftApprovalRepository;
        $this->scheduleCustomerMultipleFillShiftsRepository = $scheduleCustomerMultipleFillShiftsRepository;
    }

    /**
     * Function to calculate array of available schedules
     *
     * @return array
     */
    public function index()
    {
        $customer_details_arr = $this->customerRepository->getProjectsDropdownList('all');
        return view('hranalytics::openshift.index',compact('customer_details_arr'));
    }

    /**
     *Get a listing of the Request Type Master for Datatable.
     *
     * @return Json
     */
    public function getList($checked = null, $client_id = null)
    {
        return datatables()->of($this->repository->getAll($checked, $client_id))->addIndexColumn()->toJson();
    }

    public function details($id)
    {
        return $this->repository->getOpenshifts($id);
    }
    public function deleteAlreadyApproved($requirement_id, $shift_id, $user_id)
    {

        // if ($shift_id == 0) {
        //      EventLogEntry::where('user_id', $user_id)->where('schedule_customer_requirement_id', $requirement_id)->delete();
        //  } else {
        //      $destroy_assigned = $this->scheduleCustomerMultipleFillShiftsRepository->deleteAllocated($shift_id);
        //  }
        return redirect()->route('candidate.eventLog', [$requirement_id, $shift_id, $user_id])->with('openshift_requirement', $requirement_id)->with('openshift_multifill', $shift_id)->with('openshift_user', $user_id);
    }

    /**
     * Showing client and employees in google map
     *
     * @param [type] $id
     * @return void
     */
    public function plotOpenShiftMap($id)
    {
        $employee_details = $this->repository->getRequirementDetails($id);
        $employee_list = data_get($employee_details, 'openshifts.*.user.employee');
        $openshift = data_get($employee_details, 'openshifts.*');
        $customer = data_get($employee_details, 'customer');
        $list_data = $this->repository->getemployeeArray($employee_list);
        return view('hranalytics::openshift.openshift-map', compact('employee_details', 'list_data', 'customer', 'openshift'));
    }

    public function sendMail($requirement_id, $shift_id, $user_id, $status)
    {
        if ($status == 1) {
            $employee_details = $this->repository->sendMail($requirement_id, $shift_id, $user_id);
        }
        return redirect()->route('openshift');
    }

    public function shiftAvailability(Request $request)
    {
        $multipleShiftId = $request->get('multiple_shift_id');
        $customerId = $request->get('customer_id');
        $userId = $request->get('user_id');
        $requirementId = $request->get('requirement_id');

        if (!empty($customerId)) {
            $customerObject = Customer::find($customerId);
            if ($customerObject->stc == 0) {
                return response()->json([
                    'success' => true,
                    'multiple_shift_id' => $multipleShiftId,
                    'customer_id' => $customerId,
                    'requirement_id' => $requirementId,
                ]);
            }
        }

        $alreadyAssigned = 0;
        if (!empty($multipleShiftId)) {
            $shiftArray = ScheduleCustomerMultipleFillShifts::where('parent_id', $multipleShiftId)->get()->pluck('id')->toArray();
            array_push($shiftArray, $multipleShiftId);
            $userShifts = ScheduleCustomerMultipleFillShifts::whereIn('id', $shiftArray)->where('assigned_employee_id', $userId)->first();
            if (empty($userShifts)) {
                $vacantChildMultipleFillShiftObject = ScheduleCustomerMultipleFillShifts::where('parent_id', $multipleShiftId)
                    ->where('no_of_position', '>', 1)
                    ->whereNull('assigned_employee_id')
                    ->first();
                if (!empty($vacantChildMultipleFillShiftObject)) {
                    return response()->json([
                        'success' => true,
                        'multiple_shift_id' => $vacantChildMultipleFillShiftObject->id,
                        'customer_id' => $vacantChildMultipleFillShiftObject->scheduleCustomerRequirement->customer->id,
                        'requirement_id' => $vacantChildMultipleFillShiftObject->scheduleCustomerRequirement->id,
                    ]);
                } else {
                    $multipleFillShiftObject = $this->scheduleCustomerMultipleFillShiftsRepository->get($multipleShiftId);
                    return response()->json([
                        'success' => true,
                        'multiple_shift_id' => $multipleFillShiftObject->id,
                        'customer_id' => $multipleFillShiftObject->scheduleCustomerRequirement->customer->id,
                        'requirement_id' => $multipleFillShiftObject->scheduleCustomerRequirement->id,
                    ]);
                }
            } else {
                $alreadyAssigned = $userShifts->id;
            }
        }
        return response()->json(['success' => false, 'alreadyAssigned' => $alreadyAssigned]);
    }
}
