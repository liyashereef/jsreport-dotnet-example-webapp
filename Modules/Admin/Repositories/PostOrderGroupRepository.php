<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\PostOrderGroup;
use Illuminate\Support\Facades\Auth;

class PostOrderGroupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new PostOrderGroupRepository instance.
     *
     * @param  Modules\Admin\Models\PostOrderGroup $postOrderGroup
     */
    public function __construct(PostOrderGroup $postOrderGroup)
    {
        $this->model = $postOrderGroup;
    }


    /**
     * Get post order group list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('group','asc')->pluck( 'group','id')->toArray();
    }

    /**
     * Get post order group list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('group','asc')->select(['id', 'group'])->get();
    }    

    /**
     * Display details of single post order group
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created post order group in storage.
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
     * Remove the post order group from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
