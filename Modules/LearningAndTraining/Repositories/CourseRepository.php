<?php

namespace Modules\LearningAndTraining\Repositories;

use Modules\Admin\Models\TrainingProfileCourse;
use Modules\Admin\Models\TrainingProfileRole;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\LearningAndTraining\Models\RegisterCourse;

class CourseRepository
{

    protected $trainingProfileRole, $customer_employee_allocation_repository;

    /**
     * Create a new TrainingProfileRole instance.
     *
     * @param  Modules\Admin\Models\TrainingProfileRole $trainingProfileRole
     */
    public function __construct(TrainingProfileRole $trainingProfileRole, CustomerEmployeeAllocationRepository $customer_employee_allocation_repository, TrainingProfileCourse $trainingProfileCourses)
    {
        $this->trainingProfileRole = $trainingProfileRole;
        $this->trainingProfileCourses = $trainingProfileCourses;
        $this->customer_employee_allocation_repository = $customer_employee_allocation_repository;
    }

    /**
     * Return values for datatable
     *
     * @param $type
     */

    public function getDatatablevalues($type)
    {
        $role_id = \Auth::user()->roles[0]->id;
        $customer_list = $this->customer_employee_allocation_repository->getAllocatedCustomers(\Auth::user());
        $query_result = TrainingProfileCourse::with('training_profile', 'training_course', 'training_course.training_category', 'training_siteprofile.training_profile_site_course.training_siteprofile')->whereHas('training_course')->
            whereHas('training_profile.training_profile_role', function ($query) use ($role_id, $type) {
            $query->where('role_id', '=', $role_id)
                ->where('course_type', '=', $type)
                ->where('profile_type', '=', 'employee');
        })
            ->orwhereHas('training_siteprofile', function ($query) use ($customer_list, $type, $role_id) {
                $query->wherein('customer_id', $customer_list)
                    ->where('course_type', '=', $type)
                    ->where('profile_type', '=', 'Site')
                    ->whereHas('training_profile_site_course.training_profile.training_profile_role', function ($query) use ($role_id) {
                        $query->where('role_id', '=', $role_id);
                    });
            })
            ->get();

        return $query_result;
    }

    /**
     * Return datatable values as array
     *
     * @param empty
     */
    public function prepareData($course)
    {

        $datatable_rows = array();
        foreach ($course as $key => $each_course) {
            $each_row["id"] = $each_course->id;
            $each_row["profile_id"] = $each_course->training_profile_id;
            $each_row["course_id"] = $each_course->course_id;
            if ($each_course->profile_type == 'employee') {
                $each_row["profile_name"] = (null != $each_course->training_profile) ? $each_course->training_profile->profile_name : '--';
                $each_row["profile_type"] = 'Employee';
            } else {
                $each_row["profile_name"] = (null != $each_course->training_siteprofile) ? $each_course->training_siteprofile->profile_name : '--';

                $each_row["profile_type"] = 'Site';
            }
            $each_row["course_name"] = $each_course->training_course->course_title;
            $each_row["reference_code"] = $each_course->training_course->reference_code;
            $each_row["course_description"] = $each_course->training_course->course_description;
            $each_row["course_category"] = $each_course->training_course->training_category->course_category;
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    /**
     * Display details of single training course
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        $data['data'] = $this->trainingProfileCourses->with('training_course')->find($id);
        $data['user'] = User::with('employee')->where('id', \Auth::user()->id)->first();
        return $data;
    }

    /**
     * Store Details of registercourse
     *
     * @param $id
     * @return object
     */
    public function register($request)
    {
        $data['course_id'] = $request->get('course_id');
        $data['employee_id'] = $request->get('employee_id');
        $data['status'] = "Enrolled";
        $registerCourse = RegisterCourse::updateOrCreate(array('course_id' => $data['course_id'], 'employee_id' => $data['employee_id']), $data);
        return $registerCourse;
    }
}
