<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\RfpAwardDateLookupRepository;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\RfpAwardDateLookupRequest;
class RfpAwardDateLookupController extends Controller
{
    protected $repository;
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(RfpAwardDateLookupRepository $rfpAwarddateLookuprepository, HelperService $helperService)
    {
        $this->repository = $rfpAwarddateLookuprepository;
        $this->helperService = $helperService;
    }
    public function index()
    {
        return view('admin::masters.rfp-award-date');
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
        return view('admin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(RfpAwardDateLookupRequest $request)
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

    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
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
    public function destroy()
    {
    }
}
