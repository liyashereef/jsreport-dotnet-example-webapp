<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecProcessTab;

class RecProcessTabRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new RecProcessTab instance.
     *
     * @param  \App\Models\RecProcessTab $RecProcessTab
     */
    public function __construct(RecProcessTab $recProcessTab)
    {
        $this->model = $recProcessTab;
    }

    /**
     * Get lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id','system_name','display_name','order','instructions','detailed_help','created_at','updated_at'])->orderBy('order', 'asc')->get();
    }


    public function getInstruction($system_name)
    {
        return $this->model->select(['instructions'])->where('system_name',$system_name)->first();
    }

    public function getInstructionById($id)
    {
        return $this->model->select(['instructions'])->where('id',$id)->first();
    }

    public function getProcessById($id)
    {
        return $this->model->select(['system_name'])->where('id',$id)->first()->system_name;
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
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
