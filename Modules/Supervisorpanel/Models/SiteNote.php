<?php

namespace Modules\Supervisorpanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteNote extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['subject', 'attendees', 'location', 'notes', 'created_by', 'updated_by'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

    public function siteNoteTask()
    {
        return $this->hasMany('Modules\Supervisorpanel\Models\SiteNoteTask', 'site_notes_id', 'id');
    }

}
