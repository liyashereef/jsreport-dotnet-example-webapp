<?php

namespace Modules\Admin\Http\Controllers;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\OtherCategoryLookupRepository;
// use Modules\Admin\Http\Requests\EmployeeWhistleblowerCategoryRequest;

class OtherCategoryLookupController extends Controller
{
    protected $repository, $helperService;
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(OtherCategoryLookupRepository $repository,HelperService $helperService)
    {
        $this->repository = $repository;
        $this->helperService = $helperService;
        
    }

    public function index()
    {
        return view('admin::masters.document-category-lookup');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getList(){

        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
        
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
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
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id){

        try{
            \DB::beginTransaction(); 
            $lookup_delete = $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        }catch(\Exception $e){
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }
}
