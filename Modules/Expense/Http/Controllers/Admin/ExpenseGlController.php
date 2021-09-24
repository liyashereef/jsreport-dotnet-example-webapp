<?php

namespace Modules\Expense\Http\Controllers\Admin;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Expense\Repositories\ExpenseGlRepository;
use Modules\Expense\Http\Requests\ExpenseGlRequest;


class ExpenseGlController extends Controller
{
    protected $repository, $helperService;

    public function __construct(ExpenseGlRepository $expenseGlRepository, HelperService $helperService)
    {
        $this->expenseGlRepository = $expenseGlRepository;
        $this->helperService = $helperService;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        return view('expense::admin.expense-gl');
    }
    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->expenseGlRepository->getAll())->addIndexColumn()->toJson();
    }
    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */

    public function getSingle($id)
    {
        return response()->json($this->expenseGlRepository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return json
     */
    public function store(ExpenseGlRequest $request)
    {

        try {
            \DB::beginTransaction();
            $lookup = $this->expenseGlRepository->save($request->all());
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
            $lookup_delete = $this->expenseGlRepository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
    public function getCategoryList($id)
    {
        return response()->json($this->expenseGlRepository->getCategoryDetails($id));
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('admin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */


    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('admin::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    { }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
}
