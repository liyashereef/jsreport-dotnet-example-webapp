<?php

namespace Modules\KPI\Services;

use Jenssegers\Mongodb\Eloquent\Builder;

interface KpiJobInterface
{
    public function __construct(KpiJobOption $option);

    /**
     * Execute job code
     * 
     * @return array
     */
    public function run();

    /**
     * Modify storeToDB update filter
     * @param  Builder $q
     * @param array $data
     * @return Builder
     */
    public function getDbUpdateFilter(Builder $q, array $data);

    /**
     * Get job options of a job
     * 
     * @return KpiJobOption
     */
    public function getJobOptions();
}
