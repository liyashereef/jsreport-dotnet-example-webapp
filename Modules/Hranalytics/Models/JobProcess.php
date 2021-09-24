<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobProcess extends Model {

    use SoftDeletes;
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_id',
        'process_id',
        'user_id',
        'process_date',
        'process_note',
        'entered_by_id',
    ];
    
    /**
     * 
     * 
     */
    public function process() {
        return $this->belongsTo('Modules\Admin\Models\JobProcessLookup', 'process_id', 'id');
    }
    
    /**
     * 
     * 
     */
    public function enteredBy() {
        return $this->belongsTo('Modules\Admin\Models\User', 'entered_by_id', 'id')->withTrashed();
    }

}
