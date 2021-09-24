<?php

namespace Modules\Osgc\Repositories;

use Modules\Osgc\Models\OsgcCourse;
use Modules\Osgc\Models\OsgcCoursePrice;
use Modules\Osgc\Models\OsgcCourseContentHeader;
use Modules\Osgc\Models\OsgcCourseContentSection;
use Modules\Osgc\Models\OsgcCourseContent;
use Modules\Osgc\Models\CourseStudyGuide;
use Modules\LearningAndTraining\Models\CourseContentType;
use Auth;
use DB;
use App\Helpers\S3HelperService;
use Illuminate\Support\Facades\Storage;
use Modules\Osgc\Models\TestCourseMaster;
class OsgcCourseLookupRepository {

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model,$courseHeaderModel,$courseSectionModel,$courseContentModel,$coursePriceModel,$helperService;

    /**
     * Create a new IncidentPriorityLookup instance.
     *
     * @param  \App\Models\IncidentPriorityLookup $incidentPriorityLookupModel
     */
    public function __construct(OsgcCourse $osgcCourseModel,OsgcCourseContentHeader $osgcCourseHeaderModel,
    OsgcCourseContentSection $osgcCourseSectionModel,OsgcCourseContent $osgcCourseContentModel,
    OsgcCoursePrice $osgcCoursePriceModel,S3HelperService $s3helperService) {
        $this->model = $osgcCourseModel;
        $this->courseHeaderModel = $osgcCourseHeaderModel;
        $this->courseSectionModel = $osgcCourseSectionModel;
        $this->courseContentModel = $osgcCourseContentModel;
        $this->coursePriceModel = $osgcCoursePriceModel;
        $this->s3helperService = $s3helperService;
        
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll() {
        return $this->model->select(
                                [
                                    'id',
                                    'title',
                                    'active',
                                    
                                ]
                        )->with(['CoursePrice'])
                        ->orderBy('updated_at', 'DESC')
                        ->get();
    }

    /**
     * Display a listing of resources.
     *
     * @param empty
     * @return array
     */
    public function getList() {
        return $this->model->orderBy('title', 'asc')->pluck('title', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id) {
        return $this->model->with(['CourseHeaders','CourseSections','CoursePrice'])->get()->find($id);
    }
    public function getAllCourse() {
        $data= $this->model->select(
                                [
                                    'id',
                                    'title',
                                    'active',
                                    
                                ]
                        )->with(['CoursePrice'])
                        ->orderBy('updated_at', 'DESC')
                        ->get();
        return $this->getCourseArray($data);
    }
    public function getCourseArray($data)
    {
        $datatable_rows = array();
        foreach ($data as $key => $each_record) {   
            $each_row["id"] = $each_record->id;
            $each_row["title"] = $each_record->title;
            $each_row["price"] = '$'.$each_record->CoursePrice->price ?? '';
            if($each_record->active ==1)
            {
                $each_row["active"] ='Active';
            }else{
                $each_row["active"] ='Inactive';
            }
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data) {
        if (empty($data['id'])) {
            $data['created_by'] = Auth::user()->id;
        } else {
            $data['updated_by'] = Auth::user()->id;
        }
        if(isset($data['course_image_name']) && !empty($data['course_image_name']))
        {
            $data['course_image'] =$data['course_image_name'];
        
        }
        $course = $this->model->updateOrCreate(
            [
                'id' => $data['id']
            ],$data);
            $this->coursePriceModel->updateOrCreate(
                [
                    'course_id' => $course->id
                ],['price'=>$data['price']]);

        if (isset($data['row-no'])) {
            
            foreach ($data['row-no'] as $row_no) {
                $headings = $data['heading_' . $row_no];
                $headingId = $data['heading_id_' . $row_no] ?? NULL;
                $sortOrder = intval($data['sort_order_' . $row_no]);
                $status = intval($data['status_' . $row_no]);
                if ($headings != '') {
                    $arr = [
                        'name' => $headings,
                        'course_id' => $course->id,
                        'sort_order' => $sortOrder,
                        'active' => $status,
                    ];
                    
                    $this->courseHeaderModel->updateOrCreate([
                        'id' => $headingId
                    ],$arr);
                }
            }
            
        }
        return $course;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id) {
        return $this->model->destroy($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteCourseHeader($id) {
        $checkHeadingExist= $this->courseSectionModel->where('header_id',$id)->count();
        if($checkHeadingExist == 0)
        {
            $this->courseHeaderModel->destroy($id);
            return true;
        }else{
            return false;
        }
    }
    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getCourseContents($request) {
        $result=$this->courseSectionModel->select(
            [
                'id',
                'name',
                'sort_order',
                'completion_mandatory',
                'header_id',
                'active'
                
            ]
            )
            ->with(['courseContent','courseContent.courseContentType','CourseHeading'])
            ->where('course_id',$request->course_id)
            ->orderBy('created_at', 'DESC')->get();
        return $result;
    }
    public function getAllCourseContents($request) {
        $result=$this->courseSectionModel->select(
            [
                'id',
                'name',
                'sort_order',
                'completion_mandatory',
                'header_id',
                'active'
                
            ]
            )
            ->with(['courseContent','courseContent.courseContentType','CourseHeading'])
            ->where('course_id',$request->course_id)
            ->orderBy('created_at', 'DESC')->get();
        return $this->getCourseSectionArray($result);
    }
    public function getCourseSectionArray($data)
    {
        $datatable_rows = array();
        foreach ($data as $key => $each_record) {   
            $each_row["id"] = $each_record->id;
            $each_row["name"] = $each_record->name;
            $each_row["sort_order"] = $each_record->sort_order;
            $each_row["heading"] = $each_record->CourseHeading->name ?? '';
            $each_row["content_type"] = $each_record->courseContent->courseContentType->type ?? '';
            if($each_record->active ==1)
            {
                $each_row["active"] ='Active';
            }else{
                $each_row["active"] ='Inactive';
            }
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }
    /**
     * Display a listing of resources.
     *
     * @param empty
     * @return array
     */
    public function getCourseHeadingList($courseId) {
        return $this->courseHeaderModel->where('course_id',$courseId)->orderBy('sort_order', 'asc')->pluck('name', 'id')->toArray();
    }
     /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getContentType() {
        return CourseContentType::where('id',3)->get();
    }
    public function saveCourseContents($data)
    {
        
        $arr=array('name'=>$data['name'],
                   'sort_order'=>$data['sort_order'],
                   'course_id'=>$data['course_id'],
                   'header_id'=>$data['header_id'],
                   'active'=>$data['content_status'],
                   'completion_mandatory'=>$data['completion_mandatory'] ?? 0,
                );
        $sections=$this->courseSectionModel->updateOrCreate(
            [
                'id' => $data['id']
            ],$arr);
        $contentArr=array('fast_forward'=>$data['fast_forward'] ?? 0,'content_type_id'=>$data['content_type_id'],'content'=>$data['course_file_name'],'course_content_section_id'=>$sections->id);
        if (empty($data['id'])) {
            $contentArr['created_by'] = Auth::user()->id;
            
        } else {
            $contentArr['updated_by'] = Auth::user()->id;
            
        }
        $contentDetails=$this->getContentBySectionId($sections->id);
        if(isset($contentDetails) && $contentDetails->content !=$data['course_file_name'])
        {
            $prevFile="osgc/video/".$contentDetails->content;
            $disk = \Storage::disk('awsS3Bucket');
            if ($disk->exists($prevFile)) {
                $this->s3helperService->moveFile('awsS3Bucket',$prevFile,"temp/osgc/video/",$contentDetails->content);
            }
        }
        
        $contents=$this->courseContentModel->updateOrCreate(
            [
                'course_content_section_id' => $sections->id
            ],$contentArr);
        
        return $contents;
    }
     /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function getCourseContent($id) {
        return $this->courseSectionModel->with(['courseContent','courseContent.courseContentType','CourseHeading'])->get()->find($id);
    }
    /**
     * To upload a file
     *
     * @param [type] $data
     * @param [type] $model
     * @return void
     */
    public function uploadFile($data, $courseArr)
    {
        ini_set('max_execution_time', 30000);
        $courseDet=$this->model->get()->find($data['course_id']);
        $fileName = $this->helperService->sanitiseString($courseDet['title'].'-' .$data['name']) .'-'. time() . '.' . $data['course_content']->getClientOriginalExtension();
        $file = $data['course_content'];
        if ($data['content_type_id']==1) {
            $filePath = 'osgc/images/' . $fileName;
        }
        if ($data['content_type_id']==2) {
            $filePath = 'osgc/pdf/' . $fileName;
        }
        if ($data['content_type_id']==3) {
            $filePath = 'osgc/video/' . $fileName;
            
        }
        Storage::disk('s3')->put($filePath, file_get_contents($file));
        //$data['course_file']->move(public_path('course_files'), $fileName);

        $this->courseContentModel->where('id',$courseArr->id)->update(['content' => $fileName]);
        return $this->model;
    }
    public function getSectionList($courseId) {
        return $this->courseSectionModel->where('course_id',$courseId)->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    }
    public function getsectionListByHeader($request) {
        $sectionIds=TestCourseMaster::where('course_id',$request->course_id);
        if($request->id !=0)
        {
            $sectionIds=$sectionIds->where('id','!=',$request->id);
        }
        $sectionIds=$sectionIds->pluck('osgc_course_section_id');
        return $this->courseSectionModel->whereNotIn('id',$sectionIds)->where('header_id',$request->header_id)->orderBy('name', 'asc')->get();
    }
    public function saveStudyGuide($request)
    {
        $data=$request->all();
        $contentDetails=$this->getStudyGuide($request);
        if(isset($contentDetails) && $contentDetails->file_name !=$data['course_doc_name'])
        {
            $prevFile="osgc/pdf/".$contentDetails->file_name;
            $disk = \Storage::disk('awsS3Bucket');
            if ($disk->exists($prevFile)) {
                $this->s3helperService->moveFile('awsS3Bucket',$prevFile,"temp/osgc/pdf/",$contentDetails->file_name);
            }
        }
        $arr=array('course_section_id'=>$data['course_section_id'],'file_name'=>$data['course_doc_name']);
        CourseStudyGuide::updateOrCreate(['course_section_id'=>$data['course_section_id']],$arr);
        return true;

    }
    public function getStudyGuide($request)
    {
        return CourseStudyGuide::where('course_section_id',$request->course_section_id)->first();
        
    }
    public function updateCourseStatus($id,$flag)
    {
        if($flag ==1)
        {
            $this->model->where('id','!=',$id)->update(['active'=>0]);
        }
        $this->model->where('id',$id)->update(['active'=>$flag]);
    }
    public function checkCourseStatus($id)
    {
        $activeHeaderIds=$this->courseHeaderModel->where('course_id',$id)->where('active',1)->pluck('id');
        $activeHeader=count($activeHeaderIds);
        $headerContents=$this->courseSectionModel->where('course_id',$id)->where('active',1)->whereIn('header_id',$activeHeaderIds)->groupBy('header_id')->pluck('header_id');
        
        if($activeHeader == count($headerContents) && $activeHeader !=0)
        {
            $this->updateCourseStatus($id,1);
            return true;
        }else{
            return false;
        }
        
       
            
    }
    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function getContentBySectionId($sectionId) {
        return $this->courseContentModel->where('course_content_section_id',$sectionId)->first();
    }
    
}
