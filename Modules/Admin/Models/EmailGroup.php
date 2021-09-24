<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailGroup extends Model
{
    use SoftDeletes;
    protected $fillable = ["group_name"];

    public function allocation()
    {
        return $this->hasMany('Modules\Admin\Models\EmailGroupAllocation', 'group_id', 'id');
    }
}
