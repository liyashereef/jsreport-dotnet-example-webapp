<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeComplianceReports extends Model
{
    use SoftDeletes;
    protected $fillable = ['report_name', 'display_name', 'active'];

    public function EmployeeMobileDashboard()
    {
        return $this->hasMany("Modules\Admin\Models\EmployeeMobileDashboard", "report_id", "id");
    }
}
