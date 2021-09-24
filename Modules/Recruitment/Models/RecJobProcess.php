<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecJobProcess extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql_rec';
    public $timestamps = true;
    protected $table = 'rec_job_processes';
    public $primaryKey='id';

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
    public function process()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecJobProcessLookup', 'process_id', 'id');
    }
    
    /**
     *
     *
     */
    public function enteredBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'entered_by_id', 'id')->withTrashed();
    }
}
