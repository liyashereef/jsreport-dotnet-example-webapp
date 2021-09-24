<?php

namespace Modules\Timetracker\Repositories;

use Modules\Timetracker\Models\MobileSecurityPatrolFenceSummary as ModulesMobileSecurityPatrolFenceSummary;
use Modules\Admin\Models\Geofence as Geofence;

class MobileSecurityPatrolFenceSummaryRepository
{
    const MAP = true;
    const TABLE = false;
    protected $mobileSecurityPatrolFenceSummary;

    public function __construct(
        ModulesMobileSecurityPatrolFenceSummary $mobileSecurityPatrolFenceSummary,
        Geofence $geofence
    ) {
        $this->mobileSecurityPatrolFenceSummary = $mobileSecurityPatrolFenceSummary;
        $this->geofence = $geofence;

    }

    public function save($params)
    {
        //process data if need
        
        return $this->mobileSecurityPatrolFenceSummary->create($params);
    }

    public function getDashboardMapData($data){

        $return['active'] = Geofence::when(isset($data['customerIds']), function ($q) use ($data) {
            $q->whereIn('customer_id', $data['customerIds']);
        })
            ->when($data['startDate'] != null && $data['endDate'] != null, function ($query) use ($data) {
                $query->whereHas('MobileSecurityPatrolFenceData.shift', function ($q) use ($data) {
                    $q->where('start', '>=', $data['startDate']);
                    $q->where('end', '<=', $data['endDate']);
                });
            })
            // ->withTrashed()
            ->with($this->getFenceRelationArray($data, self::MAP))->get();

        $return['inactive'] = [];
        $fenceIds = [];

        if (sizeof($return['active']) >= 1) {
            $fenceIds = data_get($return['active'], '*.id');
        }

        $return['inactive'] =  Geofence::whereNotIn('id', $fenceIds)
            ->whereIn('customer_id', $data['customerIds'])
            ->with(array(
                'customer_trashed' => function ($query) {
                    $query->select(
                        'id',
                        'project_number',
                        'client_name',
                        'contact_person_name',
                        'contact_person_email_id',
                        'contact_person_phone',
                        'address',
                        'province',
                        'city',
                        \DB::raw('CONCAT(client_name, " (",project_number,") ") as client_details')
                    );
                }, 'ContractualVisitUnit' => function ($query) {
                    $query->select('id', 'value');
                }
            ))->get();
           
            return $return;
    }

    public function getDashboardTableData($data)
    {
        
        $return['active'] = Geofence::select('*')->when(isset($data['customerIds']), function ($q) use ($data) {
            $q->whereIn('customer_id', $data['customerIds']);

        })
            ->when($data['startDate'] != null && $data['endDate'] != null, function ($query) use ($data) {
                
                //
                $query->whereHas('MobileSecurityPatrolFenceData.shift', function ($q) use ($data) {
                   
                    
                    $q->where('start', '>=', $data['startDate']);
                    $q->where('end', '<=', $data['endDate']);
                });
            })
            ->withTrashed()
            ->with($this->getFenceRelationArray($data, self::TABLE))            
            ->get()->toArray();

        $return['inactive'] = [];
        $fenceIds = [];

        if (sizeof($return['active']) >= 1) {
            $fenceIds = data_get($return['active'], '*.id');
        }

        $return['inactive'] =  Geofence::whereNotIn('id', $fenceIds)
            ->whereIn('customer_id', $data['customerIds'])
            ->with($this->getFenceRelationArray($data, self::TABLE))
            ->get()
            ->toArray();

        return array_merge($return['active'],$return['inactive']);
    }

    function getFenceRelationArray($data, $is_map = true) {
        $relation_arr = array(
            'customer_trashed' => function ($query) {
                $query->select(
                    'id',
                    'project_number',
                    'client_name',
                    'contact_person_name',
                    'contact_person_email_id',
                    'contact_person_phone',
                    'address',
                    'province',                    
                    'city',
                    \DB::raw('CONCAT(client_name, " (",project_number,") ") as client_details')
                );
            },
            'MobileSecurityPatrolFenceData' => function ($que) use ($data){
                
                $que->select(
                    
                    'fence_id'
                    
                    
                )
                
                    ->when($data['startDate'] != null && $data['endDate'] != null, function ($query) use ($data) {
                        
                        $query->whereHas('shift', function ($q) use ($data) {
                            $q->where('start', '>=', $data['startDate']);
                            $q->where('end', '<=', $data['endDate']);
                        });
                    });
            },
            'mobile_security_patrol_fence_summaries' => function ($que) use ($data){
                
                $que->when($data['startDate']!=null,function($q) use($data){
                    $q->where('created_at','>=',$data['startDate']);
                    $q->where('created_at','<=',$data['endDate']);
                });
            }
        )
        ;
        if($is_map) {
            $relation_arr['ContractualVisitUnit'] = function ($query) {
                $query->select('id', 'value');
            };
        }
        return $relation_arr;
    }

}
