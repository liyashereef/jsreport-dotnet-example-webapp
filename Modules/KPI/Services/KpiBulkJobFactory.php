<?php

namespace Modules\KPI\Services;

use Exception;
use Modules\Admin\Utils\KpiDictionary;
use Modules\KPI\Services\Jobs\ClientSurvayBulk;
use Modules\KPI\Services\Jobs\IncidentComplianceBulk;
use Modules\KPI\Services\Jobs\PerfomanceManagementBulk;
use Modules\KPI\Services\Jobs\ScheduleComplianceBulk;
use Modules\KPI\Services\Jobs\SiteMetricBulk;

class KpiBulkJobFactory implements KpiFactoryInterface
{
    public static function create($kpiJobOption)
    {
        $jobId = trim($kpiJobOption->kpi->machine_name);
        switch ($jobId) {
            case KpiDictionary::INCIDENT_COMPLIANCE:
                return new IncidentComplianceBulk($kpiJobOption);
            case KpiDictionary::SITE_METRIC:
                return new SiteMetricBulk($kpiJobOption);
            case KpiDictionary::CLIENT_SURVEY:
                return new ClientSurvayBulk($kpiJobOption);
            case KpiDictionary::SCHEDULE_COMPLIANCE:
                return new ScheduleComplianceBulk($kpiJobOption);
            case KpiDictionary::PERFOMANCE_MANAGEMENT:
                return new PerfomanceManagementBulk($kpiJobOption);
            default:
                // throw new Exception("Job implementation not found:" . $jobId);
                return null;
        }
    }
}
