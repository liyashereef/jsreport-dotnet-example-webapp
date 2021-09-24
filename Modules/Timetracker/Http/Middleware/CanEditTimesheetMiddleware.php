<?php

namespace Modules\Timetracker\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;

class CanEditTimesheetMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $id = $request->input('employee_shift_payperiod_id');
        $empShiftPayperiod = EmployeeShiftPayperiod::find($id);
        if(is_object($empShiftPayperiod)){
            if($empShiftPayperiod->canEdit()){
                return $next($request);
            }
        }
        return response('Unauthorized',401);
    }
}
