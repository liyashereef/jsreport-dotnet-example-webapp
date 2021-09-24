<?php

namespace Modules\LearningAndTraining\Repositories;


use Modules\LearningAndTraining\Models\Team;
use Modules\LearningAndTraining\Models\TrainingTeamCourseAllocation;
use Modules\Admin\Models\User;
use Modules\LearningAndTraining\Models\RegisterCourse;
//use Modules\LearningAndTraining\Repositories\TrainingTeamCourseAllocationRepository;
class TeamRepository
{

    protected $team;

    /**
     * Create a new Team instance.
     *
     * @param  Modules\LearningAndTraining\Models\Team $team
     */
    public function __construct()
    {
        $this->team = new Team();
        $this->team_course_allocation = new TrainingTeamCourseAllocation();
        $this->user_team = new TrainingUserTeamRepositories();
        // $this->team_course_allocation = $team_course_allocation;
    }

     /**
     * function to prepare and return data for table
     *
     */
    public function getTableList()
    {
        $team_list = $this->team->orderBy('created_at', 'desc')->get()->load('team','mandatory_course','mandatory_course.training_course','recommended_course','recommended_course.training_course');
        return $this->prepareTableList($team_list);
    }
    
    /**
     * Function to prepare rows for table
     * @param team_list ClientEmployeeFeedback
     */
    public function prepareTableList($team_list)
    {
        $datatable_rows = array();
        $str_mandatory_courses = '';
        $str_recommended_course = '';
        foreach ($team_list as $key => $each_team) {
            $each_row['id'] = $each_team->id;
            $each_row['name'] = $each_team->name;
            $each_row['description'] = $each_team->description;
            $each_row['parent_team'] = '';
            $each_row['mandatory_courses'] = '';
            $each_row['recommended_course'] = '';

            if(isset($each_team->team)){
                $each_row['parent_team'] = $each_team->team->name;
            }
            if(sizeof($each_team->mandatory_course)>=1){
                foreach ($each_team->mandatory_course as $key=>$mandatory_course){
                    $str_mandatory_courses .= $mandatory_course->training_course->course_title.', ';
                }

                $each_row['mandatory_courses'] = rtrim($str_mandatory_courses,', ');
                $str_mandatory_courses = '';
            }

            if(sizeof($each_team->recommended_course)>=1){
                foreach ($each_team->recommended_course as $key1=>$recommended_course){
                    $str_recommended_course .= $recommended_course->training_course->course_title.', ';
                }

                $each_row['recommended_course'] = rtrim($str_recommended_course,', ');
                $str_recommended_course = '';
            }


//            $each_row['date_time'] = $each_team->created_at->toDateTimeString();
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function create($request)
    {
        $data['name'] = $request->get('name');
        $data['description'] = $request->get('description');
        $data['parent_team_id'] = $request->get('parent_team_id');
        $registerTeam = Team::create($data);

        return $registerTeam;
    }

    public function update($request){

        $id = $request->get('team_id');
        $data['name'] = $request->get('name');
        $data['description'] = $request->get('description');
        $data['parent_team_id'] = $request->get('parent_team_id');

        $registerTeam = Team::where('id',$id)->update($data);
        return $registerTeam;
    }

    /**
     * Display details of single training course
     *
     * @param $id
     * @return object
     */
    public function getById($id)
    {
        return $this->team->find($id);

    }

    /**
     * Display list of Parent teams
     *
     * @param
     * @return object
     */
    public function getParentTeams()
    {
        $parent_teams = $this->team->where('parent_team_id',0)
        ->select('id','name')
        ->orderBy('name')
        ->get();
        return $parent_teams;
    }


    /**
     * Display list of Parent teams
     *
     * @param
     * @return object
     */
    public function getSubTeamsByParentId($id)
    {
        $teams = $this->team->where('parent_team_id',$id)->select('id','name')->get();
        return $teams;
    }

    /**
     * Display list of Sub team Ids
     *
     * @param
     * @return object
     */
    public function getSubTeamIdsByParentId($id)
    {
        $teams = $this->team->where('parent_team_id',$id)->pluck('id');
        return $teams;
    }

    /**
     * Remove the specified training course from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($teamIds,$id)
    {
        $team = $this->team->find($id);
        $remove_count = $team->destroy($id);
        if($remove_count){
            $this->user_team->removeByTeamId($id);
            if($team->parent_team_id == 0){

                $this->removeSubTeamByParentId($id);
                $this->deleteSubTeamCourseByParentTeamId($id);

                $this->user_team->deleteByTeamIds($teamIds);
            }
        }
        return $remove_count;

    }

    /**
     * Remove the Sub team by parent_team_id from storage.
     *
     * @param  $id
     * @return object
     */
    public function removeSubTeamByParentId($id){
        return $this->team->where('parent_team_id',$id)->delete();
    }


    /**
     * Remove the Sub team courses by parent_team_id from storage.

     * @param  $id
     * @return object
     */

    public function deleteSubTeamCourseByParentTeamId($id){
        return $this->team_course_allocation->where('parent_team_id',$id)->delete();
    }

    /**
     * Get all Team with its sub team from storage.
     *
        Data Json Format [
        {
        id: 0,
        title: 'choice 1  '
        }, {
            id: 1,
            title: 'choice 2',
                 subs: [
                     {
                         id: 10,
                         title: 'choice 2 1'
                     }, {
                         id: 11,
                         title: 'choice 2 2'
                     }, {
                         id: 12,
                         title: 'choice 2 3'
                     }
                 ]
        }]; *
     * @param  $id
     * @return object
     */

    public function getAllWithSubTeam(){

        return $this->team->orderBy('created_at', 'desc')->where('parent_team_id',0)
            ->select('id','name as title','parent_team_id')
            ->with(array('subs'=>function($query){
            $query->select('id','name as title','parent_team_id');
            }))->get();
    }

    public function getCoursesByTeamId($team_id){
        return $this->team_course_allocation->select('id','course_id','mandatory','recommended')->where('team_id',$team_id)->get();
    }

    /**
     * function to get total active user count
     *
     */
    public function getTotalCount(){
        return $team_list = $this->team->count();
    }

    public function getListByCourse($course_id){
        return $this->team->select('id','name as title','parent_team_id')
            ->whereHas('team_course',function($query) use ($course_id){
                $query->where('course_id',$course_id);
            })->get();
    }




}
