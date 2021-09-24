<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use DB;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\SubjectRequest;
use Modules\Admin\Models\IncidentRecipient;
use Modules\Admin\Models\IncidentPriorityLookup;
use Modules\Admin\Http\Requests\IncidentRecipientRequest;

class IncidentReportRecipientController extends Controller
{

    /**
     * The Repository instance.
     *
     * @var \App\Services\HelperService
     * @var \Modules\Admin\Repositories\HolidayRepository;
     */
    protected $helperService;
 

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\HolidayRepository $holidayRepository;
     * @return void
     */
    public function __construct(
        HelperService $helperService
    ) {
        $this->helperService = $helperService;
    }

    /**
     * Store a newly created holiday in storage.
     *
     * @param  Modules\Admin\Http\Requests\HolidayRequest $request
     * @return Json
     */
    public function store(IncidentRecipientRequest $request)
    {
        try {
            DB::beginTransaction();
            $data_arr=array();
            $priority_arr=['High'=>'high','Medium'=>'medium','Low'=>'low'];
            IncidentRecipient::where('customer_id', $request->customer_id)->delete();
            foreach ($request->email as $key => $each_email) {
                foreach ($priority_arr as $priorityLabel => $priority) {
                    if ($request->get($priority)[$key]=='1') {
                        $templateFrom = IncidentRecipient::create(
                            [
                            'customer_id' => $request->customer_id,
                            'priority_id' => IncidentPriorityLookup::where('value', $priorityLabel)->first()->id,
                            'email' => $each_email,
                            'amendment_notification'=>$request->amendment[$key]??0,
                            ]
                        );
                    }
                }
            }
     
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Show the form for editing the specified holiday.
     *
     * @param  $id
     * @return Json
     */
    public function list($id)
    {
        $reciepint=IncidentRecipient::where('customer_id', $id)->get()->groupBy('email');
        $i=0;
        $recipient_arr=array();
        foreach ($reciepint as $email => $reciepintPriorities) {
            $recipient_arr[$i]['email']=$email;
            foreach ($reciepintPriorities as $key => $eachPriority) {
                $recipient_arr[$i][$eachPriority->priority->value]=1;
                $recipient_arr[$i]['amendment_notification']=$eachPriority->amendment_notification;
            }
            $i++;
        }
         return response()->json(['success' => true, 'data' => $recipient_arr]);
    }
}
