<?php

namespace Modules\KPI\Services;

use Jenssegers\Mongodb\Eloquent\Builder;

abstract class AbstractKpiJob
{
    /**
     * Custom filter query for DB data updation
     * 
     * @param Builder $q 
     * @param  array $data
     * @return Builder
     */
    public function getDBUpdateFilter(Builder $q, array $data)
    {
        return $q;
    }

    public function getJobOptions()
    {
        return $this->options;
    }
}
