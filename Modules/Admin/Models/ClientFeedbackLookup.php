<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientFeedbackLookup extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['feedback', 'short_name', 'is_editable'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

}
