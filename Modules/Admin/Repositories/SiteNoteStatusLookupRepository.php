<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\SiteNoteStatusLookup;

class SiteNoteStatusLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new SiteNoteStatusLookup instance.
     *
     * @param  \App\Models\SiteNoteStatusLookup $siteNoteStatusLookup
     */
    public function __construct(SiteNoteStatusLookup $siteNoteStatusLookup)
    {
        $this->model = $siteNoteStatusLookup;
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'status', 'order_sequence', 'created_at', 'updated_at'])->orderBy('order_sequence')->get();
    }

    /**
     * Display details of single Request Type
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getLookupList()
    {
        return $this->model->orderBy('order_sequence')->pluck('status', 'id')->toArray();
    }

    /**
     * Store a newly created Request Type in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $lookup = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        return $lookup;
    }

    /**
     * Remove the specified Request Type from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

}
