<?php

namespace Modules\LearningAndTraining\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;

class UserCanAccessCourse
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
        $repository = app()->make(TrainingUserCourseAllocationRepository::class);

        //if the course is allocated to the user contintue the execution;
        if($repository->checkCourseAllocation([
            'user_id' => Auth::id(),
            'course_id' => $request->route('id')
        ])){
            return $next($request);
        }

        //redirect back if the course is not allocated.
        abort(401,'Unauthorized');
    }
}
