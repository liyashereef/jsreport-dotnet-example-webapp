<?php

namespace Modules\LearningAndTraining\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\LearningAndTraining\Repositories\CourseRepository;

class CourseController extends Controller
{

    protected $courseRepository;
    protected $helperService;

    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->helperService = new HelperService();
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        return view('learningandtraining::course');
    }

    /**
     * Display a listing of the course based on their type.
     * @param $type
     */
    public function courseList($type = null)
    {
        $course = $this->courseRepository->getDatatablevalues($type);
        $data = $this->courseRepository->prepareData($course);
        return datatables()->of($data)->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->courseRepository->get($id));
    }
    /**
     * Store Course Register Details
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function registerCourse(Request $request)
    {
        try {
            \DB::beginTransaction();
            $CourseRegister = $this->courseRepository->register($request);
            \DB::commit();
            if ($CourseRegister == false) {
                return response()->json($this->helperService->returnFalseResponse());
            } else {
                return response()->json($this->helperService->returnTrueResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

}
