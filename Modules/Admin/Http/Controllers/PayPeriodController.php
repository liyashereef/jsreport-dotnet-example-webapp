<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\PayPeriodRequest;
use Modules\Admin\Models\PayPeriod;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Employeescheduling\Repositories\SchedulingRepository;

class PayPeriodController extends Controller
{

    /**
     * The Repository instance.
     *
     * @var \App\Repositories\PayPeriodRepository
     */
    protected $payPeriodRepository, $schedulingRepository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\PayPeriodRepository $PayPeriodRepository
     * @return void
     */
    public function __construct(PayPeriodRepository $payPeriodRepository, SchedulingRepository $schedulingRepository)
    {
        $this->payPeriodRepository = $payPeriodRepository;
        $this->schedulingRepository = $schedulingRepository;
    }

    /**
     * Display a listing of the Payperiods.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::masters.payperiod');
    }

    /*
     * Show the form with Payperiods.
     *
     * @return \Illuminate\Http\Response
     */

    public function getPayPeriods()
    {
        return datatables()->of(PayPeriod::whereActive(true)->get())->addIndexColumn()->toJson();
    }

    /**
     * Store a newly created Payperiods in storage.
     *
     * @param  \Illuminate\Http\PayperiodCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PayPeriodRequest $request)
    {
        $overlapping_start_count = 0;
        $overlapping_end_count = 0;
        $start_date = date('Y-m-d', strtotime($request->get('start_date')));
        $end_date = date('Y-m-d', strtotime($request->get('end_date')));
        $week_one_end_date = date('Y-m-d', strtotime($request->get('week_one_end_date')));
        $week_two_start_date = date('Y-m-d', strtotime($request->get('week_two_start_date')));

        $overlapping_records_count = PayPeriod::where([['end_date', '>=', $start_date], ['start_date', '<=', $end_date]])
            ->whereActive(true)
            ->count();
        if ($overlapping_records_count == 0) {
            $data = new PayPeriod([
                'year' => $request->get('year'),
                'pay_period_name' => $request->get('pay_period_name'),
                'short_name' => $request->get('short_name'),
                'start_date' => $start_date,
                'week_one_end_date' => $week_one_end_date,
                'week_two_start_date' => $week_two_start_date,
                'end_date' => $end_date,
            ]);
            $data->save();
            return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Pay Period data has been succesfully added.</div>'));
        } else {
            $overlapping_start_count = PayPeriod::where([['end_date', '>=', $start_date], ['start_date', '<=', $start_date]])
                ->whereActive(true)
                ->count();
            $overlapping_end_count = PayPeriod::where([['start_date', '<=', $end_date], ['end_date', '>=', $end_date]])
                ->whereActive(true)
                ->count();
            $message['message'] = 'The given data was invalid.';
            if ($overlapping_start_count > 0) {
                $message['errors']['start_date'][] = 'Employee already have shift between the same time slot.';
            }
            if ($overlapping_end_count > 0) {
                $message['errors']['end_date'][] = 'Employee already have shift between the same time slot.';
            }
            if ($overlapping_start_count == 0 && $overlapping_end_count == 0) {
                $message['errors']['start_date'][] = 'Employee already have shift between the same time slot.';
                $message['errors']['end_date'][] = 'Employee already have shift between the same time slot.';
            }
            return response()->json($message, '422');
        }
    }

    /**
     * Show the form for editing the specified Payperiod.
     *
     * @param  \App\Models\PayPeriod  $payPeriod
     * @return \Illuminate\Http\Response
     */
    public function getPayPeriod(Request $request)
    {
        return response()->json(PayPeriod::find($request->get('id')));
    }

    public function payperiodUpdate(PayPeriodRequest $request)
    {
        $id = $request->get('id');
        $scheduleTimeLogDetails = $this->schedulingRepository->getScheduleTimeLogByScheduleId('', [], [$id]);
        if(count($scheduleTimeLogDetails) > 0) {
            return response()->json(array('success' => false, 'message' => 'Operation restricted, Client schedules found for the selected payperiod'));
        }
        
        $overlapping_start_count = 0;
        $overlapping_end_count = 0;
        $update = PayPeriod::find($id);
        $update->year = $request->get('year');
        $update->pay_period_name = $request->get('pay_period_name');
        $update->short_name = $request->get('short_name');
        $update->start_date = date('Y-m-d', strtotime($request->get('start_date')));
        $update->week_one_end_date = date('Y-m-d', strtotime($request->get('week_one_end_date')));
        $update->week_two_start_date = date('Y-m-d', strtotime($request->get('week_two_start_date')));
        $update->end_date = date('Y-m-d', strtotime($request->get('end_date')));
        $start_date = date('Y-m-d', strtotime($request->get('start_date')));
        $end_date = date('Y-m-d', strtotime($request->get('end_date')));

        $overlapping_records_count = PayPeriod::where([['end_date', '>=', $start_date], ['start_date', '<=', $end_date], ['id', '<>', $id]])
            ->whereActive(true)
            ->count();
        if ($overlapping_records_count == 0) {
            $update->save();
            return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> PayPeriod data has been succesfully updated.</div>'));
        } else {
            $overlapping_start_count = PayPeriod::where([['end_date', '>=', $start_date], ['start_date', '<=', $start_date], ['id', '<>', $id]])
                ->whereActive(true)
                ->count();
            $overlapping_end_count = PayPeriod::where([['start_date', '<=', $end_date], ['end_date', '>=', $end_date], ['id', '<>', $id]])
                ->whereActive(true)
                ->count();
            $message['message'] = 'The given data was invalid.';
            if ($overlapping_start_count > 0) {
                $message['errors']['start_date'][] = 'Employee already have shift between the same time slot.';
            }
            if ($overlapping_end_count > 0) {
                $message['errors']['end_date'][] = 'Employee already have shift between the same time slot.';
            }
            if ($overlapping_start_count == 0 && $overlapping_end_count == 0) {
                $message['errors']['start_date'][] = 'Employee already have shift between the same time slot.';
                $message['errors']['end_date'][] = 'Employee already have shift between the same time slot.';
            }
            return response()->json($message, '422');
        }
    }
    /**

     * Remove the specified Payperiod from storage.
     *
     * @param  \App\Models\PayPeriod  $payPeriod
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $scheduleTimeLogDetails = $this->schedulingRepository->getScheduleTimeLogByScheduleId('', [], [$id]);
        if(count($scheduleTimeLogDetails) > 0) {
            return response()->json(array('success' => false, 'message' => 'Operation restricted, Client schedules found for the selected payperiod'));
        }
        PayPeriod::where('id', $id)->update(['active' => 0]);
        PayPeriod::find($id)->delete();
        return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> PayPeriod data have been succesfully removed.</div>'));
    }

    /**
     * Function to getCurrentPayperiod
     * @return type
     */
    public function getCurrentPayPeriod()
    {
        $currentPayPeriod = $this->payPeriodRepository->getCurrentPayperiod();
        if ($currentPayPeriod != null) {
            return $currentPayPeriod->id;
        } else {
            return null;
        }

    }

    /**
     * Function to get the pastCurrentFuturePayPeriod lookups
     * @param  string  $time_period [Past or Future or Both]
     * @param  integer $no_of_years [Future and Past Years]
     * @return array
     */
    public function pastCurrentFuturePayPeriod($time_period = null, $no_of_years = 1)
    {
        return $this->payPeriodRepository->getPastCurrentFuturePayPeriod($time_period, $no_of_years);
    }

}
