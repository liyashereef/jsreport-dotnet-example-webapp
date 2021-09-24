<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiGroupEmployeeAllocation extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'kpi_group_id', 'created_by', 'updated_by'];

    public function group()
    {
        return $this->belongsTo(KpiGroup::class, 'kpi_group_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
