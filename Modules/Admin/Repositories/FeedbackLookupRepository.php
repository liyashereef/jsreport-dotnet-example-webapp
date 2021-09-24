<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\FeedbackLookup;

class FeedbackLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new FeedbackLookupRepository instance.
     *
     * @param  \App\Models\FeedbackLookup $feedbackLookup
     */
    public function __construct(FeedbackLookup $feedbackLookup)
    {
        $this->model = $feedbackLookup;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'feedback', 'created_at', 'updated_at'])->get();
    }

    /**
     * Get Position lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        $result = $this->model
            ->orderBy(\DB::raw("FIELD(feedback,'Poor','Average','Below','Good','Excellent')"))        
            ->pluck('feedback', 'id')
            ->toArray();
        return $result;
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
