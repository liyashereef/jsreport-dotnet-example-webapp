<?php

namespace Modules\IdsScheduling\Http\Controllers;
use Modules\IdsScheduling\Repositories\IdsPaymentRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\HelperService;
use Modules\IdsScheduling\Http\Controllers\IdsSlotBookingController;
class IdsPaymentController extends Controller
{


    private $helperService;
    private $idsSlotBookingController;

    public function __construct(
        HelperService $helperService,
        IdsPaymentRepository $idsPaymentRepository,
        IdsSlotBookingController $idsSlotBookingController
    ) {
        $this->idsPaymentRepository = $idsPaymentRepository;
        $this->helperService = $helperService;
        $this->idsSlotBookingController = $idsSlotBookingController;
    }

     /**
     * Initializing payment
     * @param NULL
     */

    public function index(Request $request)
    {
      try {

          $result=$this->idsPaymentRepository->doPayment($request);
          return response()->json(['id' => $result->id]);

      }catch (\Exception $e) {
          return response()->json($this->helperService->returnFalseResponse());
      }
    }

        /**
     * Payment success page
     * @param sessionid
     */

    public function bookingPaymentSuccess(Request $request)
    {
      try{
          $sessionId=$request->get('session_id');
          $result=$this->idsPaymentRepository->retrievePayment($sessionId);
          if($result){
            $status=$result['status'] ?? '';
            if($result['entry_id'] && $status =='succeeded' && $result['refreshMultipleTime'] == false){
              $this->idsSlotBookingController->bookingScheduleMail($result['entry_id']);
            }

            return view('idsscheduling::public.payment-status',compact('status'));
          }


      }catch (\Exception $e) {
        return response()->json($this->helperService->returnFalseResponse($e));
      }

    }

}
