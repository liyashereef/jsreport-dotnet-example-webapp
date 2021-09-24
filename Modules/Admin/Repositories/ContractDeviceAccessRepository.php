<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\DeviceAccess;
use Auth;
use DB;

class ContractDeviceAccessRepository
{
      
      public function showTotalList(){
         $deviceaccess = DeviceAccess::orderBy('id','asc')->select('DeviceType','id')->get()->toArray();
         return datatables()->of($deviceaccess)->addIndexColumn()->toJson();
      }

      public function getAll(){
        $deviceaccess = DeviceAccess::orderBy('DeviceType','asc')->select('DeviceType','id')->get();
        return $deviceaccess;
     }

      
      public function save($id,$title){
        $userid = Auth::user()->id;
         $devicetypearray = ["DeviceType"=>$title,"status"=>1,"createdby"=>$userid];
         if(DeviceAccess::updateOrCreate(["id"=>$id], $devicetypearray))
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

      

      

      public function deleteDeviceaccess($deviceid)
      {
        $deviceaccessmodel = DeviceAccess::find($deviceid);
        if($deviceaccessmodel->delete())
        {
          
          return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Device access has been deleted successfully.</div>'));
        }
        else
        {
          return response()->json($message, '422');
        }
      }
}