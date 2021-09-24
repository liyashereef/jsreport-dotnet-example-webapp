<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateSetting extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['min_value', 'max_value', 'last_update_limit', 'color_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Relation to color
     *
     * @return type
     */
    public function color()
    {
        return $this->belongsTo('Modules\Admin\Models\Color');
    }

    /**
     * Relation to template settings
     *
     * @return type
     */
    public function templateSettingsRules()
    {
        return $this->hasMany('Modules\Admin\Models\TemplateSettingRules');
    }
}
