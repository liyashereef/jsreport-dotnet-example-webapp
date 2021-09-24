<?php

namespace Modules\Admin\Repositories;

use Auth;
use Modules\Admin\Models\RfpCatalogueGroup;

class RfpCatalogueGroupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new RfpCatalogueGroupRepository instance.
     *
     * @param  Modules\Admin\Models\RfpGroup $rfpCatalogueGroup
     */
    public function __construct(RfpCatalogueGroup $rfpCatalogueGroup)
    {
        $this->model = $rfpCatalogueGroup;
    }

     /**
      * Function to get rfp group list
      *
      *  @param empty
      *  @return  array
      */
    public function getList()
    {

        return $this->model->select(['id','group','created_at','updated_at'])->orderBy('group','asc')->get();
    }

    /**
     * Get rfp catalogue group list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('group','asc')->pluck( 'group','id')->toArray();
    }

    /**
     *  Display details of single rfp catalogue group
     *
     *  @param $id
     *  @return  object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     *  Store a newly created rfp catalogue group in storage.
     *
     *  @param $data
     *  @return  array
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
     * Remove the rfp catalogue group from storage.
     *
     * @param  $id
     * @return object
     */
      public function delete($id)
      {
          return $this->model->destroy($id);
      }
}
