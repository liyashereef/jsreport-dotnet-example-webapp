<?php

namespace Modules\Admin\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ComplianceExpiryAcknowledgementLogs extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'compliance_expiry_acknowledgment_logs';

    protected $fillable = [
        'log_data','created_at','created_by'
    ];


}
