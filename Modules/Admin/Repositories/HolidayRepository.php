<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Holiday;
use Modules\Admin\Models\StatHolidays;

class HolidayRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model,$statmodel;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\Holiday $holidayModel
     */
    public function __construct(Holiday $holidayModel,StatHolidays $statmodel)
    {
        $this->model = $holidayModel;
        $this->statmodel = $statmodel;

    }

    /**
     * Get  Holiday list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'year', 'holiday', 'description', 'created_at', 'updated_at'])->whereActive(true)->orderBy('description','asc')->get();
    }


    /**
     * Get  Holiday list
     *
     * @param empty
     * @return array
     */
    public function getAllStatHolidays()
    {
        return $this->statmodel->select(['id', 'holiday'])->whereActive(true)->orderBy('id','asc')->get();
    }
    /**
     * Get single Holiday details
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created Holiday in storage.
     *
     * @param  $request
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

    /**
     * Store a newly created Holiday in storage.
     *
     * @param  $request
     * @return object
     */
    public function statsave($data)
    {
        
        return $this->statmodel->updateOrCreate(['id' => $data['statid']], ["holiday"=>$data["statholiday"]]);
    }
    /**
     * Remove the specified Holiday from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteHoliday($id)
    {

        $holiday = $this->model->find($id);
        $holiday->active = 0;
        $holiday->save();
        return $this->model->destroy($id);
    }

    /**
     * Remove the specified Holiday from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteStatHoliday($id)
    {

        $holiday = $this->statmodel->find($id);
        $holiday->active = 0;
        $holiday->save();
        return $this->statmodel->destroy($id);
    }
        /**
     * Get holiday list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('holiday', 'asc')->pluck('holiday', 'id')->toArray();
    }

}
