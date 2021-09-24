<?php

namespace Modules\Timetracker\Repositories;

use App\Services\HelperService;
use Illuminate\Database\Eloquent\Model;
use Modules\Timetracker\Models\DispatchCoordinatesIdleSetting;

class DispatchCoordinatesIdleSettingRepository
{
    /**
     * @var DispatchCoordinatesIdleSetting $model
     */
    protected $model;
    protected $helper_service;

    public function __construct(DispatchCoordinatesIdleSetting $model)
    {
        $this->model = $model;
        $this->helper_service = new HelperService();
    }

    /**
     * Get the model by id (primary key)
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Update the existing entity.
     * Current implementation only support update functionality.
     * @param $id
     * @param $data
     * @return Model|null
     */
    public function update($id, $data)
    {
        try {
            \DB::beginTransaction();
            $entity = $this->getById($id);
            $entity->update($data);
            \DB::commit();
            return response()->json(array('success' => 'true', 'data' => $entity));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helper_service->returnFalseResponse($e));
        }

    }

    /**
     * Get all items in the database.
     * @return \Illuminate\Database\Eloquent\Collection|DispatchCoordinatesIdleSetting[]
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Current implementation returning the first item as the result.
     * @return DispatchCoordinatesIdleSetting
     */
    public function get()
    {
        return $this->model->first();
    }


}
