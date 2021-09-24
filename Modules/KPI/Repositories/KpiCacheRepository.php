<?php

namespace Modules\KPI\Repositories;

use Modules\KPI\Models\KpiCache;

class KpiCacheRepository
{
    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\KpiCustomerHeader $kpiCustomerHeader
     */
    public function __construct(KpiCache $kpiCache)
    {
        $this->model = $kpiCache;
    }

    /**
     * Remove all cache
     */
    public function purge()
    {
        $this->model->truncate();
    }

    /**
     * Set cache
     */
    public function set($key, $valueArr, $queriesArr)
    {
        $this->model->create([
            'key' => $key,
            'value' => json_encode($valueArr,JSON_NUMERIC_CHECK),
            'query' => json_encode($queriesArr),
        ]);
    }

    /**
     * Get has key from input query array
     * input [ key => value ,...]
     */
    public function keyFromQueries($queryArray)
    {
        return md5(json_encode($queryArray));
    }

    /**
     * Check cache exists
     */
    public function hasCache($key)
    {
        $cs = $this->model->where('key', '=', $key)->get();
        return $cs->isEmpty() ? false : true;
    }

    /**
     * Get cache from table
     */
    public function get($key)
    {
        $cs = $this->model->where('key', '=', $key)->get();
        if ($cs->isEmpty()) {
            return null;
        }

        //Get cache and increment hit
        $c = $cs[0];
        $c->incrementHit();

        return $c;
    }
}
