<?php

namespace Modules\Osgc\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use App\Helpers\S3HelperService;
use Modules\Osgc\Http\Requests\OsgcCourseRequest;
use Modules\Osgc\Http\Requests\OsgcCourseContentRequest;
use Illuminate\Http\Request;
use Modules\Osgc\Repositories\OsgcCourseLookupRepository;
use Modules\Osgc\Repositories\OsgcUserRepository;
use App\Exports\OsgcUserExport;
use Maatwebsite\Excel\Facades\Excel;
use Config;
class OsgcCourseLookupController extends Controller
{
    protected $repository, $helperService,$s3HelperService;


    /**
     * Create Repository instance.
     * @param  \App\Repositories\OsgcCourseLookupRepository $osgcCourseLookupRepository
     * @return void
     */

    public function __construct(
        OsgcCourseLookupRepository $osgcCourseLookuprepository,
        OsgcUserRepository $osgcUserrepository,
        HelperService $helperService,
        S3HelperService $s3HelperService
    ) {

        $this->repository = $osgcCourseLookuprepository;
        $this->osgcUserrepository = $osgcUserrepository;
        $this->helperService = $helperService;
        $this->s3HelperService = $s3HelperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $uploadDet=$this->s3HelperService->S3PreUpload();
        return view('osgc::admin.course.osgc-course',compact('uploadDet'));
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAllCourse())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingle($id)
    {
        $result=$this->repository->get($id);
        $uploadDet=$this->s3HelperService->S3PreUpload();
        return response()->json([
            'courseDet' => $result,
            'uploadDet' => $uploadDet
        ]);
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
            if ($lookup_delete == true) {
                return response()->json($this->helperService->returnTrueResponse());
            } else {
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
        $uploadDet=$this->s3HelperService->S3PreUpload();
        return view('osgc::admin.course.osgc-course-content',compact('result','contentTypes','headings','courseId','uploadDet'));
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCourseContentList(Request $request)
    {
        return datatables()->of($this->repository->getAllCourseContents($request))->addIndexColumn()->toJson();
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
            // if ($request->hasFile('course_content')) {
            //    $course = $this->repository->uploadFile($request->all(), $course);
            // }
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
        $result=$this->repository->getCourseContent($id);
        $uploadDet=$this->s3HelperService->S3PreUpload();
        return response()->json([
            'courseDet' => $result,
            'uploadDet' => $uploadDet
        ]);
    }
    /**
     * Display section list
     *
     * @param $headerid
     * @return json
     */
    public function getsectionList(Request $request)
    {
        return response()->json($this->repository->getsectionListByHeader($request));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return json
     */
    public function saveStudyGuide(Request $request)
    {
        try {
            \DB::beginTransaction();
            $this->repository->saveStudyGuide($request);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            dd($e);
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
    public function getStudyGuide(Request $request)
    {
        $studyGuideDet=$this->repository->getStudyGuide($request);
        $uploadDet=$this->s3HelperService->S3PreUpload();
        return response()->json([
            'studyGuideDet' => $studyGuideDet,
            'uploadDet' => $uploadDet
        ]);
    }
    /**
     * update course status to active
     *
     * @param  $request
     * @return json
     */
    public function activateCourse($id)
    {

        $details = $this->repository->checkCourseStatus($id, 1); // 1 for status active
        if ($details == false) {
            return response()->json($this->helperService->returnFalseResponse());
        } else {
            return response()->json($this->helperService->returnTrueResponse());
        }
    }
    /**
     * update course status to in Inactive
     *
     * @param  $request
     * @return json
     */
    public function deactivateCourse($id)
    {
        try {
            \DB::beginTransaction();
            $this->repository->updateCourseStatus($id, 0);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
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
    public function userIndex()
    {
        return view('osgc::admin.user.osgc-user');
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserList()
    {
        return datatables()->of($this->osgcUserrepository->getRegisteredUsers())->addIndexColumn()->toJson();
    }

    /**
     * update user status to active
     *
     * @param  $request
     * @return json
     */
    public function activateUser($id)
    {
        $arr=array('active'=>1);
        $details = $this->osgcUserrepository->updateUserStatus($arr,$id); 
        if ($details == false) {
            return response()->json($this->helperService->returnFalseResponse());
        } else {
            return response()->json($this->helperService->returnTrueResponse());
        }
    }
    /**
     * update course status to in active
     *
     * @param  $request
     * @return json
     */
    public function deactivateUser($id)
    {
        $arr=array('active'=>0);
        $details = $this->osgcUserrepository->updateUserStatus($arr,$id); 
        if ($details == false) {
            return response()->json($this->helperService->returnFalseResponse());
        } else {
            return response()->json($this->helperService->returnTrueResponse());
        }
    }
    /**
     * Resetting password
     *
     * @param  $request
     * @return json
     */
    public function resetPassword($email)
    {
        return $this->osgcUserrepository->resetPassword($email); 
        
    }

    /**
     * Export user data 
     * @param  Request $request
     * @return redirect
     */
    public function registeredUserExport(Request $request)
    {
        return Excel::download(new OsgcUserExport, 'Osgc Registered User Export-' . date("Y-m-d H:i A") . '.xlsx');
        //return (new InvoicesExport)->download('invoices.csv', \Maatwebsite\Excel\Excel::CSV);
    }
    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingleUser($id)
    {
        return $this->osgcUserrepository->get($id);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return json
     */
    public function storeUser(Request $request)
    {
        try {
            \DB::beginTransaction();
            $course = $this->osgcUserrepository->update($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
    /**
     * Fetch S3 Details for upload
     *
     * @return \Illuminate\Http\Response
     */
    public function getS3Details()
    {
        $uploadDet=$this->s3HelperService->S3PreUpload();
        return response()->json($uploadDet);
    }
}
