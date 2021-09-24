<?php

namespace Modules\KPI\Services;

use Modules\Admin\Models\KpiMaster;

interface KpiFactoryInterface
{
    /**
     * Create job based on kpi options
     * Retrun instance of KpiJobInterface
     * 
     * @param KpiJobOption $kpidJob
     * @return KpiJobInterface
     */
    public static function create(KpiJobOption $kpidJob);
}
