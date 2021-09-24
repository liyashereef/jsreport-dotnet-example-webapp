<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\OfficeAddress;
use Auth;
use DB;

class OfficeAddressRepository
{
      public function getSingleofficeaddress($id){
        return OfficeAddress::find($id)->first();
      }
      public function showTotalList(){
         $OfficeAddress = OfficeAddress::orderBy('id','asc')->select('id','addresstitle','address')->get()->toArray();
         return datatables()->of($OfficeAddress)->addIndexColumn()->toJson();
      }

      public function getLookupList(){
        $OfficeAddress = OfficeAddress::orderBy('addresstitle','asc')->select('id','addresstitle','address')->get();
        return $OfficeAddress;
      }

      
      public function save($id,$title,$address){
        $userid = Auth::user()->id;
         $officeaddressarray = ["addresstitle"=>$title,"address"=>$address,"status"=>1,"createdby"=>$userid];
         if(OfficeAddress::updateOrCreate(["id"=>$id], $officeaddressarray))
          {
            if($id<1)
            {
              return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Office address has been succesfully added.</div>'));
            }
            else {
              return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Office address has been succesfully updated.</div>'));
            }
            
          }
          else
          {
            return response()->json($message, '422');
          }
      }

      

      

      public function deleteOfficeaddress($addressid)
      {
        $officeaddressmodel = OfficeAddress::find($addressid);
        if($officeaddressmodel->delete())
        {
          
          return response()->json(array('success' => true, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Office address has been deleted successfully.</div>'));
        }
        else
        {
          return response()->json($message, '422');
        }
      }
}