<?php

namespace Modules\Osgc\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Osgc\Repositories\OsgcCourseRepository;
use Modules\Osgc\Repositories\OsgcPaymentRepository;
use App\Services\HelperService;
use Illuminate\Support\Str;
class OsgcController extends Controller
{
  protected $helperService;
    public function __construct(OsgcCourseRepository $osgcCourseRepository,OsgcPaymentRepository $osgcPaymentRepository,HelperService $helperService){
        $this->osgcCourseRepository = $osgcCourseRepository;
        $this->osgcPaymentRepository = $osgcPaymentRepository;
        $this->helperService = $helperService;
      
    }
    /**
     * Display a listing of the resource.
     * @return Response
     *  Laravel 8 update
     *  Change str_limit to Str::limit() 
     */
    public function index()
    {
      // checking any pending payments, and update successful payments details to table
        $pendingPayments=$this->osgcPaymentRepository->getPendingPayment();
        if(!empty($pendingPayments))
        {
          foreach($pendingPayments as $payment)
          {
            $paymentIntent=$payment->payment_intent;
            $paymentDetails=$this->osgcPaymentRepository->retrievePaymentIntent($paymentIntent);
            $refundDetails=$this->osgcPaymentRepository->getRefundPayment($paymentIntent);
            if($paymentDetails->status =='succeeded' && count($refundDetails) ==0)
            {
              $updateArr=array('status'=>1,'end_time'=>\Carbon\Carbon::now());
              $this->osgcPaymentRepository->updatePaymentDetails($updateArr,['payment_intent'=>$paymentIntent]);
            } 
          }
        }
        
      // checking any pending payments, and update successful payments details to table
        $url=$flag=$finalStr='';
        $result=$this->osgcCourseRepository->getActiveAndOwnCourse();//dd($result);
        if(!empty($result)){
        foreach($result as $row)
        {
            $readmore = '... <a href="#" onclick="readMore('.$row->id.')">Read More</a>';
            $dataStr='';
            $dataStr='<div class="wide-block jumbotron">';
            $dataStr.='<div class="container-fluid mb-0 ">';
            $dataStr.='<div class="input-group row">';
            $dataStr.='<div class="col-md-3" align="left" style="margin-top: 8px;"><div class="imgDiv">';
            if(!empty($row->course_image))
            {
                $file='osgc/image/'.$row->course_image ?? '';
                $url=$this->osgcCourseRepository->getSignedUrl($file);
                $dataStr.='<img  src="'.$url.'" alt="" class="w-100 banner-intro">';
            }else{
                $dataStr.='<img  src="'.asset('images/courses_noimage.png').'" alt="" class="w-100 banner-intro">';
            }
            $dataStr.='</div></div>';
            $dataStr.='<div class="col-md-7 content-area">';
            $dataStr.='<p class="course-title">'.$row->title.'</p>';
            $dataStr.='<div id="readMoreDiv'.$row->id.'"><p class="color-light readmore">'.Str::limit($row->description, 350, $readmore).'</p></div>';
            $dataStr.='</div>';
            $dataStr.='<div class="col-md-2">';
            $dataStr.='<div align="center" class="price-div align-items-center justify-content-center">';
            

            if(!empty($row->CoursePayment))
            {
              $dataStr.='<a class="btn viewCourse" href="'.route('osgc.course', ['course_id' => $row->id]).'">Go to course</a>';
              $flag=1;
              
              
            }else{
              $dataStr.='<p class="price-area">$'.$row->CoursePrice->price.'</p>';  
              $dataStr.='<button id="checkout-button" class="btn" data-id="'.$row->id.'">Buy now</button>';
            }
            $dataStr.='</div>';
            $dataStr.='</div>';
            $dataStr.='</div>';
            $dataStr.='</div>';
            $dataStr.='</div>';
            
            $finalStr.=$dataStr;

        }
      }else{
        $finalStr='<div class="col-md-12" align="center">No Active Course Found</div>';
      }
                  
      
        return view('osgc::home',compact('finalStr','url','flag'));
    }
    
    /**
     * show guarding training page
     * @return Response
     */
    public function guardTraining()
    {
      return view('osgc::guard-training');
    }

   
    public function stripePost(Request $request)
    {
      try {
        $result=$this->osgcPaymentRepository->doPayment($request);
        return response()->json(['id' => $result->id]);
      }catch (\Exception $e) {
        return response()->json($this->helperService->returnFalseResponse());
      }
    }

    /**
     * Payment success page
     * @return Response
     */
    public function coursePaymentSuccess(Request $request)
    {
      try{
          $sessionId=$request->get('session_id');
          $status=$this->osgcPaymentRepository->retrievePayment($sessionId);//dd($status);
          return view('osgc::course.course-payment',compact('status'));
          
      }catch (\Exception $e) {
        return response()->json($this->helperService->returnFalseResponse());
      }
        
    }


    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function showCourse(Request $request)
    {
        $courseDetails=$this->osgcCourseRepository->getCourseDetails($request->course_id);
        return $courseDetails;
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
