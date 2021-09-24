<?php

namespace Modules\Vehicle\Repositories;

use Modules\Vehicle\Models\VehicleMaintenanceRecord;
use Modules\Admin\Models\RegionLookup;
use Modules\Vehicle\Models\VehicleVendorLookup;


class VehicleAnalysisRepository
{
    public function __construct(VehicleMaintenanceRecord $maintenance,RegionLookup $regionLookup,VehicleVendorLookup $vehicleVendorLookup)
    {
        $this->model = $maintenance;
        $this->vehicleVendorLookup = $vehicleVendorLookup;
        $this->regionLookup = $regionLookup;
    }

 /**
     * Get all maintenance list
     *
     * @param empty
     * @return array
     */
    public function getAll($vendorId, $fromDate, $toDate)
    {
        $vehileMaintenanceRecordList = $this->vehicleVendorLookup
        ->when($vendorId, function ($q) use ($vendorId){
            $q->where('id',$vendorId);
        })
        ->get();
       return $this->prepareDataForSpendReport($vehileMaintenanceRecordList,$fromDate, $toDate);
    }

    public function prepareDataForSpendReport($vehileMaintenanceRecordList,$fromDate, $toDate)
    {
        $rows = [];
        $regionrows = [];
        $datatable_rows = array();
        foreach($vehileMaintenanceRecordList as $index => $record) {
                $rows['vendor'] = $record->vehicle_vendor;
                $regions =   $this->regionLookup->get()->toArray();
                $totalArray = ["region_name" => "Total","id" => count($regions)];
                $regionList = array_merge($regions, [$totalArray]);
                $regionrows = [];
                $regionarrayrows =[];
                $total = null;
                $totalArraySum = null;
                foreach($regionList as $key => $regions){
                    $regionrows['region'] =  $regions['region_name'];
                    $maintenanceRecord = $this->model
                    ->with(['vehicle'])
                    ->whereHas('vehicle', function($q) use($regions){
                        $q->where('region',$regions['id']);
                     })
                     ->when($toDate, function ($q) use ($toDate){
                        $q->where('service_date', '<=', $toDate);
                    })
                    ->when($fromDate, function ($q) use ($fromDate){
                        $q->where('service_date', '>=', $fromDate);
                    })
                    ->where('vendor_id',$record->id)
                    ->get();
                    $totalCharges = $maintenanceRecord->pluck('total_charges')->toArray();
                    $arraySum = array_sum($totalCharges);
                    $totalArraySum += $arraySum;
                    if(!empty($totalCharges)){
                        $total = $arraySum;
                    }else if($regions['region_name'] == "Total"){
                        $total = $totalArraySum;
                    }else{
                        $total = null;
                    }
                    $regionrows['cost'] =  $total;
                    array_push($regionarrayrows, $regionrows);
                }
                $rows['regions'] = $regionarrayrows;

                array_push($datatable_rows, $rows);
        }
        return $datatable_rows;
    }

    public function prepareDataForTotalRow($vendorId,$fromDate, $toDate){
        $rows = [];
        $regionrows = [];
        $rows['vendor'] = "Total";
        $regions =   $this->regionLookup->get()->toArray();
        $totalArray = ["region_name" => "Total","id" => count($regions)];
        $regionList = array_merge($regions, [$totalArray]);
        $regionrows = [];
        $regionarrayrows =[];
        $total = null;
        $regionTo = null;
        $regionTotal = [];
        $rowTotal = [];
        foreach($regionList as $key => $regions){
            $vehileMaintenanceRecordList = $this->model
            ->with('vehicle')
            ->whereHas('vehicle', function($q) use ($regions) {
                $q->where('region', $regions['id']);
            })
            ->when($vendorId, function ($q) use ($vendorId){
                $q->where('vendor_id',$vendorId);
            })
            ->when($toDate, function ($q) use ($toDate){
                $q->where('service_date', '<=', $toDate);
            })
            ->when($fromDate, function ($q) use ($fromDate){
                $q->where('service_date', '>=', $fromDate);
            })
            ->where('total_charges','!=',null)
            ->where('vendor_id','>',0)
            ->get()
            ->toArray();
            if(!empty($vehileMaintenanceRecordList)){
                foreach($vehileMaintenanceRecordList as $key => $maintenanceList){
                    if($maintenanceList['vehicle']['region'] == $regions['id']){
                        $regionTotal =   ($maintenanceList['total_charges'] != null) ? $maintenanceList['total_charges'] : null;
                        $regionTo += $regionTotal;
                    }
                }
            }else{
                $regionTo = null;
            }
            $rowTotal[] +=  $regionTo;
            $rowTotalValue = array_sum($rowTotal);
            $regionrows['region'] =  $regions['region_name'];
            if($regions['region_name'] == "Total"){
                $total = $rowTotalValue;
            }else{
                $total = $regionTo;
            }
            $regionrows['cost'] =  $total;
            array_push($regionarrayrows, $regionrows);
        }
        $rows['regions'] = $regionarrayrows;
        return $rows;
    }


    public function getRegionList(){
        $regionList = $this->regionLookup->get();
        return $this->prepareDataForRegionRow($regionList);
    }

    public function prepareDataForRegionRow($regionList){
        $datatable_rows = array();
        $each_row = [];
        foreach ($regionList as $key => $each_list) {
                $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
                $each_row["regions"] = isset($each_list->region_name) ? $each_list->region_name : "--";
                $each_row['key'] = 'value';
                array_push($datatable_rows, $each_row);
            }
        return $datatable_rows;
    }

    public function getVendorLookups()
    {
        return $this->vehicleVendorLookup->pluck('vehicle_vendor', 'id')->toArray();
    }


}
