<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\WhistleblowerStatusLookup;

class WhistleblowerStatusLookupsRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;


    public function __construct(WhistleblowerStatusLookup $whistleblowerStatusLookup)
    {
       $this->model = $whistleblowerStatusLookup;
    }

    public function getAll()
    {
        $result= $this->model->select(['id', 'name', 'status'])->get();
        $result_array = array();
        if($result){
            foreach ($result as $key => $data) {
                $each_row['id'] = $data->id;
                $each_row['name'] = $data->name;
                if($data->status==1){
                    $each_row['status'] = "Open";
                }else if($data->status == 2){
                    $each_row['status'] = "In Progress";
                }else if($data->status == 3){
                    $each_row['status'] = "Closed";
                }
                array_push($result_array, $each_row);
            }

        }


        return $result_array;

    }



    /**
     * Display details of single Security Clearance
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created Bank in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the Bank from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $intialStatus = $this->model->where('id',$id)->where('inital_status',1)->first();
        if($intialStatus == true){
            return false;
        }else{
            return $this->model->destroy($id);
        }

    }

    /**
     * Display details of single Security Clearance
     *
     * @param $id
     * @return object
     */
    public function getNamesLookups()
    {
        return $this->model->where('status',1)->get()->pluck('name','id');
    }

    public function getSelectedInitialValue()
    {
        return $this->model->where('inital_status', ACTIVE)->get();
    }

    public function storeIntialStatus($id)
    {
        $intialStatusExists =  $this->model->where('inital_status',true)->first();
        if(isset($intialStatusExists) && !empty($intialStatusExists))
        {
            $this->model->where('id',$intialStatusExists->id)->update(['inital_status' => false]);
        }
        return $this->model->where('id',$id)->update(['inital_status' => true]);

    }


}
