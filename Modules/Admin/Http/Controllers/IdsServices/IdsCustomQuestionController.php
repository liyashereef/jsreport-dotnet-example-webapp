<?php

namespace Modules\Admin\Http\Controllers\IdsServices;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\IdsCustomQuestionRepository;
use Modules\Admin\Repositories\IdsCustomQuestionOptionAllocationRepository;
use Modules\Admin\Repositories\IdsCustomQuestionOptionRepository;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\IdsCustomQuestionRequest;

class IdsCustomQuestionController extends Controller
{
    protected $repository;
    protected $helperService;
    protected $idsCustomQuestionOptionAllocationRepository;
    protected $idsCustomQuestionOptionRepository;
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(
        IdsCustomQuestionRepository $idsCustomQuestionRepository,
        IdsCustomQuestionOptionRepository $idsCustomQuestionOptionRepository,
        IdsCustomQuestionOptionAllocationRepository $idsCustomQuestionOptionAllocationRepository,
        HelperService $helperService
    )
    {
        $this->repository = $idsCustomQuestionRepository;
        $this->helperService = $helperService;
        $this->idsCustomQuestionOptionRepository=$idsCustomQuestionOptionRepository;
         $this->idsCustomQuestionOptionAllocationRepository=$idsCustomQuestionOptionAllocationRepository;
    }
    public function index()
    {
        return view('admin::ids-scheduling.custom-question');
    }
    public function getList()
    {
        return datatables()->of($this->repository->getDataList())->addIndexColumn()->toJson();
    }
    

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(IdsCustomQuestionRequest $request)
    {  
        try {
            \DB::beginTransaction();
            $questions = $this->repository->save($request->all());
            $options = $this->idsCustomQuestionOptionRepository->save($request->all(),$questions->id);
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
     *
     * @param  Illuminate\Http\Request $request
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $get_question=$this->repository->deleteRelatedRecords($id);
            $question_delete = $this->repository->deleteQuestion($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
