<?php

namespace Modules\Admin\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Admin\Http\Requests\ExitInterviewResignationRequest;
use Modules\Admin\Repositories\ExitResignationReasonLookupRepository;
// use Modules\Admin\Http\Requests\CandidateTerminationReasonLookupRequest;
use Illuminate\Routing\Controller;

class ExitResignationReasonLookupController extends Controller
{
    protected $repository, $helperService;
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(ExitResignationReasonLookupRepository $repository,HelperService $helperService)
    {
        $this->repository = $repository;
        $this->helperService = $helperService;
        
    }
    public function index()
    {
        return view('admin::masters.exit-resignation-reason');
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
    public function store(ExitInterviewResignationRequest $request)
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
