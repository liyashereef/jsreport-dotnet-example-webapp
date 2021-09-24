<?php

namespace Modules\Admin\Http\Controllers;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\OtherCategoryNameRequest;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\OtherCategoryNamesRepository;
use Modules\Admin\Models\DocumentType;


class OtherCategoryNameController extends Controller
{
    protected $repository, $helperService;
    
    public function __construct(OtherCategoryNamesRepository $otherCategorynamesRepository, HelperService $helperService)
    {
        $this->otherCategorynamesRepository = $otherCategorynamesRepository;
        $this->helperService = $helperService;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $type_list = array_map('ucfirst', DocumentType::where('document_type','other')->pluck('document_type', 'id')->toArray());
        return view('admin::masters.other-category-name',compact('type_list'));
    }
/**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->otherCategorynamesRepository->getAll())->addIndexColumn()->toJson();
    }
     /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */

    public function getSingle($id)
    {
        return response()->json($this->otherCategorynamesRepository->getNames($id));
    }

/**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CandidateAssignmentTypeRequest $request
     * @return json
     */
    public function store(OtherCategoryNameRequest $request)
    {
        
        try {
            \DB::beginTransaction();
            $lookup = $this->otherCategorynamesRepository->save($request->all());
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
            $lookup_delete = $this->otherCategorynamesRepository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
    public function getCategoryList($id)
    {
        return response()->json($this->otherCategorynamesRepository->getCategoryDetails($id));
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
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
   
}
