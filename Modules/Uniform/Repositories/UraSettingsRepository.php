<?php

namespace Modules\Uniform\Repositories;

use Illuminate\Http\Request;
use Modules\Uniform\Models\UraSettings;

class UraSettingsRepository
{
    protected $model;
    public function __construct(UraSettings $uraSettings)
    {
        $this->model = $uraSettings;
    }

    public function store(Request $request)
    {
        $settings = $request->only([
            'uniform-purchase-threshold'
        ]);

        $this->model->truncate();
        foreach ($settings as $k => $v) {
            $this->model->create([
                'key' => $k,
                'value' => $v
            ]);
        }
    }

    public function getAll()
    {
        return $this->model->all()->pluck('value', 'key')->toArray();
    }

    public function getByKey($key, $defaultVal = null)
    {
        $s = $this->getAll();
        return isset($s[$key]) ? $s[$key] : $defaultVal;
    }
}
