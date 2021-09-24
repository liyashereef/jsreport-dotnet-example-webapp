<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\ClientOnboardingSetting;

class ClientOnboardingSettingRepository
{
    protected $model;


    public function __construct(ClientOnboardingSetting $clientOnboardingSetting)
    {
        $this->model = $clientOnboardingSetting;
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAllByType($type)
    {
        return $this->model->where("settings_type",$type)->orderBy('id')->get();
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
    public function save($data, $type)
    {
        $this->model->where('settings_type',$type)->delete();
        foreach ($data as $key => $eachSetting){
            $dataArr = array(
                "settings_type" => $type,
                "parameter" => ($key+1),
                "value" => $eachSetting
            );
            $this->model->create($dataArr);
        }
        return true;
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
