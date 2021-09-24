<?php

namespace Modules\FeverScan\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Customer;
use Modules\FeverScan\Models\FeverReading;
use Modules\FeverScan\Models\CanadaCityAndProvinces;
use Modules\FeverScan\Repositories\FeverReadingRepository;
use Modules\Admin\Models\ShiftModule;
use Modules\Admin\Models\ShiftModuleField;


use DB;

class FeverScanController extends Controller
{
    protected $FeverReadingRepository;
    public function __construct(FeverReadingRepository $FeverReadingRepository){
        $this->FeverReadingRepository = $FeverReadingRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('feverscan::index');
    }

    public function siteView(Request $request){
     
            $filter_startdate = $request->startDate;
            $filter_endDate = $request->endDate;
            $filter_city = $request->city;
            $filter_province = $request->province;
            $filter_agegroup = $request->agegroup;
            $filter_gender = $request->gender;
            $filter_tempgroup = $request->tempgroup;
            //All permenent customers
            // $permenentCustomers =  Customer::where('stc',0)->orderBy('client_name')
            // ->select("id","project_number","client_name")
            // ->get();   

            // //All temperory customers
            // $stcCustomers =  Customer::where('stc',1)->orderBy('client_name')
            // ->select("id","project_number","client_name")
            // ->get();

            $province = $this->FeverReadingRepository->getProvinces();
            $cities = $this->FeverReadingRepository->getCities();
         
          
            if(!empty($request->all()) && 
            $request->filled('startDate') 
            || $request->filled('endDate') 
            || $request->filled('agegroup') 
            || $request->filled('province') 
            || $request->filled('gender')  
            || $request->filled('tempgroup')
            || $request->input('city') != 0
            ){
                $filterBaseCustomers  = $this->FeverReadingRepository->getSiteviewcustomer($filter_startdate,$filter_endDate,$filter_city,$filter_province,$filter_agegroup,$filter_gender,$filter_tempgroup);
                $customerIds = data_get($filterBaseCustomers,'*.customer_id');

                $customers =  Customer::orderBy('client_name')->whereIn('id',$customerIds)
                ->select("id","project_number","client_name",'address','geo_location_lat','geo_location_long','postal_code','city','province')
                ->get();
            }else{  
                $customers =  Customer::orderBy('client_name')
                ->select("id","project_number","client_name",'address','geo_location_lat','geo_location_long','postal_code','city','province')
                ->get(); 
            }
            
         
            $gender = ["Male"=>'Male',"Female"=>'Female'];
            $agegroup = $this->FeverReadingRepository->getFiltergroup(2);
            $tempgroup = $this->FeverReadingRepository->getTemperatureData();
           
            $start_date = '';
            $end_date = '';
            if($request->filled('startDate')){
                $start_date = \Carbon::parse($request->input('startDate'))->format('M d, Y');
            }

            if($request->filled('endDate')){
                $end_date = \Carbon::parse($request->input('endDate'))->format('M d, Y');
            }
           
            return view('feverscan::site-view', compact('customers','cities','province','gender','province','request','agegroup','tempgroup',
            'filter_city','filter_province','start_date','end_date'));
        

    }


    public function individualView(Request $request){
     
        $filter_startdate = $request->startDate;
        $filter_endDate = $request->endDate;
        $filter_city = $request->city;
        $filter_province = $request->province;
        $filter_agegroup = $request->agegroup;
        $filter_gender = $request->gender;
        $filter_tempgroup = $request->tempgroup;
        //All permenent customers
        // $permenentCustomers =  Customer::where('stc',0)->orderBy('client_name')
        // ->select("id","project_number","client_name")
        // ->get();   

        // //All temperory customers
        // $stcCustomers =  Customer::where('stc',1)->orderBy('client_name')
        // ->select("id","project_number","client_name")
        // ->get();
        $customers =  Customer::orderBy('client_name')
        ->select("id","project_number","client_name")
        ->get();
        $province = $this->FeverReadingRepository->getProvinces();
        $cities = $this->FeverReadingRepository->getCities();
        //$city = Customer::orderBy('city')->distinct('city')->pluck('city')->toArray();
        $city = [];
        $agegroup = $this->FeverReadingRepository->getFiltergroup(2);
        // $tempgroup = $this->FeverReadingRepository->getFiltergroup(1);
        $tempgroup = $this->FeverReadingRepository->getTemperatureData();
        $gender = ["Male"=>'Male',"Female"=>'Female'];
        $customer_id = $request->customer_id;
        $customerarray = array_filter(explode(",",$customer_id));
        
        $customer_score = [];
        
        // if($request->startDate){
        //     $filter_startdate = $request->startDate;
        // }else{
        //     $filter_startdate =date("-3 days",strtotime(date("Y-m-d")));
        // }
        // if($request->startDate){
        //     $filter_endDate = $request->endDate;
        // }else{
        //     $filter_endDate = date("Y-m-d");
        // }
        

        $individualviewdata  = $this->FeverReadingRepository->getIndividualviewdata($filter_startdate,$filter_endDate,$filter_city,
        $filter_province,$filter_agegroup,$filter_gender,$filter_tempgroup,$customerarray);
        return view('feverscan::individual-view', compact('customers','cities','gender','province','request','agegroup','tempgroup','individualviewdata',
    'customer_id','filter_city'));
    
}

    public function getFeverReadingReportView(){
        return view('feverscan::report-view');
    }

    public function getFeverReadingReportData(Request $request){
        $readings =  $this->FeverReadingRepository->getReportData($request->all());
        return  datatables()->of($readings)->addIndexColumn()->toJson();
    }

    public function getCustomerFeverReadinginfo(Request $request){
         $inputs = $request->all();
        $tempgroup = $this->FeverReadingRepository->getTemperatureData();
        $agegroup = $this->FeverReadingRepository->getFiltergroup(2);
        $customer = Customer::select("id","project_number","client_name",'address','geo_location_lat','geo_location_long','postal_code','city','province')->find($request->customer_id);
        $genderAgeGroup = $this->formatGenderAgeGroup($this->FeverReadingRepository->getGenderAgeGroupByCustomer($request->all()),$agegroup); 
        $temperatureAgeGroup = $this->formatTemperatureAgeGroup($this->FeverReadingRepository->getTemperatureAgeGroupByCustomer($request->all()), $agegroup,$tempgroup); 
        return $this->setInfoData($genderAgeGroup,$temperatureAgeGroup,$customer,$agegroup,$tempgroup,$inputs);
    }

    public function formatGenderAgeGroup($genderAgeGroup,$agegroup){
       
        $result = [];
        $male_total = 0;
        $female_total = 0;

        foreach($agegroup as $ageKey=>$age){

            $result[$age]['Male'] = 0;
            $result[$age]['Female'] = 0;
            $result[$age]['total'] = 0;

            foreach($genderAgeGroup as $key=>$value){
                if($age == $value->age){
                    if($value->gender == 'Male'){
                        $result[$age]['Male'] = $value->total;
                        $male_total = $male_total + $value->total;
                    }elseif($value->gender == 'Female'){
                        $result[$age]['Female'] = $value->total;
                        $female_total = $female_total + $value->total;
                    }else{}
                }
                $result[$age]['total'] = $result[$age]['Male'] + $result[$age]['Female'];
            }
        }
        $result['male_total'] =$male_total;
        $result['female_total'] =$female_total;
        $result['grand_total'] =$male_total+$female_total;
        
        return $result;
    }


    public function formatTemperatureAgeGroup($temperatureAgeGroup,$agegroup,$tempgroup){
       
        $result = [];
   
        foreach($tempgroup as $tempKey=>$temp){
            $tempgroupAgeTotal = 0;
                foreach($agegroup as $ageKey=>$age){

                $result[$tempKey][$age]['count'] = 0;

                foreach($temperatureAgeGroup as $key=>$value){ 
                    if($tempKey == $value->temperature_id && $age == $value->age){ 
                        $result[$tempKey][$age]['count'] = $value->total;
                        $tempgroupAgeTotal = $tempgroupAgeTotal + $result[$tempKey][$age]['count'];
                    }
                }
            }
        }

        return $result;
    }

    public function setInfoData($genderAgeGroup, $temperatureAgeGroup,$customer,$agegroup,$tempgroup,$inputs){
        
        if(!empty($inputs) && $inputs['end_date'] == ''){
            $inputs['end_date'] = \Carbon::now()->format('M d, Y');
        }
        $info_html = '';
        $tempAgeTotal = [];
        $ageGrandTotal = 0;
        if(!empty($customer)){
                
                $address = ucwords($customer->address) . '<br/>' . ucwords($customer->city) . ', ' . trim($customer->postal_code) . ', ' . trim($customer->province);
                $info_html = '<div class="row map-row">';
                $info_html .= '<div class="col-md-6 col-xs-12 col-sm-6 col-lg-6"> <div class="row">';
                $info_html .= '<div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Project No</div>';
                $info_html .= '<div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' . $customer->project_number .'</div></div>';
                $info_html .= '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Client</div>';
                $info_html .= '<div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' . $customer->client_name . '</div></div>';
                $info_html .= '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Address</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"><span>' . $address . '</span></div></div>';
               
                $info_html .= '</div><div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">';
                $info_html .= '<div class="row"> <div class="col-md-10 col-lg-8 col-xs-12 col-sm-12 map-label" style="font-size:13px;padding-bottom:12px;font-stretch:semi-expanded;" >Date Range</div></div>';
                $info_html .= '<div class="row"> <div class="col-md-3 col-lg-3 col-xs-12 col-sm-12 map-label">Start </div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">'.(($inputs['start_date'] )? ($inputs['start_date']) : '  --').'</div></div>';
                $info_html .= '<div class="row"> <div class="col-md-3 col-lg-3 col-xs-12 col-sm-12 map-label">End </div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"><span>'.$inputs['end_date'].'</span></div></div>';
                $info_html .= '</div></div>';

        }

        if(!empty($genderAgeGroup)){
            $info_html .= '<div class="row map-row" > <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12 split-line">';
            $info_html .= '<div class="row" id="dashboard"> </div><div class="row"><div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 map-legend-style">';
        
        // Gender Age Group - Titles
            $info_html .= '<div class="row">';
            $info_html .= '<div class="col title-style">Gender </div>';
            foreach($agegroup as $ageKey=>$age){
                $info_html .= '<div class="col title-style" style="text-align:center;">' .$age. '</div>';
            }
            $info_html .= '<div class="col total-style" style="text-align:center;"> Total </div>';
            $info_html .= '</div>';

        // Gender Age Group - Male Count
            $info_html .= '<div class="row">';
            $info_html .= '<div class="col title-style"> Male </div>';
            foreach($agegroup as $ageKey=>$age){
                foreach($genderAgeGroup as $ageKey=>$ageGroup){
                    if($age == $ageKey){
                        $info_html .= '<div class="col data-style" style="text-align:center;">' .$ageGroup['Male']. '</div>';
                    }
                    
                }
            }
                $info_html .= '<div class="col total-style" style="text-align:center;"> '.$genderAgeGroup['male_total'].' </div>';
                $info_html .= '</div>';
        
        // // Gender Age Group - Female Count
            $info_html .= '<div class="row">';
            $info_html .= '<div class="col title-style"> Female </div>';
            foreach($agegroup as $ageKey=>$age){
                foreach($genderAgeGroup as $ageKey=>$ageGroup){
                    if($age == $ageKey){
                        $info_html .= '<div class="col data-style" style="text-align:center;">' .$ageGroup['Female']. '</div>';
                    }  
                }  
            }
            
            $info_html .= '<div class="col total-style" style="text-align:center;"> '.$genderAgeGroup['female_total'].' </div>';
            $info_html .= '</div>';
             
            // Gender Age Group - Total Count
            $info_html .= '<div class="row" >';
            $info_html .= '<div class="col total-style"> Total </div>';
            foreach($agegroup as $ageKey=>$age){
                foreach($genderAgeGroup as $ageKey=>$ageGroup){
                    if($age == $ageKey){
                    $info_html .= '<div class="col total-style border-top" style="text-align:center;">'.$ageGroup['total'].'</div>';
                }     
            }
        }
                $info_html .= '<div class="col total-style border-top" style="text-align:center;"> '.$genderAgeGroup['grand_total'].' </div>';
                $info_html .= '</div>';
                $info_html .= '</div></div></div></div>';

        }

        if(!empty($temperatureAgeGroup)){

            $info_html .= '<div class="row map-row" > <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">';
            $info_html .= '<div class="row" id="dashboard"> </div><div class="row"><div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 map-legend-style">';
        
        // Temperature Age Group - Titles
            $info_html .= '<div class="row">';
            $info_html .= '<div class="col title-style"> Reading </div>';
            foreach($agegroup as $ageKey=>$age){
                $info_html .= '<div class="col title-style" style="text-align:center;">' .$age. '</div>';
            }
            $info_html .= '<div class="col total-style" style="text-align:center;"> Total </div>';
            $info_html .= '</div>';

        // Temperature Age Group - Data
            foreach($tempgroup as $tempKey=>$temp){
                $tempTotal = 0;
                $info_html .= '<div class="row">';
                $info_html .= '<div class="col title-style" style="width: 150px;"> '.$temp.' </div>';
                foreach($agegroup as $ageKey=>$age){
                   
                    if (!array_key_exists($age,$tempAgeTotal))
                    {
                        $tempAgeTotal[$age] = 0;
                    }
                    
                   
                    foreach($temperatureAgeGroup as $key=>$temperature){  
                        if($tempKey ==  $key){ 
                            $info_html .= '<div class="col data-style " style="text-align:center;">' .$temperature[$age]['count']. '</div>';
                            $tempTotal = $tempTotal+ $temperature[$age]['count'];
                            
                            $tempAgeTotal[$age] =  $tempAgeTotal[$age]+$temperature[$age]['count'];
                        } 
                    }
                   
                }
                    $info_html .= '<div class="col total-style" style="text-align:center;"> '.$tempTotal.' </div>';
                    $info_html .= '</div>';
            }

            $info_html .= '<div class="row" >';
            $info_html .= '<div class="col total-style"> Total </div>';
            foreach($tempAgeTotal as $key=>$ageTotal){ 
                $info_html .= '<div class="col total-style border-top" style="text-align:center;">'.$ageTotal.'</div>';
                $ageGrandTotal = $ageGrandTotal + $ageTotal;
            }
            $info_html .= '<div class="col total-style border-top" style="text-align:center;"> '.$ageGrandTotal.' </div>';
            $info_html .= '</div>';

            $info_html .= '</div></div></div></div>';

        }

         return $info_html;

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('feverscan::create');
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
        return view('feverscan::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('feverscan::edit');
    }

    public function alterFeverScanModule(Request $request){
        $customer_id = false;
        $feverscanmodule = \Config::get('globals.fever_scan_module');
        try {
        
            $modules = ShiftModule::where('module_name',$feverscanmodule)->get();
            foreach ($modules as $module) {
                # code...
                echo $moduleid = $module->id;
                $unwanteddropdowns = ['Name','City','Province'];
                $customer_id = $module->customer_id;
                $modulefields = ShiftModuleField::where('module_id',$moduleid)->whereIn('field_name',$unwanteddropdowns)->delete();
                //dd($modulefields);
            }
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setFeverScanModule($customer_id = false){
        try {
            DB::beginTransaction();      
     
        $dropdowns  = ["temperature","age_group"];
        $tempid  = DB::table('shift_module_dropdowns')->insertGetId([
                "dropdown_name"=>"Temperature",
                "info"=>0,
                "detail"=>null,
                "created_at"=>"2020-03-13"
            ]);
        if($tempid>0){
            $seq = 1;
            for($i=96;$i<=103.2;$i=$i+0.1){
                $temperature = $i;
                DB::table('shift_module_dropdown_options')->insertGetId([
                    "option_name"=>round($temperature,1),
                    "option_info"=>null,
                    "shift_module_dropdown_id"=>$tempid,
                    "order_sequence"=>$seq,
                    "created_at"=>"2020-03-13" 
                ]);
                $seq++;
            }
        }

        $age= DB::table('shift_module_dropdowns')->insertGetId([
            "dropdown_name"=>"Age Group",
            "info"=>0,
            "detail"=>null,
            "created_at"=>"2020-03-13"
        ]);

        if($age>0){
            $seq =1;
            $agearray = ["Under 25","25-35","35-50","50-60","60-70","70 Plus"];
            foreach ($agearray as $key => $value) {
                DB::table('shift_module_dropdown_options')->insertGetId([
                    "option_name"=>$value,
                    "option_info"=>null,
                    "shift_module_dropdown_id"=>$age,
                    "order_sequence"=>$seq,
                    "created_at"=>"2020-03-13" 
                ]);
                $seq++;
            }
            

        }

        $city  = DB::table('shift_module_dropdowns')->insertGetId([
            "dropdown_name"=>"City",
            "info"=>0,
            "detail"=>null,
            "created_at"=>"2020-03-13"
        ]);

        $province  = DB::table('shift_module_dropdowns')->insertGetId([
            "dropdown_name"=>"Province",
            "info"=>0,
            "detail"=>null,
            "created_at"=>"2020-03-13"
        ]);
  
       $customers = Customer::select('id')->where('active',1)
       ->when($customer_id != false, function ($q) use ($customer_id) {
        return $q->where('id', $customer_id);
        })->get();

       foreach ($customers as $each_customer) {
          $module_id  = DB::table('shift_modules')->insertGetId([
              "customer_id"=>$each_customer->id,
              "module_name"=>'Fever Scan',
              "is_active"=>1,
              "dashboard_view"=>0,
              "enable_timeshift"=>0,
              'created_at' => \Carbon\Carbon::now()
  
          ]);

        DB::table('shift_module_fields')->insert(array(
            0 => array(
                 "module_id"=> $module_id,
                 "field_name"=> 'Name',
                 "system_name"=>'name',
                 "field_type"=>7,
                 "field_status"=> 1,
                 "dropdown_id"=>0,
                 "is_multiple_photo"=>0,
                 "order_id"=>1,
                 'created_at' => \Carbon\Carbon::now()
            ),
            1 => array(
                "module_id"=> $module_id,
                "field_name"=> 'Gender',
                "system_name"=>'gender',
                "field_type"=>3,
                "field_status"=> 1,
                "dropdown_id"=>12,
                "is_multiple_photo"=>0,
                "order_id"=>2,
                'created_at' => \Carbon\Carbon::now()
            ),
            2 => array(
                "module_id"=> $module_id,
                 "field_name"=> 'Age Group',
                 "system_name"=> 'age_group',
                 "field_type"=>3,
                 "field_status"=> 1,
                 "dropdown_id"=>$age,
                 "is_multiple_photo"=>0,
                 "order_id"=>3,
                 'created_at' => \Carbon\Carbon::now()
            ),
            3 => array(
                "module_id"=> $module_id,
                 "field_name"=> 'Temperature',
                 "system_name"=>'temperature',
                 "field_type"=>3,
                 "field_status"=> 1,
                 "dropdown_id"=> $tempid,
                 "is_multiple_photo"=>0,
                 "order_id"=>4,
                 'created_at' => \Carbon\Carbon::now()
            ),
            4 => array(
                "module_id"=> $module_id,
                 "field_name"=> 'Notes',
                 "system_name"=>'notes',
                 "field_type"=>4,
                 "field_status"=> 1,
                 "dropdown_id"=>0,
                 "is_multiple_photo"=>0,
                 "order_id"=>6,
                 'created_at' => \Carbon\Carbon::now()
            ),
            5 => array(
                 "module_id"=> $module_id,
                 "field_name"=> 'Location',
                 "system_name"=>'location',
                 "field_type"=>2,
                 "field_status"=> 1,
                 "dropdown_id"=>0,
                 "is_multiple_photo"=>0,
                 "order_id"=>7,
                 'created_at' => \Carbon\Carbon::now()
            )            
        ));

    }
        
     
        

       
        DB::commit();
    } catch (\Throwable $th) {
        dd($th);
        DB::rollBack();

    }

    }
}
