<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\VisitorLogScreeningTemplateCustomerAllocation;

class VisitorLogScreeningTemplateCustomerAllocationRepository
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
     * @param  \Modules\Admin\Models\VisitorLogScreeningTemplateCustomerAllocation $model
     */
    public function __construct(VisitorLogScreeningTemplateCustomerAllocation $model)
    {
        $this->model = $model;
    }

    /**
     * Get VisitorStatus list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->get();
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
     * Store a newly created VisitorStatus in storage.
     *
     * @param  $request
     * @return object
     */
    public function save($data)
    {
        return $this->model->create($data);
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

    /**
     * Get  Visitor screening template details
     *
     * @param $id
     * @return object
     */
    public function getTemplateByCustomerId($inputs)
    {
        return $this->model
        ->with(['VisitorLogScreeningTemplate' => function ($query) {
            $query->select('id','name');
        },'VisitorLogScreeningTemplate.VisitorLogScreeningTemplateQuestion'=> function ($query) {
            $query->select('id','visitor_log_screening_template_id','question','answer');
        }])
        ->where('customer_id',$inputs['customerId'])
        ->whereHas('Customer', function($query){
            return $query->where('visitor_screening_enabled',1);
        })
        ->orderBy('id','DESC')
        ->first();
    }

}

