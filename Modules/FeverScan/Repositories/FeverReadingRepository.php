<?php

namespace Modules\FeverScan\Repositories;

use Log;
use Modules\FeverScan\Models\FeverReading;
use Modules\Admin\Models\AdminColorsetting;
use Modules\FeverScan\Models\CanadaCityAndProvinces;
use DB;

class FeverReadingRepository
{

    protected $model;
    public function __construct(
        FeverReading $feverReading
    ) {
        $this->model = $feverReading;
    }

     /**
     * Get 
     * @param $customer_id
     * @return string
     */
    public function store($request)
    {
        $data['customer_id'] = $request->customer_id;
        $data['module_id'] = $request->module_id;
        $data['shift_id'] = $request->shift_id;
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;
        $data['gender'] = $request->gender;
        $data['age_group'] = $request->age_group;
        $data['city'] = $request->city;
        $data['province'] = $request->province;
        $data['temperature'] = $request->temperature;
        $data['temperature_id'] = $request->temperature_id;
        $data['notes'] = $request->notes;
        $data['geo_location_lat'] = $request->geo_location_lat;
        $data['geo_location_long'] = $request->geo_location_long;
        $data['created_by'] = $request->created_by;
        return $this->model->create($data);
    }   
    /**
     * 
     *groupidentifier == 1(Temperature),==2 (Age) 
     */
    public function getFiltergroup($groupidentifier=1){
        $returnarray =[];
        $values =  AdminColorsetting::where('fieldidentifier',$groupidentifier)->get()->pluck('title');
        foreach ($values as $key => $value) {
            $returnarray[$value]=$value;
        }
        return $returnarray;
    }

    public function getTemperatureData(){
        $returnarray =[];
        $values = AdminColorsetting::where('fieldidentifier',1)->select('id','title')->get(); 
        foreach ($values as $key => $value) {
            $returnarray[$value->id]=$value->title;
        } 
        return $returnarray;       
    }

    public function getProvinces(){
        $cityarray = [];
         $cities = FeverReading::select('province')->distinct()->get()->pluck('province');
         foreach ($cities as $key => $value) {
            $cityarray[$value] = $value;
         }
         //dd($cities);
         return $cityarray;
    }

    public function getCities(){
        $cityarray = [];
         $cities = FeverReading::select('province','city')->distinct('city')->get();
        // dd($cities);
         return $cities;
    }
    public function getIndividualviewdata($filter_startdate,$filter_endDate,$filter_city,
    $filter_province,$filter_agegroup,$filter_gender,$filter_tempgroup,$customerarray){
        if($filter_startdate==""){
            $filter_startdate= date("Y-m-d",strtotime("-3 day",strtotime(date("Y-m-d"))));
        }
        if($filter_endDate==""){
            $filter_endDate = date("Y-m-d");
        }
        $filter_endDate= date("Y-m-d",strtotime("+1 day",strtotime($filter_endDate)));
        // $rangebegin =0;
        // $rangeend = 97;
        // if($filter_tempgroup){
        //     $temperature = AdminColorsetting::where('title',$filter_tempgroup)->first();
        //     $rangebegin =$temperature->rangebegin;
        //     $rangeend = $temperature->rangeend;
        // }
        $maparray = [];
        $fevermassresult = [];
        $feverReading = FeverReading::select('*',\DB::raw('(select colorhexacode from admin_colorsettings where id=fever_readings.temperature_id) as colorcode'))->whereNotNull('geo_location_lat')->whereNotNull('geo_location_long')
        ->with(array('customer'=>function($query){
            $query->select('id','project_number','client_name','address','postal_code','city','province');
        }))
        ->where('created_at','>=',$filter_startdate)
        ->where('created_at','<=',$filter_endDate)
        ->when($filter_agegroup,function($q)use($filter_agegroup){
            return $q->where('age_group',$filter_agegroup);
        })
        ->when($filter_gender,function($q)use($filter_gender){
            return $q->where('gender',$filter_gender);
        })
        ->when($filter_tempgroup,function($q)use($filter_tempgroup){
            // return $q->whereBetween('temperature',[$rangebegin,$rangeend]);
            return $q->where('temperature_id',$filter_tempgroup);
        })
        ->when($filter_province,function($q)use($filter_province){
            return $q->where('province',$filter_province);
        })
        ->when($filter_city,function($q)use($filter_city){
            return $q->where('city',$filter_city);
        })
        // ->when($customerarray,function($q){

        // })
        // ->WhereHas('customer',function($q)use($filter_city,$filter_province){
        //     //return $q->where('$filter_city');
        // })
        ->get();
        return $feverReading;
    }

    public function getSiteviewcustomer($filter_startdate,$filter_endDate,$filter_city,
    $filter_province,$filter_agegroup,$filter_gender,$filter_tempgroup){
    
        // $filter_endDate= date("Y-m-d",strtotime("+1 day",strtotime($filter_endDate)));

        $maparray = [];
        $fevermassresult = [];
        $feverReading = FeverReading::select('customer_id')->whereNotNull('geo_location_lat')->whereNotNull('geo_location_long')
        ->with(array('customer'=>function($query){
            $query->select('id','project_number','client_name','address','postal_code','city','province');
        }))

        ->when($filter_startdate,function($q)use($filter_startdate){
            return $q->whereDate('created_at','>=',$filter_startdate);
        })
        ->when($filter_endDate,function($q)use($filter_endDate){
            return $q->whereDate('created_at','<=',$filter_endDate);
        })
        ->when($filter_agegroup,function($q)use($filter_agegroup){
            return $q->where('age_group',$filter_agegroup);
        })
        ->when($filter_gender,function($q)use($filter_gender){
            return $q->where('gender',$filter_gender);
        })
        ->when($filter_tempgroup,function($q)use($filter_tempgroup){
            return $q->where('temperature_id',$filter_tempgroup);
        })
        ->when($filter_province,function($q)use($filter_province){
            return $q->where('province',$filter_province);
        })
        ->when($filter_city,function($q)use($filter_city){
           return $q->where('city',$filter_city);
        })
        ->distinct('customer_id')
        ->get();
        return $feverReading;
    }

    public function getGenderAgeGroupByCustomer($inputs){
        return  $feverReadings = FeverReading::select('customer_id','gender','age_group as age',\DB::raw('count(*) as total'))
            ->groupBy('gender','age_group','customer_id')
            ->where('customer_id',$inputs['customer_id'])
            ->get();
    }

    public function getTemperatureAgeGroupByCustomer($inputs){
        return  $feverReadings = FeverReading::select('customer_id','temperature_id','age_group as age',\DB::raw('count(*) as total'))
            ->groupBy('temperature_id','age_group','customer_id')
            ->where('customer_id',$inputs['customer_id'])
            ->get();
    }

    public function getReportData($inputs){
        
        $feverReading = FeverReading::when($inputs['from_date'],function($q)use($inputs){
            $q->whereDate('created_at','>=',$inputs['from_date']);
        })
        ->when($inputs['to_date'],function($q)use($inputs){
            $q->whereDate('created_at','<=',$inputs['to_date']);
        })
        ->with(['customer'=>function($query){
            $query->select('id','project_number','client_name');
        }])
        ->orderBy('created_at','DESC')
        ->get();

        return $feverReading;
    }

}
