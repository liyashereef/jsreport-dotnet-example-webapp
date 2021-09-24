<?php

namespace Modules\KPI\Services;

use Modules\Admin\Utils\KpiDictionary;
use Modules\KPI\Services\Jobs\ClientSurvayDaily;
use Modules\KPI\Services\Jobs\IncidentComplianceDaily;
use Modules\KPI\Services\Jobs\PerformanceManagementDaily;
use Modules\KPI\Services\Jobs\ScheduleComplianceDaily;
use Modules\KPI\Services\Jobs\SiteMetricDaily;
use Modules\KPI\Services\Jobs\TrainingComplianceDaily;

class KpiDailyJobFactory implements KpiFactoryInterface
{
    public static function create($kpiJobOption)
    {
        $jobId = trim($kpiJobOption->kpi->machine_name);

        switch ($jobId) {
            case KpiDictionary::INCIDENT_COMPLIANCE:
                return new IncidentComplianceDaily($kpiJobOption);
            case KpiDictionary::SITE_METRIC:
                return new SiteMetricDaily($kpiJobOption);
            case KpiDictionary::SCHEDULE_COMPLIANCE:
                return new ScheduleComplianceDaily($kpiJobOption);
            case KpiDictionary::CLIENT_SURVEY:
                return new ClientSurvayDaily($kpiJobOption);
            case KpiDictionary::TRAINING_COMPLIANCE:
                return new TrainingComplianceDaily($kpiJobOption);
            case KpiDictionary::PERFOMANCE_MANAGEMENT:
                return new PerformanceManagementDaily($kpiJobOption);
            default:
                // throw new Exception("Job implementation not found:" . $jobId);
                return null;
        }
    }
}
