<?php

namespace Modules\Timetracker\Repositories;

use Modules\Timetracker\Models\MobileSecurityPatrol;
use Modules\Admin\Repositories\CustomerRepository;
use Auth;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Timetracker\Models\MobileSecurityPatrolFenceMeta;

class MobileSecurityPatrolFenceMetaRepository
{
    protected $customerRepository;
    protected $mobileSecurityPatrol;
    /**
     * Create a new TripRepository instance.
     *
     * @param  Modules\Timetracker\Models\MobileSecurityPatrol
     */
    public function __construct(
        MobileSecurityPatrol $mobileSecurityPatrol,
        CustomerRepository $customerRepository,
        MobileSecurityPatrolFenceSummaryRepository $fenceSummary
    ) {
        $this->mobileSecurityPatrol = $mobileSecurityPatrol;
        $this->customerRepository = $customerRepository;
        $this->fenceSummary = $fenceSummary;
    }
    /**
     * Save Mobile Security Patrol Notes
     *
     * @param type $request $user
     * @return type object
     */
    public function store(
        $shift_id,
        $total_visits,
        $missed,
        $average
    )
    {
        $security_patrol_fence_meta = new MobileSecurityPatrolFenceMeta;
        $security_patrol_fence_meta->shift_id = $shift_id;
        $security_patrol_fence_meta->total_visits = $total_visits;
        $security_patrol_fence_meta->missed = $missed;
        $security_patrol_fence_meta->average = $average;
        $security_patrol_fence_meta->save();
        return $security_patrol_fence_meta;

    }

}
