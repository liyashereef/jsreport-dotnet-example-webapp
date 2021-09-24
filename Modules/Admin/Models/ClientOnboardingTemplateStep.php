<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientOnboardingTemplateStep extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['section_id','step','sort_order'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function section()
    {
        return $this->belongsTo(
            'Modules\Admin\Models\ClientOnboardingDefaultSection',
            'section_id', 'id'
        )->orderBy('sort_order');
    }
}
