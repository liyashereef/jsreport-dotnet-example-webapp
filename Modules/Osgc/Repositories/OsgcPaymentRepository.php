<?php

namespace Modules\Osgc\Repositories;

use \Stripe\Stripe;
use \Stripe\Checkout\Session;
use Modules\Osgc\Repositories\OsgcCourseLookupRepository;
use Modules\Osgc\Repositories\OsgcCourseRepository;
use Modules\Osgc\Models\CoursePayment; 
use Illuminate\Support\Facades\Log;
class OsgcPaymentRepository
{
   
    public function __construct(OsgcCourseLookupRepository $osgcCourseLookupRepository,CoursePayment $coursePayment,OsgcCourseRepository $osgcCourseRepository)
    {
      
        $this->osgcCourseLookupRepository = $osgcCourseLookupRepository;
        $this->osgcCourseRepository = $osgcCourseRepository;
        $this->coursePayment = $coursePayment;
        $this->stripe_secret_key = config('globals.stripe_secret_key');
        
    }

    public function doPayment($request)
    {
      $courseDetails=$this->osgcCourseLookupRepository->get($request->course_id);
      $price=$courseDetails->CoursePrice->price;
      $title=$courseDetails->title;
      \Stripe\Stripe::setApiKey($this->stripe_secret_key);
      $productArr=[
        'name' => $title,
       
        
      ];
      if(!empty($courseDetails->course_image))
        {
            $file='osgc/image/'.$courseDetails->course_image ?? '';
            $url=$this->osgcCourseRepository->getSignedUrl($file);
            $productArr['images'] = [$url];
        }
        
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
              'price_data' => [
                'currency' => 'cad',
                'product_data' =>$productArr,
                'unit_amount' => $price *100,
              ],
              'quantity' => 1,
              
            ]],
            'mode' => 'payment',
            'success_url' => url('osgc/paymentSuccess?session_id={CHECKOUT_SESSION_ID}'),//'http://127.0.0.1:8000/osgc/paymentSuccess?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('osgc.home'),
          ]);
         
         $updateArr=array('course_id'=>$request->course_id,'status'=>0,'started_time'=>\Carbon\Carbon::now(),'amount'=>$price,'transaction_id'=>$session->id,'user_id'=>\Auth::guard('osgcuser')->user()->id,'payment_intent'=>$session->payment_intent);
         $this->updatePaymentDetails($updateArr,['transaction_id'=>$session->id]);
         $this->paymentLog($session,"Session Details:");
         $updatePaymentIntent=$this->updatePaymentIntent($session->payment_intent);
          
         return $session;
    }

    public function updatePaymentDetails($updateArr,$condArr)
    {
        return $this->coursePayment->updateorCreate($condArr,$updateArr);
        
    }
    public function retrievePayment($sessionId)
    { 
        $session=$this->retrieveSession($sessionId);
        $this->paymentLog($session,"Session Details1:");//dd($session);
        $payment_intent=$session->payment_intent ?? '';
        $status='Failed';
        if($payment_intent)
        {
          $intent = $this->retrievePaymentIntent($payment_intent);
          $status=$intent->status ?? '';
        }
        // if($status == 'succeeded')
        // {
        //     $updateArr=array('status'=>1);
        //     $this->coursePayment->updateorCreate($updateArr,['transaction_id'=>$session->id]);
        // }
        //$status=$session->payment_status ?? '';
        if($status == 'succeeded')
        {
            $updateArr=array('status'=>1,'end_time'=>\Carbon\Carbon::now());
            $this->coursePayment->where('transaction_id','=',$sessionId)->update($updateArr);
            
        }else if($status == 'processing')
        {
           $updateArr=array('status'=>2);
           $this->coursePayment->where('transaction_id','=',$sessionId)->update($updateArr);
        }else{
            $updateArr=array('status'=>0,'end_time'=>\Carbon\Carbon::now());
            $this->coursePayment->where('transaction_id','=',$sessionId)->update($updateArr);
        }
        return $status;
    }

    public function paymentLog($response,$type){
      Log::channel('osgcPayment')
          ->info($type.json_encode(['date' => \Carbon\Carbon::now()->format('Y-m-d'),
          'time' => \Carbon\Carbon::now()->format('H:i:s'),
          // 'courseId'=>$courseId,
          'userId'=>\Auth::guard('osgcuser')->user()->id,
          'response'=>$response
          ])
      );
  }
  public function retrieveSession($sessionId)
  {

    \Stripe\Stripe::setApiKey($this->stripe_secret_key);
    $session = \Stripe\Checkout\Session::retrieve($sessionId);
    $this->paymentLog($session,"Session Details:");
    return  $session;

      
  }
  public function retrievePaymentIntent($paymentId)
  {

    \Stripe\Stripe::setApiKey($this->stripe_secret_key);
    $result = \Stripe\PaymentIntent::retrieve($paymentId);
    return  $result;

      
  }
  public function updatePaymentIntent($paymentId)
  {

    \Stripe\Stripe::setApiKey($this->stripe_secret_key);
    $result = \Stripe\PaymentIntent::update($paymentId,[
            'metadata' => ['type' => 'OSGC'],
          ]);
    $this->paymentLog($result,"Payment Intent Details:");
    return  $result;

      
  }
  public function getPendingPayment()
  {
    return $this->coursePayment->where('user_id',\Auth::guard('osgcuser')->user()->id)->where('status',2)->get();
  }
  public function getRefundPayment($paymentIntent)
  {
    $stripe = new \Stripe\StripeClient(
      $this->stripe_secret_key
    );
    $result=$stripe->refunds->all(['payment_intent' => $paymentIntent]);
    return $result;
  }

}
