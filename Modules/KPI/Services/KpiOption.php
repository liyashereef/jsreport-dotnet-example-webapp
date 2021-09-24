<?php

namespace Modules\KPI\Services;

use Carbon\Carbon;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\KpiMaster;

class KpiJobOption
{

    public $kpi;
    public $yesterday;
    public $today;
    public $allCustomers;

    public function __construct(KpiMaster $kpi,$arguments = [])
    {
        $this->kpi = $kpi;
        $this->yesterday = Carbon::now()->subDays(1)->format('Y-m-d');
        $this->today = Carbon::now()->format('Y-m-d');
        $this->allCustomers = Customer::all();
        $this->arguments = $arguments;
    }
}
