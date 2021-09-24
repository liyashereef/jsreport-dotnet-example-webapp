<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecUniformItems;
use Modules\Recruitment\Models\RecUniformItemSizeMeasurementMapping;
use SebastianBergmann\Environment\Console;

class RecUniformItemRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new RecExperienceLookupRepository instance.
     *
     * @param  Modules\Recruitment\Models\RecUniformItems $recUniformItems
     */
    public function __construct(RecUniformItems $recUniformItems)
    {
        $this->model = $recUniformItems;
    }

    /**
     * Get Experience  lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'item_name'])->get();
    }

    /**
     * Get Experience lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('item_name')->pluck('item_name', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    public function saveMapping($data, $id)
    {
        $itemId = $id;
        $measuringPoints = $data['measuring_points'];
        $size = $data['size'];
        $min = $data['min'];
        $max = $data['max'];

        $i = 0;
        RecUniformItemSizeMeasurementMapping::where('item_name_id', $id)->delete();
        foreach ($size as $key => $sizeValue) {
            foreach ($measuringPoints as $k => $measureValue) {
                RecUniformItemSizeMeasurementMapping::updateOrCreate([
                    'item_name_id' => $itemId,
                    'size_name_id' => $sizeValue,
                    'measurement_name_id' => $measureValue,
                    'min' => $min[$i],
                    'max' => $max[$i]
                ]);
                $i++;
            }
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
}
