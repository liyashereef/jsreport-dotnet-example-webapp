<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\OsgcCourseRequest;
use Modules\Admin\Http\Requests\OsgcCourseContentRequest;
use Illuminate\Http\Request;
use Modules\Admin\Repositories\OsgcCourseLookupRepository;


class OsgcCourseLookupController extends Controller
{
    protected $repository,$helperService;
   

    /**
     * Create Repository instance.
     * @param  \App\Repositories\OsgcCourseLookupRepository $osgcCourseLookupRepository
     * @return void
     */

    public function __construct(
        OsgcCourseLookupRepository $osgcCourseLookuprepository ,HelperService $helperService
    ) {

        $this->repository = $osgcCourseLookuprepository;
        $this->helperService = $helperService;
        
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::masters.osgc-course');
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return json
     */
    public function store(OsgcCourseRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
    /**
     * Remove the course Heading.
     *
     * @param  $id
     * @return json
     */
    public function deleteCourseHeader($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->repository->deleteCourseHeader($id);
            \DB::commit();
            if($lookup_delete ==true)
            {
                return response()->json($this->helperService->returnTrueResponse());
            }else{
                return response()->json($this->helperService->returnFalseResponse());
            }
            
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
     /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function courseContent($courseId)
    {
        $result=$this->repository->get($courseId);
        $contentTypes=$this->repository->getContentType();
        $headings=$this->repository->getCourseHeadingList($courseId);
        return view('admin::masters.osgc-course-content',compact('result','contentTypes','headings','courseId'));
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCourseContentList(Request $request)
    {
        return datatables()->of($this->repository->getCourseContents($request))->addIndexColumn()->toJson();
    }

        /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return json
     */
    public function saveCourseContents(OsgcCourseContentRequest $request)
    {
        try {
            \DB::beginTransaction();
            $course = $this->repository->saveCourseContents($request->all());
            if ($request->hasFile('course_content')) {
               $course = $this->repository->uploadFile($request->all(), $course);
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            dd($e);
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingleCourseContent($id)
    {
        return response()->json($this->repository->getCourseContent($id));
    }
    
}
