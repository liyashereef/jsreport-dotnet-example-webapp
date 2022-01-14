<?php

namespace Modules\Employeescheduling\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Employeescheduling\Repositories\InheritScheduleRepository;

class InheritScheduleController extends Controller
{

    protected $payPeriodRepository, $inheritScheduleRepository, $helperService;

    public function __construct(PayPeriodRepository $payPeriodRepository, InheritScheduleRepository $inheritScheduleRepository, HelperService $helperService)
    {
        $this->payPeriodRepository = $payPeriodRepository;
        $this->inheritScheduleRepository = $inheritScheduleRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!(\Auth::user()->can('employee_schedule_inherit'))) {
            return redirect()->to('/');
        }
        return view('employeescheduling::inherit-schedule');
    }

    public function customers()
    {
        $result = [];
        $customers = $this->inheritScheduleRepository->getCustomerList();
        if (!empty($customers)) {
            $result = collect($customers)->pluck('client_name', 'id');
        }
        return response()->json(['success' => true, 'data' => $result]);
    }

    public function fetchSourcePayPeriods(Request $request)
    {
        return response()->json(['success' => true, 'data' => $this->inheritScheduleRepository->fetchSourcePayperiods($request->get('customer_id'))]);
    }

    public function fetchDestinationPayPeriods(Request $request)
    {
        return response()->json(['success' => true, 'data' => $this->payPeriodRepository->getAllActivePayPeriodsabovedate()]);
    }

    public function process(Request $request)
    {
        try {
            \DB::beginTransaction();
            $status = $this->inheritScheduleRepository->approvedPayperiodExist($request);
            if ($status) {
                return response()->json(['success' => false, 'msg' => "Operation restricted, Approved schedules found with selected destination payperiods"]);
            }
            $customerId = $request->get('customer_id');
            $sourcePayPeriod = $request->get('source_payperiod');
            $destinationPayPeriod = $request->get('destination_payperiod');
            $this->inheritScheduleRepository->inheritProcess($customerId,$sourcePayPeriod,$destinationPayPeriod);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
}
