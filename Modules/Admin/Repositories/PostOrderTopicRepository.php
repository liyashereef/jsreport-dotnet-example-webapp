<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\PostOrderTopic;
use Illuminate\Support\Facades\Auth;

class PostOrderTopicRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new PostOrderTopicRepository instance.
     *
     * @param  Modules\Admin\Models\PostOrderTopic $postordertopic
     */
    public function __construct(PostOrderTopic $postordertopic)
    {
        $this->model = $postordertopic;
    }


    /**
     * Get post order topic list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('topic','asc')->pluck( 'topic','id')->toArray();
    }

    /**
     * Get post order topic list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('topic','asc')->select(['id', 'topic'])->get();
    }    

    /**
     * Display details of post order topic
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created post order topic in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        if(!isset($data['id'])) {
            $data['created_by'] = Auth::user()->id;
        }
        $data['updated_by'] = Auth::user()->id;
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);        
    }

    /**
     * Remove the specified post order topic from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
