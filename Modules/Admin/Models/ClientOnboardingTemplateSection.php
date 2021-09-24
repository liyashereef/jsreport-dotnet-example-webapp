<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientOnboardingTemplateSection extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['section','sort_order'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function step()
    {
        return $this->hasMany(
            'Modules\Admin\Models\ClientOnboardingTemplateStep',
            'section_id', 'id'
        )->orderBy('sort_order');
    }

    /**
     * Delete event
     */
    public static function boot()
    {
        parent::boot();
        static::deleting(function($section) { // before delete() method call this
            $section->step()->delete();
        });
    }

    public function getAllStepsAttribute() {
        if(is_countable($this->step)) {
            return implode(', ',data_get($this->step,"*.step"));
        }else {
            return $this->step;
        }
    }
}
