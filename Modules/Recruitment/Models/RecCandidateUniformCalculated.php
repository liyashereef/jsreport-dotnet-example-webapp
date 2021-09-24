<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateUniformCalculated extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_uniform_calculated';
    public $timestamps = true;
    protected $fillable = ['candidate_id','kit_id','item_id','size_id'];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    public function item()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecUniformItems', 'item_id', 'id');
    }
    public function size()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecUniformSizes', 'size_id', 'id');
    }
    public function kit()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCustomerUniformKit', 'kit_id', 'id');
    }
}
