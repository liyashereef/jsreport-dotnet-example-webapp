<?php

namespace Modules\Hranalytics\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Modules\Admin\Models\Customer;

/**
 * @property mixed customer_id
 * @property mixed customer
 * @property mixed qr_code_id
 * @property mixed qr_code
 * @property mixed user_id
 * @property mixed user
 * @property mixed first_scan_by_user_id
 * @property mixed first_scan_by_user
 * @property mixed last_scan_by_user_id
 * @property mixed last_scan_by_user
 */
class QrPatrolLogs extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'qr_patrol_logs_general';
    protected $fillable = [
        'customer_id',
        'qr_code_id',
        'date',
        'required_scan',
        'actual_scan',
        'compliance_value',
        'first_scan',
        'first_scan_by',
        'last_scan',
        'last_scan_by',
        'date',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];
}
