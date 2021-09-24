<?php

namespace Modules\Expense\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Expense\Repositories\ExpenseParentCategoryRepository;
use Modules\Expense\Http\Requests\ExpenseParentCategoryRequest;


class ParentExpenseCategoryController extends Controller
{
    public function __construct(
        ExpenseParentCategoryRepository $expenseParentcategoryRepository,
        HelperService $helperService
        ) {
            $this->repository = $expenseParentcategoryRepository;
            $this->helperService = $helperService;
        }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('expense::admin.expense_parent_category');   
    }

    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('expense::create');
    }

    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }
    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(ExpenseParentCategoryRequest $request)
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
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('expense::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('expense::edit');
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
