<?php

namespace Modules\Facility\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Facility\Models\Facility;
use Modules\Facility\Models\FacilityService;
use Modules\Facility\Models\FacilityUser;
use Modules\Facility\Models\FacilityBooking;
use Modules\Facility\Models\FacilityServiceUserAllocation;
use Modules\Facility\Models\FacilityUserWeekendDefinition;
use Modules\Facility\Models\FacilityUserPrerequisiteAnswer;
use Modules\Facility\Models\FacilityPrerequisite;
use Modules\Facility\Repositories\FacilityRepository;
use Modules\Facility\Http\Requests\AddUser;
use Modules\Facility\Http\Requests\EditUser;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Auth;


class FacilityUserController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    protected $facilityrepository,$helperService;

    public function __construct(FacilityRepository $facilityrepository,
    CustomerRepository $customerrepository,CustomerEmployeeAllocationRepository $customeremployeeallocationrepository){
        $this->facilityrepository = $facilityrepository;
        $this->customerrepository = $customerrepository;
        $this->customeremployeeallocationrepository = $customeremployeeallocationrepository;
    }

    public function viewFacilityusers()
    {

        $customerarray =[];
        $users = collect([]);
        $customersvalue = [];
        if(\Auth::user()->hasAnypermission('super_admin','manage_all_facility_users')){
            $customers = $this->customerrepository->getCustomerList("ALL_CUSTOMER");

            foreach ($customers as $value) {
                if($value->facility_booking==1){
                                $customerarray[$value->id] = $value->client_name." (".$value->project_number.")";
                }
            }
            $users = FacilityUser::all();
        }else if(\Auth::user()->hasAnypermission('manage_allocated_facility_users')){
            $customers = $this->customeremployeeallocationrepository->getUserallocatedcustomers();
            foreach ($customers as $key => $value) {
                if($value->customer->facility_booking==1){
                $customerarray[$value->customer->id] = $value->customer->client_name."( ".$value->customer->project_number.")";
                $customerid = $value->customer->id;

                array_push($customersvalue,$value->customer->id);
                }
            }
            $users = FacilityUser::whereIn("customer_id",$customersvalue)->get();
        }

        return view('facility::users',compact('users','customerarray'));
    }

    public function getCustomersfacility(Request $request){
        $customerid = $request->customerid;
        $chosenfacility = $request->facility;
        $chosenservice = $request->service;
        $facilityservice = null;
        $facilityorservice = 0;
        $singleservice = "";
        if($chosenfacility>0){
            $facilityservice = FacilityService::select('id','service')->where("facility_id",$chosenfacility)->orderBy('service','asc')->get();
        }
        $users = FacilityUser::
        with(["FacilityServiceUserAllocation"=>function($q)use($chosenfacility)
        {
            return $q->with(['facilityuserweekenddefinition',
            'facilityuserprerequisiteanswer'])->
            addSelect("*")->where(["model_type"=>"Modules\\Facility\\Models\\Facility","model_id"=>$chosenfacility]);
        }])
        ->with(["ServiceFacilityUserAllocation"=>function($q)use($chosenservice)
        {
            return $q->
            addSelect("*")->where(["model_type"=>"Modules\\Facility\\Models\\FacilityService","model_id"=>$chosenservice]);
        }])
        ->when($chosenservice>0,function($q)use($chosenservice){

        })
        ->where("customer_id",$customerid)->get();
        $service = collect([]);
        if($chosenservice>0){
            $service = FacilityService::find($chosenservice);
        }
        $item = collect([]);
        if($chosenfacility>0){
            $item = Facility::find($chosenfacility);
            if($item->single_service_facility==0){
                $facilityorservice=2;
            }else if($item->single_service_facility==1){
                $facilityorservice=1;
            }
        }

        $facility = Facility::select('id','facility','single_service_facility')->where("customer_id",$customerid)->orderBy('facility','asc')->get();
       // return view("facility::partials.allocatedusers",compact(('users')));
       $facilitydetails = Facility::find($chosenfacility);
       return response()->json(array('facility'=>$facility,'facilityservice'=>$facilityservice,'chosenfacility'=>$request->facility,'chosenservice'=>$request->service,
       'body' => \View::make('facility::partials.allocatedusers')->with(compact('users','facilitydetails','item','service','chosenfacility','facilityorservice'))
       ->render()));

    }

    public function getUserprerequisites(Request $request){
        $facilityid = $request->facilityid;
        $allocationid = $request->allocationid;
        $user_id = $request->user_id;

        $facprereq = FacilityPrerequisite::with(['FacilityUserPrerequisiteAnswer'=>function($q)use($facilityid,$user_id,$allocationid){
            return $q->where(["facility_allocation_id"=>$allocationid]);
        }])->where('facility_id',$facilityid)->get();
        if($facprereq->count()<1){
            $content["code"]=406;
            $content["message"]="There are no prerequisites defined";
            $content["success"] ="Warning";
            $content["text"] ="";
        }else{
            $body = \View::make("facility::partials.userprerequisites")->with(compact('facprereq','user_id','allocationid'))->render();
            $content["code"]=200;
            $content["message"]="There are no prerequisites defined";
            $content["success"] ="success";
            $content["text"] =$body;
        }
        //return view("facility::partials.userprerequisites",compact('facprereq','user_id','allocationid'));
       return json_encode($content,true);
    }

    public function saveFacilityuserprerequisite(Request $request){
        $formdata = $request->formdata;
        try{
        foreach($formdata as $key=>$value){

            $facilityid =  $value["facilityid"];
            $userid =  $value["userid"];
            $allocationid =  $value["allocationid"];
            $requisite =  $value["requisite"];
            $choice =  $value["choice"];
            $answers = ["facility_id"=>$facilityid,"facility_allocation_id"=>$allocationid,"user_id"=>$userid,
            "prereq_id"=>$requisite,"answer"=>$choice,"created_by"=>\Auth::user()->id,"updated_by"=>\Auth::user()->id];
            $exist = FacilityUserPrerequisiteAnswer::where(["facility_id"=>$facilityid,"facility_allocation_id"=>$allocationid,
            "user_id"=>$userid,"prereq_id"=>$requisite]);
            if($exist->count()>0){
                $id = ($exist->first())->id;
                $facilityprereq = FacilityUserPrerequisiteAnswer::find($id);
                $facilityprereq->answer = $choice;
                $facilityprereq->updated_by = \Auth::user()->id;
                $facilityprereq->save();
                // FacilityUserPrerequisiteAnswer::insert($answers,["facility_id"=>$facilityid,"facility_allocation_id"=>$allocationid,
                // "user_id"=>$userid,"prereq_id"=>$requisite]);
            }else{
                FacilityUserPrerequisiteAnswer::insert($answers);
            }

        }
        $content["code"]=200;
        $content["message"]="Requisite added successfully";
        $content["success"] ="success";
    }
    catch(\Exception $e){
        $content["code"] =406;
        $content["message"] ="System issue";
        $content["success"] ="warning";

    }
    return json_encode($content,true);
    }

    public function viewFacilityuserallocation(Request $request){
        $customerarray =[];
        $users = collect([]);
        $customersvalue = [];
        if(\Auth::user()->hasAnypermission('super_admin','view_all_customer_facility')){
            $customers = $this->customerrepository->getCustomerList("ALL_CUSTOMER");
            foreach ($customers as $value) {
                if($value->facility_booking==1){
                $customerarray[$value->id] = $value->client_name." (".$value->project_number.")";
                }
            }
            $users = FacilityUser::all();
        }else if(\Auth::user()->hasAnypermission('view_allocated_customer_facility')){
            $customers = $this->customeremployeeallocationrepository->getUserallocatedcustomers();
            foreach ($customers as $key => $value) {
                if($value->customer->facility_booking==1){
                $customerarray[$value->customer->id] = $value->customer->client_name."( ".$value->customer->project_number.")";
                $customerid = $value->customer->id;

                array_push($customersvalue,$value->customer->id);
                }
            }
            $users = FacilityUser::whereIn("customer_id",$customersvalue)->get();
        }
        return  view("facility::userallocation",compact('customerarray'));
    }

    public function addFacilityusers(AddUser $request){
        $userid = $this->facilityrepository->addFacilityusers($request);
        if($userid>0){
            $content["code"]=200;
            $content["message"]="User added successfully";
            $content["success"] ="success";
        }else{
            $content["code"] =406;
            $content["message"] ="System issue";
            $content["success"] ="warning";
        }
        return json_encode($content,true);
    }

    public function editFacilityusers(EditUser $request){
        $userid = $this->facilityrepository->editFacilityusers($request);
        if($userid>0){
            $content["code"]=200;
            $content["message"]="User updated successfully";
            $content["success"] ="success";
        }else{
            $content["code"] =406;
            $content["message"] ="System issue";
            $content["success"] ="warning";
        }
        return json_encode($content,true);
    }


    public function getUserdetails(Request $request){
        $userid = $request->id;
        $userdetails = FacilityUser::select('first_name','last_name','username','email',
        'alternate_email','phoneno','customer_id','unit_no','active')->find($userid)->toArray();
        return json_encode($userdetails,true);
    }

    public function removeFacilityusers(Request $request){
        $userid = $request->userid;
        $user = FacilityUser::find($userid)->delete();
        if($user){
            $content["code"]=200;
            $content["message"]="User removed";
            $content["success"] ="success";
            FacilityBooking::where(["facility_user_id"=>$userid])
            ->whereDate('booking_date_start', '>=', date("Y-m-d"))->delete();
        }else{
            $content["code"] =406;
            $content["message"] ="System issue";
            $content["success"] ="warning";
        }
        return json_encode($content,true);
    }

    public function addAllocatefacilityusers(Request $request){
        $userid = $request->userid;
        $facilities = Facility::select('*')
        ->when($userid>0,function($q)use($userid){
            return $q->
            addselect(\DB::raw('(select count(*) from facility_service_user_allocations where facility_user_id="'.$userid.'"
            and model_type="Modules\Facility\Models\Facility" and model_id=`facilities`.`id`) as alloc'));
        })
        ->with(["facilityserviceuserallocation"=>function($qry)use($userid){
            return $qry->where("facility_user_id",$userid)->count();
        }])
        ->with(['facilityservices'=>function($q)use($userid){
            return $q->select('*')
            ->with(["facilityserviceuserallocation"=>function($qry)use($userid){
                return $qry->where("facility_user_id",$userid)->count();
            }]);
        }])->get();

        return view("facility::partials.allocatefacilityusers",compact('facilities'));
    }

    public function saveorremoveallocation(Request $request){
        $model_id = $request->id;
        $user_id = $request->user_id;
        $type = $request->type;
        $facility_id = $request->facility_id;


        $facilityalloc["facility_user_id"] = $user_id;
        $facilityalloc["model_type"] = "Modules\Facility\Models\Facility";
        $facilityalloc["created_by"] = \Auth::user()->id;
        $facilityalloc["created_at"] = date("Y-m-d H:i");
        $allocationcount = 0;
        if($type=="addfacility"){
            $model_type = "Modules\Facility\Models\Facility";
        }else if($type=="removefacility"){
            $model_type = "Modules\Facility\Models\Facility";
            FacilityBooking::where(["facility_user_id"=>$user_id, 'model_type' => $model_type,
            "model_id"=>$model_id])->whereDate('booking_date_start', '>=', date("Y-m-d"))->delete();
        }else if($type=="addservice"){
            $model_type = "Modules\Facility\Models\FacilityService";
            $servicedetails = FacilityService::find($model_id);
            $facility_id = $servicedetails->facility_id;
            $facilityalloc["model_id"] = $facility_id;
            $facmain = FacilityServiceUserAllocation::
            where(['facility_user_id' => $user_id, 'model_type' => "Modules\Facility\Models\Facility","model_id"=>$facility_id])->count();
            if($facmain<1){
                FacilityServiceUserAllocation::updateOrCreate(
                $facilityalloc,
                ['facility_user_id' => $user_id, 'model_type' => "Modules\Facility\Models\Facility","model_id"=>$facility_id]
                );
            }

        }else if($type=="removeservice"){
            $model_type = "Modules\Facility\Models\FacilityService";
            $servicedetails = FacilityService::where('facility_id',$facility_id)->get()->pluck('id');
            $allocationcount = FacilityServiceUserAllocation::where(['facility_user_id' => $user_id,
             'model_type' => "Modules\Facility\Models\FacilityService"])->whereIn("model_id",$servicedetails)->count();
             if($allocationcount>1){

             }else{
                FacilityServiceUserAllocation::where(
                    ['facility_user_id' => $user_id, 'model_type' => "Modules\Facility\Models\Facility","model_id"=>$facility_id]
                )->delete();

             }

             FacilityBooking::where(["facility_user_id"=>$user_id, 'model_type' => $model_type,
            "model_id"=>$model_id])->whereDate('booking_date_start', '>=', date("Y-m-d"))->delete();

        }
        $facilityalloc = $this->facilityrepository->saveorremoveallocation($model_id,$user_id,$model_type,$type);

        if($type=="addfacility"){
            $lastinsid = $facilityalloc->id;
        }else if($type=="addservice"){
            $model_type = "Modules\Facility\Models\FacilityService";
            $lastinsid = $facilityalloc->id;
        }else{
            $lastinsid = 1;
        }
        if($lastinsid>0){
            $content["code"]=200;
            $content["message"]="Successfully created";
            $content["success"] ="success";

        }else{
            $content["code"] =406;
            $content["message"] ="Already allocated";
            $content["success"] ="warning";
        }
        return json_encode($content,true);
    }

    public function saveorremovemassallocation(Request $request){
        try{
        $model_id = $request->id;
        $userid = $request->user_id;
        $type = $request->type;
        $facility_id = $request->facility_id;
        $lastinsid = 0;
        $now = date("Y-m-d H:i");

        if($type=="addservice"){
            try{
            $servicedetails = FacilityService::find($model_id);

            $facility_id = $servicedetails->facility_id;
            }
            catch(\Throwable $e){

                throw $e;
            }
            $model_type = "Modules\Facility\Models\FacilityService";
        }else if($type=="addfacility"){
            $model_type = "Modules\Facility\Models\Facility";
        }
        try{

        foreach($userid as $user){

            $user_id = $user;
            if($user_id>0){
                $facilityalloc["facility_user_id"] = $user;
                $facilityalloc["created_by"] = \Auth::user()->id;
                $facilityalloc["created_at"] = $now;
                $allocationcount = 0;

                if($type=="addfacility"){
                    //$model_type = "Modules\Facility\Models\Facility";
                }else if($type=="addservice"){

                    $facilityalloc["model_type"] = $model_type;
                    $facilityalloc["model_id"] = $facility_id;

                    FacilityServiceUserAllocation::updateOrCreate($facilityalloc,['facility_user_id' => $user,'model_type' => "Modules\Facility\Models\Facility","model_id"=>$facility_id]);


                }




                    $facilityallocation = $this->facilityrepository->saveorremovemassallocation($model_id,$user_id,$model_type,$type);
                    if($type=="addfacility"){

                       $lastinsid = $facilityallocation;
                    }else if($type=="addservice"){

                       $lastinsid = $facilityallocation;
                    }

            }

        }

            }catch(\Throwable $e){

                throw $e;
            }
        if($lastinsid>0){
            $content["code"]=200;
            $content["message"]="Successfully created";
            $content["success"] ="success";
        }else{
            $content["code"] =406;
            $content["message"] ="Already allocated";
            $content["success"] ="warning";
        }
        return json_encode($content,true);
    }catch(\Throwable $e){
        throw $e;
    }
    }

    public function saveorremovedayallocation(Request $request){
        $model_id = $request->id;
        $user_id = $request->user_id;
        $type = $request->type;
        $days = [];
        if($type=="addweekend"){
            $days = [6,7];
            foreach($days as $day){
                $lastinsid =FacilityUserWeekendDefinition::insert([
                    "facility_service_user_allocation_id"=>$model_id,
                    "day_id"=>$day,
                    "created_by"=>\Auth::user()->id
                ]);
            }
        }else if($type=="addweekday"){
            $days = [1,2,3,4,5];
            foreach($days as $day){
                $lastinsid =FacilityUserWeekendDefinition::insert([
                    "facility_service_user_allocation_id"=>$model_id,
                    "day_id"=>$day,
                    "created_by"=>\Auth::user()->id
                ]);
            }
        }else if($type=="removeweekend"){
            $days = [6,7];
            $lastinsid = FacilityUserWeekendDefinition::where('facility_service_user_allocation_id',$model_id)->whereIn('day_id',$days)->delete();
        }else if($type=="removeweekday"){
            $days = [1,2,3,4,5];
            $lastinsid = FacilityUserWeekendDefinition::where('facility_service_user_allocation_id',$model_id)->whereIn('day_id',$days)->delete();
        }



        if($lastinsid){
            $content["code"]=200;
            $content["message"]="Successfully created";
            $content["success"] ="success";
        }else{
            $content["code"] =406;
            $content["message"] ="Already allocated";
            $content["success"] ="warning";
        }
        return json_encode($content,true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('facility::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('facility::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('facility::edit');
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
