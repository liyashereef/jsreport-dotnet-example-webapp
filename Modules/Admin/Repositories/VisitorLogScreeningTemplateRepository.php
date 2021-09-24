<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\VisitorLogScreeningTemplate;
use Modules\Admin\Models\VisitorLogScreeningTemplateCustomerAllocation;

class VisitorLogScreeningTemplateRepository
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
     * @param  \Modules\Admin\Models\VisitorLogScreeningTemplate $model
     */
    public function __construct(VisitorLogScreeningTemplate $model)
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
        return $this->model
        ->with(['VisitorLogScreeningTemplateCustomerAllocation' => function ($query) {
            $query->select('id','visitor_log_screening_template_id','customer_id');
        },'VisitorLogScreeningTemplateCustomerAllocation.Customer'=> function ($query) {
            $query->select('id','project_number','client_name');
        }])
        ->get();
    }

      /**
     * Get single VisitorStatus details
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        $result = $this->model->with(['VisitorLogScreeningTemplateCustomerAllocation' => function ($query) {
            $query->select('id','visitor_log_screening_template_id','customer_id');
        },'VisitorLogScreeningTemplateCustomerAllocation.Customer'=> function ($query) {
            $query->select('id','project_number','client_name');
        }])->find($id);
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

}
