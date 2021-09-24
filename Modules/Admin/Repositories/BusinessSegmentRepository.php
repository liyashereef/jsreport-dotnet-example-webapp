<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\BusinessSegment;
use Modules\Admin\Models\TrainingCourse;

class BusinessSegmentRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $trainingCourseModel;

    /**
     * Create a new TrainingCategoryLookupRepository instance.
     *
     * @param  \App\Models\TrainingCategory $trainingCategory
     * @param  \App\Models\TrainingCourse $trainingCourse
     */
    public function __construct(BusinessSegment $businessSegment)
    {
        $this->model = $businessSegment;
        
    }

    public function getSingleBusinessreason($id){
        return BusinessSegment::find($id)->first();
      }

    /**
     * Get training category list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('segmenttitle', 'asc')->select(['id', 'segmenttitle', 'created_at', 'updated_at'])->get();
    }

    /**
     * Get training category list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('segmenttitle', 'asc')->pluck('segmenttitle', 'id')->toArray();
    }

    /**
     * Display details of single training category
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created training category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $data['status']=1;
        $data['createdby']=\Auth::User()->id;
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
      
            return $this->model->destroy($id);
    }
}
