<?php

namespace Modules\ClientApp\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\ClientApp\Http\Resources\V1\Customer\CustomerResource;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;

class ValidateCustomerIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->validate([
            'customerId' => 'required|numeric'
        ]);
        $customerId = $request->input('customerId');
        $currentKey = 'clientAppCustomerAllocation' . Auth::user()->id;
        if (Cache::has($currentKey)) {
            $customerIdArr = Cache::get($currentKey);
        } else {
            if (Auth::user()->hasPermissionTo('view_all_customers_clientapp')) {
                $customerIdArr = Customer::all()->pluck('id')->toArray();
            } else {
                $customerIdArr = CustomerEmployeeAllocation::where('user_id', '=', Auth::user()->id)
                    ->pluck('customer_id')->toArray();
            }
            // removed at CustomerEmployeeAllocationRepository -> allocateEmployee
            Cache::add($currentKey, $customerIdArr, now()->addMinutes(3));
        }
        if (in_array($customerId, $customerIdArr)) {
            return $next($request);
        }
        return response('Unauthorized', 401);
    }
}
