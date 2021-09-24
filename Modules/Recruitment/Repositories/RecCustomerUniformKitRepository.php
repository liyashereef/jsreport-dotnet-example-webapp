<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecCustomerUniformKit;
use Modules\Recruitment\Models\RecCustomerUniformKitMapping;
use Modules\Admin\Models\Customer;
use Modules\Recruitment\Models\RecJob;
use Modules\Recruitment\Models\RecCandidateUniformSize;
use Modules\Recruitment\Models\RecCandidateUniformCalculated;
use Modules\Recruitment\Models\RecUniformItemSizeMeasurementMapping;
use Modules\Recruitment\Models\RecCandidateUniformShippmentDetail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RecCustomerUniformKitRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new RecCustomerUniformKitRepository instance.
     *
     * @param  Modules\Recruitment\Models\RecUniformSizes $recUniformSizes
     */
    public function __construct(RecCustomerUniformKit $recCustomerUniformKitModel, RecCustomerUniformKitMapping $recCustomerUniformKitMappingModel, RecCandidateUniformSize $recCandidateUniformSize, RecCandidateUniformCalculated $recCandidateUniformCalculated, RecJob $RecJob)
    {
        $this->model = $recCustomerUniformKitModel;
        $this->mappingModel = $recCustomerUniformKitMappingModel;
        $this->recCandidateUniformSize=$recCandidateUniformSize;
        $this->recCandidateUniformCalculated=$recCandidateUniformCalculated;
    }

    /**
     * Get Experience  lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        $uniformKitList =  $this->model->select(['id', 'kit_name','customer_id'])->get();
        return $this->prepareDataForUniformKitList($uniformKitList);
    }

        /**
     * Prepare datatable elements as array.
     * @param  $result
     * @return array
     */
    public function prepareDataForUniformKitList($uniformKitList)
    {
        $datatable_rows = array();
        foreach ($uniformKitList as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $customerDetails = Customer::where('id', $each_list->customer_id)->first();
            $each_row["project_details"] = isset($customerDetails->id) ? $customerDetails->client_name.' ('.$customerDetails->project_number.')'  : '';
            $each_row["customer_id"] = isset($each_list->key_id) ? $each_list->key_id : "--";
            $each_row["kit_name"] = isset($each_list->kit_name) ? $each_list->kit_name : "--";
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    /**
     * Get Experience lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('kit_name')->pluck('kit_name', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $uniformKitNameData = ['kit_name' => $data['kit_name'],'customer_id'=> $data['customer_id']];
        $result =  $this->model->create($uniformKitNameData);
        $KitId = ($result->id) ? $result->id : null;
        if (!empty($result)) {
            foreach ($data['item_id'] as $items) {
                $input['kit_id'] = $KitId;
                $input['item_id'] = $items;
                foreach ($data['quantity'] as $quantity) {
                    $input['quantity'] = $quantity;
                }
                $result =  $this->mappingModel->create($input);
            }
        }
        return true;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function update($data)
    {
        $uniformKitNameData = ['kit_name' => $data['kit_name'],'customer_id'=> $data['customer_id']];
        $result =  $this->model->updateOrCreate(array('id' => $data['id']), $uniformKitNameData);
        if (!empty($result)) {
            for ($i = 0; $i < count($data['uniform_kit_id']); $i++) {
                $customerUniformKitMappingData = [
                'kit_id' => $result->id,
                'item_id' => $data['item_id'][$i],
                'quantity' => $data['quantity'][$i],
                ];
                if (isset($data['uniform_kit_id'][$i]) && ($data['uniform_kit_id'][$i] != null)) {
                    $this->mappingModel->where('id', $data['uniform_kit_id'][$i])->update($customerUniformKitMappingData);
                } else {
                    $customerUniformKitMappingResult =  $this->mappingModel->create($customerUniformKitMappingData);
                }
            }
            return response()->json(array('success' => 'true'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $deleteKitName = $this->model->destroy($id);
        if ($deleteKitName) {
            return $this->mappingModel->where('kit_id', $id)->delete();
        }
    }
    

     /**
     * Save uniform details.
     * @param item_measurement=>[{1=>20,2=>30}]
     * @param customer_id=>199
     * @return object
     */

    public function saveUniformDetails($data, $candidateid, $job_id = null)
    {
        $recCandidateUniformSize = array();
        $uniform_arr=array();
        $result= array();
        $saveRecCandidateUniformSize=array();
        $saveRecCandidateUniformCalculation=array();
        $item_measurement_arr=$data['item_measurement'][0];

        $addr=isset($data['item_measurement'][1])?$data['item_measurement'][1]['address']:$data['address'];

        $job=RecJob::find($job_id);
         // $job=RecJob::find(7);
        $this->recCandidateUniformCalculated->where('candidate_id', $candidateid)->delete();
        $uniform_kit_Details=$this->model->where('customer_id', $job->customer_id)->first();
        if ($uniform_kit_Details) {
            $uniform_kit_item=$this->mappingModel->where('kit_id', $uniform_kit_Details->id)->pluck('item_id')->toArray();
        }
        foreach ($item_measurement_arr as $key => $value) {
            if (isset($value)) {
                $result['measurement_id']= $key;
                $result['measurement_value']= number_format((float)$value, 3, '.', '');
                $result['kit_id']= $uniform_kit_Details?$uniform_kit_Details->id:null;
                $result['candidate_id']=$candidateid;
                $result['customer_id']= $job->customer_id;
                $uniformsizeStore =$this->recCandidateUniformSize->updateOrCreate(
                    ['customer_id' => $job->customer_id, 'candidate_id' => $candidateid,'measurement_id'=>$key],
                    $result
                );
            }
        }
        if (isset($uniform_kit_Details)) {
            foreach ($uniform_kit_item as $key => $each_item) {
                $uniform_arr=array();
                $item_measurement_arrs=RecUniformItemSizeMeasurementMapping::where('item_name_id', $each_item)->get();

                foreach ($item_measurement_arrs as $key => $value) {
                   // dd($item_measurement_arr[$value->measurement_name_id]);
                    if (isset($item_measurement_arr[$value->measurement_name_id])) {
                        if ($item_measurement_arr[$value->measurement_name_id]>=$value->min && $item_measurement_arr[$value->measurement_name_id]<=$value->max) {
                            $recCandidateUniformSize['measurement_id']= $value->measurement_name_id;
                            $recCandidateUniformSize['measurement_value']= $item_measurement_arr[ $value->measurement_name_id];
                            $recCandidateUniformSize['kit_id']=$uniform_kit_Details->id;
                            $recCandidateUniformSize['candidate_id']=$candidateid;
                            $recCandidateUniformSize['customer_id']= $job->customer_id;
                            // $saveRecCandidateUniformSize[]=$this->recCandidateUniformSize->create($recCandidateUniformSize);
                            $recCandidateUniformSize['size_name_id']=$value->size_name_id;
                            $uniform_arr[]=$recCandidateUniformSize;
                        }
                    }
                }
                $size_id=$this->sizeofItem($uniform_arr, 'size_name_id');
                if ($size_id) {
                    $recCandidateUniformCalculation['size_id']=$size_id;
                    $recCandidateUniformCalculation['item_id']=$each_item;
                    $recCandidateUniformCalculation['kit_id']=$uniform_kit_Details->id;
                    $recCandidateUniformCalculation['candidate_id']=$candidateid;
                    $saveRecCandidateUniformCalculation[]=$this->recCandidateUniformCalculated->updateOrCreate(
                        ['kit_id' => $uniform_kit_Details->id, 'candidate_id' => $candidateid,'item_id'=>$each_item],
                        $recCandidateUniformCalculation
                    );
                }
            }
        }
            $details['candidate_id'] = $candidateid;
            $details['kit_id'] = isset($uniform_kit_Details)?$uniform_kit_Details->id:null;
            $details['shippment_status'] = 0;
            $details['shippment_address'] = $addr;
         //$details['shippment_address'] = "test";
            $details['status_date_time'] = Carbon::now();
            RecCandidateUniformShippmentDetail::updateOrCreate(
                ['candidate_id' => $candidateid],
                $details
            );
      //  }
         return response()->json(array('recCandidateUniformSizes' => $saveRecCandidateUniformSize,'recCandidateUniformCalculations'=>$saveRecCandidateUniformCalculation));
    }
    private function sizeofItem($arr, $index)
    {
        $lastFrom =null ;
        $max_size_id=null;
        foreach ($arr as $item) {
            if ($item[$index] !== $lastFrom) {
                $max_size_id= $item[$index];
            }
    
            $lastFrom = $item[$index];
            $max_size_id = $item[$index];
        }
         return $max_size_id;
    }

    public function getKitDetails($id, $candidate_id)
    {
        $data= $this->recCandidateUniformCalculated->where('kit_id', $id)->where('candidate_id', $candidate_id)->with('item', 'size', 'kit.customerUniformKitMappings')->get();
        return (array('result' => $data,'id'=>$id,'candidate_id'=>$candidate_id));
    }
}
