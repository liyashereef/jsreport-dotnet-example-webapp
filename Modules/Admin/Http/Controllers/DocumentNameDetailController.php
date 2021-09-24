<?php

namespace Modules\Admin\Http\Controllers;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\DocumentNameRequest;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\DocumentNameDetailRepository;
use Modules\Admin\Models\DocumentType;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Models\AuthorisedAccessDocument;  


class DocumentNameDetailController extends Controller
{
    protected $repository, $helperService;
    
    /**
     * Create Repository instance.
     * @param  \App\Repositories\DocumentNameDetailRepository $repository
     * @return void
     */
    public function __construct(DocumentNameDetailRepository $repository, HelperService $helperService,UserRepository $userRepository)
    {
        $this->repository = $repository;
        $this->helperService = $helperService;
        $this->userRepository = $userRepository;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        $type_list = array_map('ucfirst', DocumentType::orderBy('document_type')->pluck('document_type', 'id')->toArray());
        $documentnameRoles = $this->userRepository->getRoleLookup();
        $authorized_access_list = AuthorisedAccessDocument::orderBy('name','asc')->pluck('name','id');
        return view('admin::masters.document-name',compact('type_list','documentnameRoles','authorized_access_list'));
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
        return response()->json($this->repository->getNames($id));
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getCategoryList($id)
    {
        return response()->json($this->repository->getCategoryDetails($id));
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getOtherCategoryNames($id)
    {
        return response()->json($this->repository->getOtherCategoryNames($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CandidateAssignmentTypeRequest $request
     * @return json
     */
    public function store(DocumentNameRequest $request)
    {
        try {
            \DB::beginTransaction();
            $isvalid = $request->is_valid;
            if($isvalid == null){
                $request->request->add(['is_valid' => null]);
            }
            $isautoarchive= $request->is_auto_archive;
            if($isautoarchive == null){
                $request->request->add(['is_auto_archive' => 0]);
            }
            $type_id = $request->document_type_id;
            $answer_type = $this->repository->getCategoryModels($type_id);
            $request->request->add(['answer_type' => $answer_type]);
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
