<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\ContractBillingCycle;
use Auth;
use DB;

class ContractBillingCycleRepository
{
      
      public function showTotalList(){
         $billingcycle = ContractBillingCycle::orderBy('id','asc')->select('title','id')->get()->toArray();
         return datatables()->of($billingcycle)->addIndexColumn()->toJson();
      }

      public function getAll()
      {
        $billingcycle = ContractBillingCycle::orderBy('title','asc')->select('title','id')->get();
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
         $billingcyclearray = ["title"=>$title,"status"=>1,"createdby"=>$userid];
         if(ContractBillingCycle::updateOrCreate(["id"=>$id], $billingcyclearray))
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

      public function deleteRatechange($billingcycleid)
      {
        $billingcyclemodel = ContractBillingCycle::find($billingcycleid);
        if($billingcyclemodel->delete())
        {
          
          return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Contracts Billing frequency has been deleted successfully.</div>'));
        }
        else
        {
          return response()->json($message, '422');
        }
      }
}