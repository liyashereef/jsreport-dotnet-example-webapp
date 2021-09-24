<?php

namespace Modules\Expense\Repositories;

use Modules\Expense\Models\ExpenseMileageReimbursementFlatRate;
use Modules\Expense\Models\ExpenseMileageReimbursementSlabRate;

use App\Services\HelperService;


class MileageReimbursementRepository
{

    protected $model,$helperService;

    public function __construct(ExpenseMileageReimbursementFlatRate $mileageReimbursementflatRate,
     HelperService $helperService,
     ExpenseMileageReimbursementSlabRate $mileageReimbursementslabRate)
    {
        $this->model = $mileageReimbursementflatRate;
        $this->mileageReimbursementslabRatemodel = $mileageReimbursementslabRate;
        $this->helperService = $helperService;
        

    }

    public function getAll()
    {
        $mileage_details = $this->model->select(['id', 'flat_rate', 'created_at', 'user_id','is_active'])
            ->with('createdBy')
            ->orderBy('flat_rate', 'asc')
            ->get();
            return $this->prepareDataForMileagetype($mileage_details);
    }

    public function prepareDataForMileagetype($mileage_details)
    {
        $datatable_rows = array();
        foreach ($mileage_details as $key => $each_list) {
            $each_row["id"]                      =  isset($each_list->id)?$each_list->id:"--";
            $each_row["flat_rate"]               =  isset($each_list->flat_rate)?$each_list->flat_rate:"--";
            $each_row["created_at"]              =  isset($each_list->created_at)?$each_list->created_at->toFormattedDateString():"--";;
            $each_row['created_by']              =  isset($each_list->createdBy)?$each_list->createdBy->first_name." ".$each_list->createdBy->last_name:"--";          
            $each_row["is_active"]               =  $each_list->is_active==1 ? "Active":"Inactive";
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function save($data)
    {
   // dd($data);
        
       $data['user_id'] = \Auth::user()->id;
        if($data['mileage-reimbursement-type']==0)
          {
            $active_status= $this->model->where('id','!=', $data['id'])->update(['is_active' => 0]);
            $mileage_save = $this->model->updateOrCreate(array('id' => $data['id']), $data);
            return $mileage_save;

    
             }

        if($data['mileage-reimbursement-type']==1)

            {
                $ids=$this->mileageReimbursementslabRatemodel->pluck('id')->toArray();
                $this->mileageReimbursementslabRatemodel->destroy($ids);
                if(isset($data['starting_kilometer'])){
                for($i = 0; $i < count($data['starting_kilometer']); $i++){
                    
                    $list['starting_kilometer'] = $data['starting_kilometer'][$i];
                    $list['ending_kilometer'] = $data['ending_kilometer'][$i];
                    $list['cost'] = $data['cost'][$i];  
                    $mileage_save = $this->mileageReimbursementslabRatemodel->create($list);

                }
                return $mileage_save;
               }
                
            }
    
}
  public function get($id)
           { 
    return $this->model->find($id);
     }
}
        
    
    

