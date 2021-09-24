<?php


namespace Modules\Admin\Repositories;

use Modules\Admin\Models\TemplateSetting;
use Modules\Admin\Models\TemplateSettingRules;

class TemplateSettingRepository
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
     * @param  \App\Models\TemplateSetting $templateSetting
     */
    public function __construct(TemplateSetting $templateSetting)
    {
        $this->model = $templateSetting;

    }

    public function getAvgColor($avg_score)
    {
             $colorObj = TemplateSettingRules::with('color')
                ->where('min_value', '<=', $avg_score)
                ->where('max_value', '>=', $avg_score)
                ->first();
        return $colorObj;
    }


}