<?php

namespace Modules\Supervisorpanel\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerReportEmailScheduler extends Model
{   
    protected $table = "customer_report_email_scheduler";

    protected $fillable = ['customerid','payperiodid','payperioddate','supervisormail','maildate','sendflag','active'];
}
