<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'template_name', 'template_description', 'start_date', 'end_date', 'active', 'created_by', 'updated_by',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Relation to form
     *
     * @return type
     */
    public function templateForm()
    {
        return $this->hasMany('Modules\Admin\Models\TemplateForm');
    }

  
    /**
     * Override parent boot and Call deleting event
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($templates) {
            foreach ($templates->templateForm()->get() as $templates) {
                $templates->delete();
            }
        });
    }

}
