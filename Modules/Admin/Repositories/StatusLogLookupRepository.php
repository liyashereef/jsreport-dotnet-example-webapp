<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\StatusLogLookup;

class StatusLogLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $statusLogLookupModel;

    /**
     * Create a new StatusLogLookupRepository instance.
     *
     * @param  \App\Models\StatusLogLookup $statusLogLookupModel
     */
    public function __construct(StatusLogLookup $statusLogLookupModel)
    {
        $this->statusLogLookupModel = $statusLogLookupModel;
    }

    /**
     * Get Status Log lookup list
     *
     * @param empty
     * @return array
     */
    public function statusLogLookupList()
    {
        $statusLogLookupList = $this->statusLogLookupModel->orderBy('status', 'ASC')->pluck('status', 'id')->toArray();
        return $statusLogLookupList;
    }

    /**
     * Get score based on the chosen status
     *
     * @param [type] $id
     * @return void
     */
    public function getScore($id)
    {
        return $this->statusLogLookupModel->where('id', '=', $id)->first()->score;
    }

}
