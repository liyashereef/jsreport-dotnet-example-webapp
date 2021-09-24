<?php

namespace Modules\Admin\Repositories;

use DB;
use Modules\Admin\Models\ShiftTiming;

class ScheduleShiftTimingsRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new ShiftTiming instance.
     *
     * @param  \App\Models\ShiftTiming $shiftTiming
     */
    public function __construct(ShiftTiming $shiftTiming)
    {
        $this->model = $shiftTiming;
        $arr_all_shifts = config("globals.array_shift");
    }

    /**
     * Get ShiftTiming list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        $arr_all_shifts = config("globals.array_shift");
        $result = $this->model->select(['id', 'shift_name', 'from', 'to', 'displayable', 'created_at', 'updated_at', 'deleted_at'])->get();
        $shift_array = $all_shift_array = array();
        foreach ($result as $key => $shift) {
            $shift_array[$key]['id'] = $shift->id;
            $shift_name = (str_replace("_", " ", $shift->shift_name));
            $shiftname = (str_word_count($shift_name) == 2) ? ucfirst($shift_name) : ucwords($shift_name);
            $shift_array[$key]['shift_name'] = $shiftname;
            $shift_array[$key]['from'] = $shift->from != null ? $shift->from : '--';
            $shift_array[$key]['to'] = $shift->to != null ? $shift->to : '--';
            $shift_array[$key]['displayable'] = $shift->displayable;
            if (($key = array_search($shiftname, $arr_all_shifts)) !== false) {
                unset($arr_all_shifts[$key]);
            }
        }

        foreach ($arr_all_shifts as $key => $shift) {
            $all_shift_array[$key]['shift_name'] = $shift;
            $all_shift_array[$key]['from'] = '--';
            $all_shift_array[$key]['to'] = '--';
            $all_shift_array[$key]['displayable'] = 0;
        }

        return array_merge($shift_array, $all_shift_array);

    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {

        return $this->model->orderBy('shift_name')->pluck('shift_name', 'id')->toArray();
    }

    public function getShiftTimeFrom()
    {

        return $this->model->orderBy('shift_name')->pluck('from', 'id')->toArray();
    }

    public function getShiftTimeTo()
    {

        return $this->model->orderBy('shift_name')->pluck('to', 'id')->toArray();
    }

    /**
     * Get displayable shift timing lookup list
     *
     * @param empty
     * @return array
     */
    public function getDisplayableLookup()
    {

        return $this->model->orderBy('id')->where('displayable', 1)->pluck('shift_name', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($shift_name)
    {
        $shift = $this->model->where('shift_name', $shift_name)->first();
        if ($shift == null) {
            $shiftname = (str_replace("_", " ", $shift_name));
            $shiftname = (str_word_count($shiftname) == 2) ? ucfirst($shiftname) : ucwords($shiftname);
            $array['shift_name'] = $shiftname;
            $array['from'] = null;
            $array['to'] = null;
        } else {
            $array = $shift->toArray();
        }
        return $array;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        try {
            DB::beginTransaction();
            $data['shift_name'] = strtolower(str_replace(" ", "_", $data['shift_name']));
            $shift_save = $this->model->updateOrCreate(array('shift_name' => $data['shift_name']), $data);
            DB::commit();
            return $shift_save;

        } catch (\Exception $e) {
            DB::rollBack();
        }
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

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function getId($shift_name)
    {
        $shift=str_replace(' ', '_', $shift_name);
        $shift = $this->model->where('shift_name', $shift)->first();
        if ($shift != null) {
            return $shift->id;
        }
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function getName($id)
    {
        $shift = $this->model->where('id', $id)->first();
    
        if ($shift != null) {
            return  str_replace('_', ' ', $shift->shift_name);
        }
    }
}
