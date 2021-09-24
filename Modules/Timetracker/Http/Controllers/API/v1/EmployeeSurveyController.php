<?php

namespace Modules\Timetracker\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Hranalytics\Repositories\EmployeeSurveyApiRepository;

class EmployeeSurveyController extends Controller
{
    public $successStatus = 200;

    public function __construct(EmployeeSurveyApiRepository $employeeRatingApiRepository)
    {
        $this->employeeRatingApiRepository = $employeeRatingApiRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('timetracker::index');
    }

    public function getEmployeeWiseRatings(Request $request)
    {
        return $this->employeeRatingApiRepository->getEmployeeRatings($request);
    }

    public function getTemplatedetail(Request $request)
    {
        return $this->employeeRatingApiRepository->getTemplatedetail($request);
    }

    public function getSurveyDetails(Request $request)
    {
        return $this->employeeRatingApiRepository->fetchSurveydetails($request);
    }

    public function submitEmployeeSurvey(Request $request)
    {
        try {
            \DB::beginTransaction();
            $result = $this->employeeRatingApiRepository->submitEmployeeSurvey($request);
            \DB::commit();
            $content['data'] = $result;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('timetracker::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('timetracker::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('timetracker::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
