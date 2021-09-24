<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IncidentReportSubject;

class IncidentReportSubjectRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CriteriaLookupRepository instance.
     *
     * @param  \App\Models\CriteriaLookup $criteriaLookup
     */
    public function __construct(IncidentReportSubject $subject)
    {
        $this->model = $subject;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->all()->load('incident_category');
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAllBasedOnCustomerId($customer_id)
    {
        return $this->model->with('incident_category')->whereHas('customer_allocation', function ($query) use ($customer_id) {
           $query->where('customer_id', '=', $customer_id); 
    })->get();
    }


    /**
     * Get Position lookup list
     *
     * @param empty
     * @return array
     */
    public function getList($id=null)
    {
      
        return $this->model->orderBy('subject', 'asc')->when(($id!=null), function ($q) use ($id) {
            $q->whereHas('customer_allocation', function ($query) use ($id) {
           $query->where('customer_id', '=', $id); 
    });
        })->pluck('subject', 'id')->toArray();
    }

    public function getSubjectList()
    {
      
        return $this->model->orderBy('subject', 'asc')->pluck('subject', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id)->load('incident_category');
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

     /**
     * Get Position lookup list
     *
     * @param empty
     * @return array
     */
    public function getAllForFM()
    {
        return $this->model->orderBy('id')->select('subject', 'id')->get();
    }
}
