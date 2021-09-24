<?php

namespace Modules\Osgc\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Osgc\Models\CoursePayment; 
class CanReadOsgcCourseMiddleware
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
        $checkPayment=CoursePayment::where('user_id',\Auth::guard('osgcuser')->user()->id)->where('status',1);
        if($request->course_id){
            $checkPayment=$checkPayment->where('course_id',$request->course_id);
        }
        $checkPayment=$checkPayment
        ->whereHas("getActiveUser")
        ->count();
        if($checkPayment ==0)
        {
            return abort('403');
        }
        return $next($request);
    }
}
