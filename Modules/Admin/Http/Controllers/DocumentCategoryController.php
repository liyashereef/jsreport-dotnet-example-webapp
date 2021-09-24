<?php

namespace Modules\Admin\Http\Controllers;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\DocumentCategoryRequest;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\DocumentCategoryRepository;
use Modules\Admin\Models\DocumentType;

class DocumentCategoryController extends Controller
{
    protected $repository, $helperService;
    
    /**
     * Create Repository instance.
     * @param  \App\Repositories\DocumentNameDetailRepository $repository
     * @return void
     */
    public function __construct(DocumentCategoryRepository $repository, HelperService $helperService)
    {
        $this->repository = $repository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    
    public function index()
    {

        $type_list = array_map('ucfirst', DocumentType::orderBy('document_type')->where('id','!=',OTHER)->pluck('document_type', 'id')->toArray());
        return view('admin::masters.document-category',compact('type_list'));
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
     * @param  App\Http\Requests\CandidateAssignmentTypeRequest $request
     * @return json
     */
    public function store(DocumentCategoryRequest $request)
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
