<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\ContractCellPhoneProvider;
use Auth;
use DB;

class ContractCellphoneProviderRepository
{
      
      public function showTotalList(){
         $contractcellphoneprovider = ContractCellPhoneProvider::orderBy('id','asc')->select('providername','id')->get()->toArray();
         return datatables()->of($contractcellphoneprovider)->addIndexColumn()->toJson();
      }

      public function getAll(){
        $contractcellphoneprovider = ContractCellPhoneProvider::orderBy('providername','asc')->select('providername','id')->get();
        return $contractcellphoneprovider;
     }
      
      public function save($id,$title){
        $userid = Auth::user()->id;
         $devicetypearray = ["providername"=>$title,"status"=>1,"createdby"=>$userid];
         if(ContractCellPhoneProvider::updateOrCreate(["id"=>$id], $devicetypearray))
          {
            if($id<1)
            {
              return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Device access has been succesfully added.</div>'));
            }
            else {
              return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Device access frequency has been succesfully updated.</div>'));
            }
            
          }
          else
          {
            return response()->json($message, '422');
          }
      }

      

      

      public function deleteContractCellPhoneProvider($deviceid)
      {
        $cellphoneprovidertitlemodel = ContractCellPhoneProvider::find($deviceid);
        if($cellphoneprovidertitlemodel->delete())
        {
          
          return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Cellphone provider has been deleted successfully.</div>'));
        }
        else
        {
          return response()->json($message, '422');
        }
      }
}