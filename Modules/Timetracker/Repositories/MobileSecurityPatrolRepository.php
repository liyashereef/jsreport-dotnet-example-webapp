<?php

namespace Modules\Timetracker\Repositories;

use Modules\Timetracker\Models\MobileSecurityPatrol;
use Modules\Admin\Repositories\CustomerRepository;
use Auth;

class MobileSecurityPatrolRepository
{
    public $CustomerRepository;
    /**
     * Create a new TripRepository instance.
     *
     * @param  Modules\Timetracker\Models\MobileSecurityPatrol
     */
    public function __construct(MobileSecurityPatrol $mobileSecurityPatrol,CustomerRepository $CustomerRepository)
    {
        $this->mobileSecurityPatrol = $mobileSecurityPatrol;
        $this->CustomerRepository = $CustomerRepository;
    }
    /**
     * Save Mobile Security Patrol Notes
     *
     * @param type $request $user
     * @return type object
     */
    public function storeNotes($request, $user)
    {
        $security_patrol = new MobileSecurityPatrol;
        $security_patrol->shift_id = $request->shift_id;
        $security_patrol->customer_id = $request->customer_id;
        $security_patrol->subject_id = $request->subject_id;
        $security_patrol->user_id = $user->id;
        $security_patrol->description = $request->description;
        $security_patrol->save();
        return $security_patrol;

    }

    /***
     *  @description - To List Mobile Patrols
     *
     *  @param empty
     *  @return array
     *
     */
    public function listPatrol($clientId=null)
    {
        $arr_user = [Auth::User()->id];
        $allocatedcustomers = $this->CustomerRepository->getAllAllocatedCustomerId($arr_user);


        if (Auth::user()->hasAnyPermission(['view_all_mobile_security_patrol','admin', 'super_admin'])) {
            $mobile_patrol = $this->mobileSecurityPatrol->orderBy('created_at', 'desc')
            ->with(['customer', 'subject', 'user.employee'])
            ->get();
        } else {
            $mobile_patrol = $this->mobileSecurityPatrol->orderBy('created_at', 'desc')
            ->with(['customer', 'subject', 'user.employee'])
            ->whereIn('customer_id', $allocatedcustomers)
            ->get();
        }

        $mobile_patrol = $mobile_patrol->when($clientId != null, function ($q) use ($clientId) {
            return $q->where('customer_id', $clientId);
        });

        $mobile_security_patrol = [];



        foreach ($mobile_patrol as $key => $patrol) {
            $customerid = $patrol['customer']['id'];
            $mobile_security_patrol[$key]['customer'] = $patrol['customer']['client_name'];
            $mobile_security_patrol[$key]['project_number'] = $patrol['customer']['project_number'];
            $mobile_security_patrol[$key]['employee_number'] = $patrol['user']['employee']['employee_no'];
            $mobile_security_patrol[$key]['created_at'] = $patrol['created_at']->toFormattedDateString();
            $mobile_security_patrol[$key]['created_time'] = date("g:i A", strtotime($patrol['created_at']));
            $mobile_security_patrol[$key]['reported_by'] = $patrol['user']['full_name'];
            $mobile_security_patrol[$key]['subject'] = $patrol['subject']['subject'] ?? '';
            $mobile_security_patrol[$key]['description'] = $patrol['description'];
        }

        return $mobile_security_patrol;
    }

}
