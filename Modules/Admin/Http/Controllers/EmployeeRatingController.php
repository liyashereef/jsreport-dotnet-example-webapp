<?php

namespace Modules\Admin\Http\Controllers;


    
use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\EmployeeRatingPolicyRequest;
use Modules\Admin\Repositories\TrainingLookupRepository;
use Illuminate\Http\Request;
use Modules\Admin\Models\EmployeeRatingPolicies;
use Modules\Admin\Models\EmployeeRatingPolicyAllocation;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\Admin\Repositories\EmployeeRatingLookupRepository;
use Modules\Admin\Repositories\EmployeeRatingPolicyRepository;

class EmployeeRatingController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\EmployeeRatingPolicyRepository $employeeRatingPolicyRepository
     * @return void
     */
    public function __construct(EmployeeRatingPolicyRepository $employeeRatingPolicyRepository, HelperService $helperService)
    {
        $this->repository = $employeeRatingPolicyRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lookups=EmployeeRatingLookup::all()->toArray();
        return view('admin::masters.employee-rating-polices',compact('lookups'));
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
     * @param  App\Http\Requests\TrainingRequest $request
     * @return json
     */
    public function store(EmployeeRatingPolicyRequest $request)
    {
            
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
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
}

    
