<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\CompliancePolicyCategoryRequest;
use Modules\Admin\Repositories\CompliancePolicyCategoryRepository;

class CompliancePolicyCategoryController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CompliancePolicyCategoryRepository $compliancePolicyCategoryRepository
     * @return void
     */
    public function __construct(CompliancePolicyCategoryRepository $compliancePolicyCategoryRepository, HelperService $helperService)
    {
        $this->repository = $compliancePolicyCategoryRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::masters.compliance-policy-category');
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
     * @param  App\Http\Requests\CompliancePolicyCategoryRequest $request
     * @return json
     */
    public function store(CompliancePolicyCategoryRequest $request)
    {
        try {
            \DB::beginTransaction();
            $category = $this->repository->save($request->all());
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
     * @param  Illuminate\Http\Request $request
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $category_delete = $this->repository->delete($id);
            \DB::commit();
            if ($category_delete == false) {
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
