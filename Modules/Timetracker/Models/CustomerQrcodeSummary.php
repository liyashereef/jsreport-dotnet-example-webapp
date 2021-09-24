<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerQrcodeSummary extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = ['total_count', 'expected_attempts', 'missed_count_percentage', 'qrcode_id', 'shift_id'];

    public function shifts()
    {

        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShift', 'shift_id', 'id');
    }

    public function qrcode()
    {
        return $this->belongsTo('Modules\Admin\Models\CustomerQrcodeLocation', 'qrcode_id', 'id');
    }
    public function qrcodeWithTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\CustomerQrcodeLocation', 'qrcode_id', 'id')->withTrashed();
    }

    /**
     * Get all of the comments for the CustomerQrcodeSummary
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qrcodeshiftWithTrashed()
    {
        return $this->hasMany('Modules\Timetracker\Models\CustomerQrcodeWithShift', 'qrcode_id', 'qrcode_id')->orderBy("time", "asc");
    }


    public function qrcodeshiftdescWithTrashed()
    {
        return $this->hasMany('Modules\Timetracker\Models\CustomerQrcodeWithShift', 'qrcode_id', 'qrcode_id')->orderBy("time", "desc");
    }

    public function getCheckpointAttribute()
    {
        return ($this->qrcodeWithTrashed->location) ?? '--';
    }

    public function getEmployeeDetailsAttribute()
    {
        return ($this->shifts->shift_payperiod->trashed_user->name_with_emp_no) ?? "--";
    }

    public function getClientNameAttribute()
    {
        return ($this->shifts->shift_payperiod->customer->client_name) ?? "--";
    }
}
