<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientOnboardingSection extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'section',
        'sort_order',
        'percentage_completed',
        'client_onboarding_id',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function createdBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User','created_by','id')->withTrashed();
    }

    public function updatedBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User','updated_by','id')->withTrashed();
    }

    public function clientOnboarding()
    {
        return $this->belongsTo('Modules\Contracts\Models\ClientOnboarding','client_onboarding_id','id');
    }

    public function step()
    {
        return $this->hasMany('Modules\Contracts\Models\ClientOnboardingStep','section_id','id')
            ->orderBy('sort_order');
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
}
