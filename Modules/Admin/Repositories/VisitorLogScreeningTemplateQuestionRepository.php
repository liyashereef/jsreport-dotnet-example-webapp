<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\VisitorLogScreeningTemplateQuestion;

class VisitorLogScreeningTemplateQuestionRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  \Modules\Admin\Models\VisitorLogScreeningTemplateQuestion $model
     */
    public function __construct(VisitorLogScreeningTemplateQuestion $model)
    {
        $this->model = $model;
    }

    /**
     * Get VisitorStatus list
     *
     * @param empty
     * @return array
     */
    public function getAll($visitor_log_screening_template_id)
    {
        return $this->model->where('visitor_log_screening_template_id',$visitor_log_screening_template_id)->get();
    }

    /**
     * Get single VisitorStatus details
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        $result = $this->model->find($id);
        return $result;
    }

     /**
     * Get single withTrashed
     * @param $id
     * @return object
     */
    public function getIncludingTrashed($id)
    {
        $result = $this->model->withTrashed()->find($id);
        return $result;
    }

    /**
     * Store a newly created VisitorStatus in storage.
     *
     * @param  $request
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified VisitorStatus from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $this->model->where('id',$id)->update(['updated_by'=> \Auth::id()]);
        return $this->model->destroy($id);
    }

    public function deleteByTemplateId($id){
        $this->model->where('visitor_log_screening_template_id',$id)->update(['updated_by'=> \Auth::id()]);
        return $this->model->where('visitor_log_screening_template_id',$id)->delete();
    }

}

