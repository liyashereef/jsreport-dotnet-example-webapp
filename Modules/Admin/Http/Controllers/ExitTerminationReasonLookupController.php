<?php

namespace Modules\Admin\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Admin\Http\Requests\ExitInterviewTerminationRequest;
use Modules\Admin\Repositories\ExitTerminationReasonLookupRepository;


class ExitTerminationReasonLookupController extends Controller
{

    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\ExitTerminationReasonLookupRepository $repository
     * @return void
     */
    public function __construct(ExitTerminationReasonLookupRepository $repository, HelperService $helperService)
    {
        $this->repository = $repository;
        $this->helperService = $helperService;
    }

       /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::index');
    }

    public function reason()
    {
        return view('admin::masters.exit-terminate-reason');
    }

    public function list()
    {
        
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    public function single($id)
    {
        return response()->json($this->repository->get($id));
    }

    public function save(ExitInterviewTerminationRequest $request)
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
    public function store(Request $request)
    {
    }

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
