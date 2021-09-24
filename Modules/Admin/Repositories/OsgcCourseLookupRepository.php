<?php

namespace Modules\Admin\Repositories;

use Modules\Osgc\Models\OsgcCourse;
use Modules\Osgc\Models\OsgcCoursePrice;
use Modules\Osgc\Models\OsgcCourseContentHeader;
use Modules\Osgc\Models\OsgcCourseContentSection;
use Modules\Osgc\Models\OsgcCourseContent;
use Modules\LearningAndTraining\Models\CourseContentType;
use Auth;
use DB;
use App\Services\HelperService;
use Illuminate\Support\Facades\Storage;
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
    OsgcCoursePrice $osgcCoursePriceModel,HelperService $helperService) {
        $this->model = $osgcCourseModel;
        $this->courseHeaderModel = $osgcCourseHeaderModel;
        $this->courseSectionModel = $osgcCourseSectionModel;
        $this->courseContentModel = $osgcCourseContentModel;
        $this->coursePriceModel = $osgcCoursePriceModel;
        $this->helperService = $helperService;
        
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
                                    
                                ]
                        )
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
        return $this->model->with(['CourseHeaders','CourseSections'])->get()->find($id);
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
        $course = $this->model->updateOrCreate(
            [
                'id' => $data['id']
            ],$data);
        if (isset($data['row-no'])) {
            //dd($data['row-no']);
            foreach ($data['row-no'] as $row_no) {
                $headings = $data['heading_' . $row_no];
                $headingId = $data['heading_id_' . $row_no] ?? NULL;
                $sortOrder = intval($data['sort_order_' . $row_no]);//dd($headings);
                if ($headings != '') {
                    $arr = [
                        'name' => $headings,
                        'course_id' => $course->id,
                        'sort_order' => $sortOrder,
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
                'header_id'
                
            ]
            )
            ->with(['courseContent','courseContent.courseContentType','courseContent.coursePrice','CourseHeading'])
            ->where('course_id',$request->course_id)
            ->orderBy('sort_order', 'ASC')->get();
        return $result;
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
        return CourseContentType::where('active',1)->get();
    }
    public function saveCourseContents($data)
    {
        $priceArr=array();
        $arr=array('name'=>$data['name'],
                   'sort_order'=>$data['sort_order'],
                   'course_id'=>$data['course_id'],
                   'header_id'=>$data['header_id'],
                   'completion_mandatory'=>$data['completion_mandatory'] ?? 0,
                );
        $sections=$this->courseSectionModel->updateOrCreate(
            [
                'id' => $data['id']
            ],$arr);
        $contentArr=array('content_type_id'=>$data['content_type_id'],'course_content_section_id'=>$sections->id);
        if (empty($data['id'])) {
            $contentArr['created_by'] = Auth::user()->id;
            $priceArr['created_by'] = Auth::user()->id;
        } else {
            $contentArr['updated_by'] = Auth::user()->id;
            $priceArr['updated_by'] = Auth::user()->id;
        }
        $contents=$this->courseContentModel->updateOrCreate(
            [
                'course_content_section_id' => $sections->id
            ],$contentArr);
        $priceArr['price']=$data['price'];
        $priceArr['course_content_id'] = $contents->id;
        
        $this->coursePriceModel->updateOrCreate(
            [
                'course_content_id' => $contents->id
            ],$priceArr);
        return $contents;
    }
     /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function getCourseContent($id) {
        return $this->courseSectionModel->with(['courseContent','courseContent.courseContentType','courseContent.coursePrice','CourseHeading'])->get()->find($id);
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


}
