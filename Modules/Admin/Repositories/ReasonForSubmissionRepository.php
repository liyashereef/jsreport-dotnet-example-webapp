<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\ContractSubmissionReason;
use Auth;
use DB;


class ReasonForSubmissionRepository
{

      public function getSinglereasonforsubmission($id){
        return ContractSubmissionReason::find($id)->withTrashed()->first();
      }
      
      public function getReasonList(){
         $submissionreasons = ContractSubmissionReason::orderBy('sequence','asc')->select('reason','id','sequence')->get()->toArray();
         return datatables()->of($submissionreasons)->addIndexColumn()->toJson();
      }

      public function getReasonListLookup(){
        return $submissionreasons = ContractSubmissionReason::orderBy('reason','asc')->select('reason','id','sequence')->get();
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

      public function deleteSubmissionReason($reasonid,$current_sequence)
      {
        $reasonforsubmissionmodel = ContractSubmissionReason::find($reasonid);
        if($reasonforsubmissionmodel->delete())
        {
          $updatesequenceabovemodel = ContractSubmissionReason::where('sequence','>',$current_sequence)->update([
            'sequence'=> DB::raw('sequence-1')
            ]);
         // $updatesequenceabovemodel->save();
          return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Contracts reason data has been deleted successfully.</div>'));
        }
        else
        {
          return response()->json($message, '422');
        }
      }


      
}