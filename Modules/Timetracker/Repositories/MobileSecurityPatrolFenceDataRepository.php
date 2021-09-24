<?php



namespace Modules\Timetracker\Repositories;

use Modules\Timetracker\Models\MobileSecurityPatrol;
use Modules\Admin\Repositories\CustomerRepository;
use Auth;
use DB;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Timetracker\Models\MobileSecurityPatrolFenceData;
use Modules\Timetracker\Models\MobileSecurityPatrolFenceSummary;

class MobileSecurityPatrolFenceDataRepository
{
    protected $customerRepository;
    protected $mobileSecurityPatrol;
    /**
     * Create a new TripRepository instance.
     *
     * @param  Modules\Timetracker\Models\MobileSecurityPatrol
     */
    public function __construct(
        MobileSecurityPatrol $mobileSecurityPatrol,
        CustomerRepository $customerRepository,
        MobileSecurityPatrolFenceSummaryRepository $fenceSummary,
        MobileSecurityPatrolFenceMetaRepository $fence_meta
    ) {
        $this->mobileSecurityPatrol = $mobileSecurityPatrol;
        $this->customerRepository = $customerRepository;
        $this->fenceSummary = $fenceSummary;
        $this->fence_meta = $fence_meta;
    }

    /**
     * Save Mobile Security Patrol Notes
     *
     * @param array $fence_data_arr
     * @return MobileSecurityPatrolFenceData object
     */
    public function store(array $fence_data_arr)
    {
        $security_patrol_fence_data = MobileSecurityPatrolFenceData::create($fence_data_arr);
        return $security_patrol_fence_data;

    }
    /***
     *  @description - To List Mobile Patrols
     *
     *  @param empty
     *  @return array
     *
     */
    public function getGeofenceList($fromdate,$todate,$cacheddata,$client_id = null,$employee_id = null)
    {
        $user = auth()->user();
        $todate = date("Y-m-d",strtotime("+1 day",strtotime($todate)));
        $allocatedcustomers = $this->customerRepository->getAllAllocatedCustomerId([$user->id]);
        $key = "satellitetracking-".Auth::user()->id;
        $expiresAt = now()->addMinutes(10);

        if($cacheddata == 0 || $cacheddata == null){
            \Cache::forget($key);
        }

        if(\Cache::has($key)){
            $result = \Cache::get($key);
        }else{
            try {

                $shiftIdSummary = MobileSecurityPatrolFenceData::select('shift_id')->distinct('shift_id')->get()->toArray();
//                dd($shiftIdSummary);

                    $result =  EmployeeShift::select('id','employee_shift_payperiod_id','start','end','shift_type_id','work_hours')

                    ->with([
                        'shift_payperiod'=>function($q){
                            $q->select('id','pay_period_id','payperiod_week','employee_id','customer_id');
                        },
                        'geofence_meta'=>function($q){
                            $q->select('id','shift_id','total_visits','missed','average');
                        },
                      /*  'geofence_summary'=>function($query){
                            $query->select('id','fence_id','shift_id','visit_count_expected','visit_count_actual','visit_count_missed','visit_count_average','hours_average');
                        },
                        'geofence_summary.fence_trashed'=>function($q){
                            $q->select('id','title','address','customer_id','geo_lat','geo_lon','geo_rad','visit_count');
                        },*/
                        'shift_payperiod.trashed_customer' => function ($q) {
                            $q->select("id","project_number","client_name");
                        },
                        'shift_payperiod.trashed_employee' => function ($q) {
                            $q->select('id','user_id','employee_no','mst_driver');
                        },
                        'shift_payperiod.trashed_employee.trashedUser' => function ($q) {
                            $q->select('id','first_name','last_name','username','email');
                            // $q->addselect(\DB::raw('(select employee_no from `employees` where user_id=users.id) as employee_no'));
                            $q->addselect(\DB::raw('CONCAT_WS(first_name," ",last_name) as full_name'));
                        }
                    ])->orderBy('created_at', 'desc')
//                    ->whereHas('geofence_meta')
                    ->whereIn('id',$shiftIdSummary)
                    //->whereRaw('$allocatedcustomers in (select customer_id from employee_shift_payperiods where id=employee_shifts.employee_shift_payperiod_id)')
                    //todo:check below relation
                    ->when($fromdate,function($q)use($fromdate,$todate){
                        $q->where('start','>=', $fromdate);
                        $q->where('end','<=', $todate);
                        //$q->whereBetween('end', [$fromdate, $todate]);
                        //$q->where([['start','>=',$fromdate],['end','<=',$todate]]);
                    })
                    ->when(!$user->hasAnyPermission(['view_all_satellite_tracking','admin', 'super_admin']),function($q)use($allocatedcustomers){
                        $q->whereIn(DB::raw("(select customer_id from employee_shift_payperiods where id=employee_shifts.employee_shift_payperiod_id)"),$allocatedcustomers );
                    })
                ->get();

                    \Cache::put($key,$result,$expiresAt);
            } catch (\Throwable $th) {
                //throw $th;
            }

        }
        if($client_id!=null){
            $result= $result->where('shift_payperiod.customer_id', $client_id);
        }
        if($employee_id!=null){
            $result= $result->where('shift_payperiod.employee_id', $employee_id);
        }
     //   dd($result->toArray());
           return $result;

    }


    public function getGeoSummaryList($shiftid){
        return MobileSecurityPatrolFenceSummary::select('id','fence_id','shift_id','visit_count_expected','visit_count_actual','visit_count_missed','visit_count_average','hours_average')
        ->with(['fence_trashed'])->where('shift_id',$shiftid)->get();
    }

    public function setMSPFenceDetailsByShift($shift_id) {
        $total_expected_visits = 0;
        $total_actual_visits = 0;
        $total_missed_visits = 0;
        $overall_average = 0;
        $total_visited_fence_arr = Array();
        $employee_shift = EmployeeShift::with('shift_payperiod','shift_payperiod.trashed_customer.geoFenceDetails')
            ->where('id', $shift_id)
            ->first();
        $expected_fence_visit_count_obj = data_get($employee_shift,'shift_payperiod.trashed_customer.geoFenceDetails');
        $user_id = $employee_shift->shift_payperiod->employee_id;
        $customer_id = $employee_shift->shift_payperiod->customer_id;
        $actual_fence_visit_count_obj = $this->actualVisitCountByShift($shift_id);

        $fence_arr['shift_id'] = $shift_id;
        $total_visits_expected = array_sum(data_get($expected_fence_visit_count_obj, '*.visit_count'));
        foreach($actual_fence_visit_count_obj as $each_fence_visit) {
            $fence_id = $each_fence_visit->fence_id;
            $expected_fence_visit_count_obj_val = $expected_fence_visit_count_obj->first(function($item) use ($fence_id) {
                return $item->id == $fence_id;
            });
            $expected_fence_visit_count = $expected_fence_visit_count_obj_val->visit_count;
            $actual_fence_visit_count = $each_fence_visit->actual_visit_count;
            $missed_fence_visit_count = $expected_fence_visit_count - $actual_fence_visit_count;
            $missed_fence_visit_count = ($missed_fence_visit_count >= 0) ? $missed_fence_visit_count : 0;
            //$fence_visit_average = $actual_fence_visit_count/$expected_fence_visit_count * 100;
            $fence_visit_average = ($expected_fence_visit_count > 0) ? $actual_fence_visit_count/$expected_fence_visit_count * 100 : 0;
            $fence_total_visit_time = $each_fence_visit->total_time;
            $fence_average_visit_time = $each_fence_visit->average_time;


            $total_visited_fence_arr[] = $each_fence_visit->fence_id;
            $fence_arr['fence_id'] = $each_fence_visit->fence_id;
            $fence_arr['visit_count_expected'] = $expected_fence_visit_count;
            $fence_arr['visit_count_actual'] = $actual_fence_visit_count;
            $fence_arr['visit_count_missed'] = $missed_fence_visit_count;
            $fence_arr['visit_count_average'] = $fence_visit_average;
            $fence_arr['hours_total'] = $fence_total_visit_time;
            $fence_arr['hours_average'] = number_format($fence_average_visit_time,3, '.', '');
            $this->fenceSummary->save($fence_arr);

            $total_expected_visits = $total_expected_visits + $expected_fence_visit_count;
            $visit_count_capped = ($actual_fence_visit_count > $expected_fence_visit_count) ? $expected_fence_visit_count : $actual_fence_visit_count;
            $total_actual_visits = $total_actual_visits + $visit_count_capped;
            $total_missed_visits = $total_missed_visits +  $missed_fence_visit_count;
        }

        $total_missed_visits = $total_visits_expected - $total_actual_visits;
        $overall_average = ($this->getOverallAverage($total_actual_visits, $total_visits_expected));
        $this->fence_meta->store($shift_id,$total_actual_visits,$total_missed_visits,$overall_average);
    }

    public function actualVisitCountByShift($shift_id) {
        return MobileSecurityPatrolFenceData::
        selectRaw('fence_id, count(IF(visited = 1, (fence_id), (null) )) as actual_visit_count, sum(duration) as total_time, avg(duration) as average_time')
            ->where('shift_id', $shift_id)
            ->groupBy('fence_id')
            ->get();
    }

    public function getOverallAverage($actual_fence_visit_count, $total_visits_expected) {
        $overall_average = ($actual_fence_visit_count > 0 && $total_visits_expected > 0) ? $actual_fence_visit_count/$total_visits_expected: 0;
        return ($overall_average <= 100) ? $overall_average :100;
    }


}
