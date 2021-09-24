<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\PaymentMethod;
use Auth;
use DB;

class ContractPaymentMethodRepository
{
      
      public function showTotalList(){
         $billingcycle = PaymentMethod::orderBy('id','asc')->select('paymentmethod','id')->get()->toArray();
         return datatables()->of($billingcycle)->addIndexColumn()->toJson();
      }

      public function getAll(){
        $billingcycle = PaymentMethod::orderBy('paymentmethod','asc')->select('paymentmethod','id')->get();
        return $billingcycle;
     }

      public function getLastsequence(){
          $lastsequencearray = ContractSubmissionReason::orderBy('sequence','desc')->select('sequence')->take('1')->first();
          
          if(empty($lastsequencearray)){
            $lastsequence = 1;
          }
          else
          {
            $lastsequence = $lastsequencearray->sequence +1;
          }   
          return $lastsequence;
      }

      public function save($id,$title){
        $userid = Auth::user()->id;
         $paymentmethodarray = ["paymentmethod"=>$title,"status"=>1,"createdby"=>$userid];
         if(PaymentMethod::updateOrCreate(["id"=>$id], $paymentmethodarray))
          {
            if($id<1)
            {
              return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Contracts Billing frequency has been succesfully added.</div>'));
            }
            else {
              return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Contracts Billing frequency has been succesfully updated.</div>'));
            }
            
          }
          else
          {
            return response()->json($message, '422');
          }
      }

      public function saveSubmissionReason($reasonforsubmission,$submissionid = null,$previoussequence = 0)
      {
          $userid = Auth::user()->id;
          $lastsequence = 0;
          if($previoussequence < 1)
          {
             $lastsequence = $this->getLastsequence();
          }
          
          if($lastsequence<1)
          {
              $lastsequence = 1;
          }

          if($previoussequence>0)
          {
              $lastsequence = $previoussequence;
          }
          $reasonarray = ["reason"=>$reasonforsubmission,"sequence"=>$lastsequence,"status"=>1,"createdby"=>$userid];
          $reasonofsubmission = ContractSubmissionReason::updateOrCreate(["id"=>$submissionid], ["reason"=>$reasonforsubmission,'sequence' => $lastsequence,'status'=>1,"createdby"=>$userid]);

          if($reasonofsubmission->save())
          {
            return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Contracts reason data has been succesfully added.</div>'));
          }
          else
          {
            return response()->json($message, '422');
          }
          
          
  
      }

      public function updateSubmissionReason($reasonforsubmission,$submissionid = null,$previoussequence = 0)
      {
          $userid = Auth::user()->id;
          //$reasonarray = ["reason"=>$reasonforsubmission,"sequence"=>$lastsequence,"status"=>1,"createdby"=>$userid];
          //$reasonofsubmission = ContractSubmissionReason::updateOrCreate(["id"=>$submissionid], ["reason"=>$reasonforsubmission,'sequence' => $lastsequence,'status'=>1,"createdby"=>$userid]);
          $reasonofsubmission = ContractSubmissionReason::find($submissionid);
          $reasonofsubmission->reason = $reasonforsubmission;
          if($reasonofsubmission->save())
          {
            return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Contracts reason data has been Updated successfully.</div>'));
          }
          else
          {
            return response()->json($message, '422');
          }
          
          
  
      }

      public function deletePaymentMethod($paymentmethodid)
      {
        $paymentmethodmodel = PaymentMethod::find($paymentmethodid);
        if($paymentmethodmodel->delete())
        {
          
          return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Payment Method has been deleted successfully.</div>'));
        }
        else
        {
          return response()->json($message, '422');
        }
      }
}