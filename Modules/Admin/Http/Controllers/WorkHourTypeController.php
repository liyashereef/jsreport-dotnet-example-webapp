<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Repositories\WorkHourTypeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Repositories\WorkHourCustomerRepository;
use Modules\Timetracker\Models\EmployeeShiftWorkHourType;

class WorkHourTypeController extends Controller
{
    protected $repository;
    protected $helperService;
    protected $workHourCustomerRepository;

    public function __construct(
        WorkHourTypeRepository $repository,
        HelperService $helperService,
        WorkHourCustomerRepository $workHourCustomerRepository
    ) {
        $this->repository = $repository;
        $this->helperService = $helperService;
        $this->workHourCustomerRepository = $workHourCustomerRepository;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sortOrder = EmployeeShiftWorkHourType::orderby("sort_order", "asc")->get()->pluck("sort_order")->toArray();
        return view('admin::activitycode.work-hour-type', compact("sortOrder"));
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
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PositionRequest $request
     * @return json
     */
    public function store(Request $request)
    {
        $id = $request->input('id');
        $request->validate([
            'name' => 'required|unique:employee_shift_work_hour_types,name,' . $id . ',id,deleted_at,NULL',
            'description' => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            if ($id) {
                $data = [
                    "id" => $id,
                    "name" => $request->name,
                    "description" => $request->description,
                    "sort_order" => $request->sort_order,
                    "updated_by" => \Auth::user()->id

                ];
            } else {
                $sortData =
                    EmployeeShiftWorkHourType::orderBy("sort_order", "desc")->first();
                $data = [
                    "id" => null,
                    "name" => $request->name,
                    "description" => $request->description,
                    "sort_order" => $sortData->sort_order + 1,
                    "created_by" => \Auth::user()->id

                ];
            }
            $this->repository->save($data);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return json
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            //Check allocation
            if ($this->workHourCustomerRepository->workTypeAllocationCheck($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Activity type allocation exists. Please unallocate and try again'
                ]);
            }

            $this->repository->delete($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
