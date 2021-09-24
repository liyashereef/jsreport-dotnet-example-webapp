<?php

namespace Modules\Expense\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Expense\Http\Requests\ExpenseCategoryRequest;
use Modules\Expense\Repositories\ExpenseCategoryLookupRepository;
use Modules\Expense\Repositories\TaxMasterRepository;

class ExpenseCategoryLookupController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @return void
     */
    public function __construct(
        ExpenseCategoryLookupRepository $expenseCategoryLookupRepository,
        HelperService $helperService,
        TaxMasterRepository $taxmasterRepository
    ) {
        $this->repository = $expenseCategoryLookupRepository;
        $this->taxmasterRepository = $taxmasterRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taxes = $this->taxmasterRepository->getList();
        return view('expense::admin.expense_category', compact('taxes'));
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
     * @return json
     */
    public function store(ExpenseCategoryRequest $request)
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
     * @param  Illuminate\Http\Request $request
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
