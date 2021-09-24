<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientOnboarding extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'rfp_details_id',
        'percentage_completed',
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

    public function rfp()
    {
        return $this->belongsTo('Modules\Contracts\Models\RfpDetails','rfp_details_id','id')->withTrashed();
    }

    public function section()
    {
        return $this->hasMany('Modules\Contracts\Models\ClientOnboardingSection','client_onboarding_id','id')->orderBy('sort_order');
    }

    public function sectionWithTrashed()
    {
        return section()->withTrashed();
    }

    /**
     * Delete event
     */
    public static function boot()
    {
        parent::boot();
        static::deleting(function($clientOnboarding) { // before delete() method call this
            $clientOnboarding->section()->delete();
        });
    }
}
