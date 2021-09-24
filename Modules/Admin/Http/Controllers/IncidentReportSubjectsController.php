<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use DB;
use Modules\Admin\Http\Requests\SubjectRequest;
use Modules\Admin\Repositories\IncidentCategoryRepository;
use Modules\Admin\Repositories\IncidentReportSubjectRepository;
use Modules\Admin\Models\CustomerIncidentSubjectAllocation;

class IncidentReportSubjectsController extends Controller
{

    /**
     * The Repository instance.
     *
     * @var \App\Services\HelperService
     * @var \Modules\Admin\Repositories\HolidayRepository;
     */
    protected $helperService;
    protected $incidentReportSubjectRepository;
    protected $incidentCategoryRepository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\HolidayRepository $holidayRepository;
     * @return void
     */
    public function __construct(HelperService $helperService,
     IncidentReportSubjectRepository $incidentReportSubjectRepository,
     IncidentCategoryRepository $incidentCategoryRepository)
    {
        $this->helperService = $helperService;
        $this->incidentReportSubjectRepository = $incidentReportSubjectRepository;
        $this->incidentCategoryRepository = $incidentCategoryRepository;
    }
    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::subjects',[
            'incidentCategories' => $this->incidentCategoryRepository->getAll()
        ]);
    }
    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->incidentReportSubjectRepository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Function to get Subject Lookup
     * @return array
     */
    public function getSubjectLookup($id=null)
    {
        return $this->incidentReportSubjectRepository->getList($id);
    }
    /**
     * Store a newly created holiday in storage.
     *
     * @param  Modules\Admin\Http\Requests\HolidayRequest $request
     * @return Json
     */
    public function store(SubjectRequest $request)
    {
        try {
            DB::beginTransaction();
            $role = $this->incidentReportSubjectRepository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

    /**
     * Show the form for editing the specified holiday.
     *
     * @param  $id
     * @return Json
     */
    public function getSingle($id)
    {
        return response()->json($this->incidentReportSubjectRepository->get($id));
    }

    /**
     * Remove the specified holiday from storage.
     *
     * @param  $id
     * @return Json
     */
    public function destroy($id)
    {

        try {
            DB::beginTransaction();
           /* $records = CustomerIncidentSubjectAllocation::where('subject_id', '=', $id)->count();
             if ($records > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please remove all assigned clients and try again'
                ]);
            }*/
            $subject_delete = $this->incidentReportSubjectRepository->delete($id);
            DB::commit();
             return response()->json(['success' => true, 'message' => 'Subject has been deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong']);
        }

    }
  

}
