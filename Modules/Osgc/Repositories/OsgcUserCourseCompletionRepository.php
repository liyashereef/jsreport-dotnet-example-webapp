<?php

namespace Modules\Osgc\Repositories;


use Modules\Osgc\Models\UserCourseCompletion;
use Modules\Osgc\Models\OsgcCourseContentSection;
use Modules\Osgc\Models\OsgcCourseContentHeader;
use Modules\Osgc\Models\AllocatedUserCourses;
use App\Services\HelperService;
class OsgcUserCourseCompletionRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $helperService;
     /**
     * Create a new OsgcCourseLookupRepository instance.
     *
     * @param  \App\Models\OsgcCourse $trainingCourse
     */
    public function __construct(UserCourseCompletion $model,
    HelperService $helperService)
    {

        $this->model = $model;
        $this->helperService = $helperService;
       
    }
    /**
     * Get data by id
     *
     * @param id
     * @return object
     */
    public function getAlreadyExist($sectionId)
    {
        return $this->model->where('course_section_id',$sectionId)->where('user_id',\Auth::guard('osgcuser')->user()->id)->count();
    }
    public function saveUserCourseCompletionStatus($sectionId)
    {   
        $secDetails=OsgcCourseContentSection::find($sectionId);
        $arr=array(
        'course_section_id'=>$sectionId,
        'course_header_id'=>$secDetails->header_id,
        'user_id'=> \Auth::guard('osgcuser')->user()->id,
        'content_started'=>1,
        );
        $details=$this->model->updateOrCreate($arr,$arr);
        return $details;
    }
   
    public function updateUserCourseCompletionStatus($sectionId,$updateArr,$courseId=0)
    {
        $checkRowExist=$this->model->where('course_section_id',$sectionId)->where('status',1)->where('user_id', \Auth::guard('osgcuser')->user()->id)->count();
        $result=$this->model->where('course_section_id',$sectionId)->where('status',0)->where('user_id', \Auth::guard('osgcuser')->user()->id)->update($updateArr);
        if($courseId !=0 && $checkRowExist ==0)
        {
            // $checkCompletionStatus=$this->checkUserCourseCompletion($courseId,\Auth::guard('osgcuser')->user()->id);
            // if($checkCompletionStatus == false)
            // {
                $arr=array('course_section_id'=>$sectionId);
                $checkCourseCompletion=$this->checkAllCourseAndTestCompleted($courseId,\Auth::guard('osgcuser')->user()->id);
                if($checkCourseCompletion ==true)
                {
                    $arr['status']=1;
                    $arr['completed_time']=\Carbon\Carbon::now();
                }
                $this->updateFinalCourseCompletion($courseId,$arr);
            //}
        }
        return $result;
        
    }
    public function checkAllCourseAndTestCompleted($courseId,$userId)
    {
        $totalheaders=OsgcCourseContentHeader::where('course_id',$courseId)->where('active',1)->pluck('id');
        $sectionids=OsgcCourseContentSection::whereIn('header_id',$totalheaders)->where('active',1)
        ->where('completion_mandatory',1)
        ->pluck('id');
        if(count($sectionids) >0)
        {
            $checkCouseCompletion=$this->model->whereIn('course_section_id',$sectionids)
                ->where('user_id',$userId)
                ->where('status',1)->count();//dd($sectionids .'/'.$checkCouseCompletion);
           
        }else{
            $sectionids=OsgcCourseContentSection::whereIn('header_id',$totalheaders)->where('active',1)
            ->pluck('id');
            $checkCouseCompletion=$this->model->whereIn('course_section_id',$sectionids)
                ->where('user_id',$userId)
                ->where('status',1)->count();//dd($sectionids .'/'.$checkCouseCompletion);
        }
        if(count($sectionids) <= $checkCouseCompletion)
        {
            return true;
        }else{
            return false;
        }
        
    }
    public function getCourseCompletionById($sectionId)
    {
        return $this->model->where('course_section_id',$sectionId)->where('user_id',\Auth::guard('osgcuser')->user()->id)->first();
    }
    public function updateFinalCourseCompletion($courseId,$arr)
    {
        $conditionalArr=array('course_id'=>$courseId,'user_id'=> \Auth::guard('osgcuser')->user()->id);
        return AllocatedUserCourses::updateorCreate($conditionalArr,$arr);
    }
    public function checkUserCourseCompletion($courseId,$userId)
    {
       $result=AllocatedUserCourses::where('course_id',$courseId)->where('user_id',$userId)->first();
       if(isset($result->status) && $result->status==1)
       {
           return true;
       }else{
           return false;
       }
    }
    public function getUserAllocatedCourse($courseId,$userId,$completed='')
    {
       $result= AllocatedUserCourses::with('courseSection')->where('course_id',$courseId)->where('user_id',$userId);
       if($completed ==true)
       {
         $result->where('status',1);
       }
       return $result=$result->first();
       
    }
    public function checkAllsectionCompleteUnderHeader($sectionId)
    {
        $sectionDet=OsgcCourseContentSection::where('id',$sectionId)->first();
        $sectionids=OsgcCourseContentSection::where('header_id',$sectionDet->header_id)->where('active',1)
        ->pluck('id');
        $checkCouseCompletion=$this->model->whereIn('course_section_id',$sectionids)
                ->where('user_id',\Auth::guard('osgcuser')->user()->id)
                ->where('status',1)->count();//dd($sectionids .'/'.$checkCouseCompletion);
        if(count($sectionids) <= $checkCouseCompletion)
        {
            return true;
        }else{
            return false;
        }
    }
         /**
     * Display details of user course completion status
     *
     * @param $id
     * @return object
     */
    public function userCourseCompletionPercentage($courseId,$userId) {
        $totalCourseContentIds=OsgcCourseContentSection::where('course_id',$courseId)->where('active',1)->where('completion_mandatory',1)->pluck('id');
        if(count($totalCourseContentIds) ==0)
        {
            $totalCourseContentIds=OsgcCourseContentSection::where('course_id',$courseId)->where('active',1)->pluck('id');
         
        }
        $totalCourseContent=count($totalCourseContentIds);
        $courseWatched=$this->model->whereIn('course_section_id',$totalCourseContentIds)
        ->where('user_id',$userId)
        ->where('status',1)->count();
        if($courseWatched !=0)
        {
            $percentageCompletion=round(($courseWatched/$totalCourseContent)*100);
            if($percentageCompletion >= 100)
            {
                $percentageCompletion=100;
            }
        }else{
            $percentageCompletion=0;
        }
        return $percentageCompletion;
    }
}
