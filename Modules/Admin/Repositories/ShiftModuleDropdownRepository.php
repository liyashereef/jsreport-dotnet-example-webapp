<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\ShiftModuleDropdown;

class ShiftModuleDropdownRepository
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
     * @param  \App\Models\WorkType $workTypeModel
     */
    public function __construct(ShiftModuleDropdown $shiftModuleDropdown)
    {
        $this->model = $shiftModuleDropdown;

    }

    /**
     * Get  WorkType list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'dropdown_name','post_order'])->get();
    }

    /**
     * Get single WorkType details
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);

    }

    /**
     * Store a newly created WorkType in storage.
     *
     * @param  $request
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified WorkType from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

}
