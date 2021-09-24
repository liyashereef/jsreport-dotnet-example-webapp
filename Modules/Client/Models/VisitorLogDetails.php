<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLogDetails extends Model
{

    public $timestamps = true;

    protected $fillable = [
        'customer_id', 'template_id', 'first_name', 'last_name', 'phone', 'email',
        'checkin', 'checkout', 'visitor_type_id', 'name_of_company', 'whom_to_visit', 'notes', 'picture_file_name',
        'signature_file_name', 'created_by', 'license_number', 'uuid', 'force_checkout', 'vehicle_reference',
        'work_location', 'additional_comments', 'check_in_option','uid','visitor_log_screening_submission_uid','payload'
    ];


    /**
     * Type relation
     */
    public function type()
    {
        return $this->belongsTo('Modules\Admin\Models\VisitorLogTypeLookup', 'visitor_type_id', 'id');
    }
    /**
     * Customer relation
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    public function visitor()
    {
        return $this->hasOne(Visitor::class, 'uid', 'uid')->withTrashed();
    }

    public function metas()
    {
        return $this->hasMany(VisitorLogMeta::class, 'visitor_log_id', 'id');
    }

    public function getQrAddedAttribute()
    {
        $visitor = $this->visitor;
        if ($visitor == null) {
            return '--';
        }
        if ($visitor->barCode == null || empty($visitor->barCode)) {
            return 'No';
        }
        return 'Yes';
    }
}
