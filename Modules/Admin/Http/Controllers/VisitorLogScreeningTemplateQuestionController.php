<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Services\HelperService;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Repositories\VisitorLogScreeningTemplateQuestionRepository;
use Modules\Admin\Repositories\VisitorLogScreeningTemplateRepository;

class VisitorLogScreeningTemplateQuestionController extends Controller
{
    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\VisitorLogScreeningTemplateQuestionRepository $visitorLogScreeningTemplateQuestionRepository;
     * @return void
     */
    public function __construct(
        HelperService $helperService,
        VisitorLogScreeningTemplateQuestionRepository $repository,
        VisitorLogScreeningTemplateRepository $templateRepository
        )
    {
        $this->helperService = $helperService;
        $this->repository = $repository;
        $this->templateRepository = $templateRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($id)
    {
        $template = $this->templateRepository->get($id);
        return view('admin::client.visitorlog-screening-template-questions',compact('id','template'));
    }

    /**
     * List all visitor screening template questions in datatable.
     * @return Json
     */
    public function getList($id){
        return datatables()->of($this->repository->getAll($id))->addIndexColumn()->toJson();
    }



    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingle($id){
        return response()->json($this->repository->get($id));
    }

     /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request){
        try {
            \DB::beginTransaction();
            $inputs = $request->all();

            if($request->filled('id')){
                $inputs['updated_by'] = Auth::id();
            }else{
                $inputs['created_by'] = Auth::id();
            }
            $lookup = $this->repository->save($inputs);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the Visitor Status from storage.
     *
     * @param  $id
     * @return Json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            // $this->visitorLogScreeningTemplateCustomerAllocationRepository->deleteByTemplateId($id);
            $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }
}
