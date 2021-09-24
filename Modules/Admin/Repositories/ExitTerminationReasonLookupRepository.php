<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\ExitTerminationReasonLookup;

class ExitTerminationReasonLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new ExitTerminationReasonLookup instance.
     *
     * @param  \App\Models\ExitTerminationReasonLookup $positionLookup
     */
    public function __construct(ExitTerminationReasonLookup $model)
    {
        $this->model = $model;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'reason','shortname','created_at','updated_at'])->get();
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function save($data)
    {
       
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }
 }
