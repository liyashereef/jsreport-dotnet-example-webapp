<?php

namespace Modules\Supervisorpanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteNoteTask extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['site_notes_id', 'task_name', 'assigned_to', 'due_date', 'status_id', 'created_by', 'updated_by'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function siteNote()
    {
        return $this->belongsTo('Modules\Supervisorpanel\Models\SiteNote', 'site_notes_id', 'id');
    }

    public function siteNoteStatusLookup()
    {
        return $this->belongsTo('Modules\Admin\Models\SiteNoteStatusLookup', 'status_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'assigned_to', 'id');
    }
}
