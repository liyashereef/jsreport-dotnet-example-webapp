<?php

namespace Modules\Admin\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\IncidentCategoryRepository;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\IncidentReportSubject;
use Modules\Admin\Repositories\IncidentReportSubjectRepository;

class IncidentCategoryController extends Controller
{
    protected $incidentCategoryRepository;
    protected $helperService;

    public function __construct(
        IncidentCategoryRepository $incidentCategoryRepository,
        HelperService $helperService
    ) {
        $this->incidentCategoryRepository = $incidentCategoryRepository;
        $this->helperService = $helperService;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::incident-categories');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('admin::incident-categories');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:incident_categories,name,'. $request->id . ',id,deleted_at,NULL'
        ], [
            'name.required' => 'Category is required',
            'name.unique' => 'Category already exists.'
        ]);

        try {
            DB::beginTransaction();
            $indicentCategory = $this->incidentCategoryRepository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        return response()->json($this->incidentCategoryRepository->get($id));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('admin::incident-categories');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    { }

    /**
     * List incident categories.
     */
    public function list()
    {
        return datatables()->of($this->incidentCategoryRepository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $records = IncidentReportSubject::where('incident_category_id', '=', $id)->count();
            if ($records > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please remove all assigned category and try again'
                ]);
            }
            $this->incidentCategoryRepository->delete($id);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Category removed successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong']);
        }
    }
}
