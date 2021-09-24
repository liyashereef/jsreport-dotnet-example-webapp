<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\UniformSchedulingCustomQuestionRepository;
use Modules\Admin\Repositories\UniformSchedulingCustomQuestionOptionAllocationRepository;
use Modules\Admin\Repositories\UniformSchedulingCustomQuestionOptionRepository;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\IdsCustomQuestionRequest;

class UniformSchedulingCustomQuestionController extends Controller
{
    protected $repository;
    protected $helperService;
    protected $uniformSchedulingCustomQuestionOptionRepository;
    protected $uniformSchedulingCustomQuestionOptionAllocationRepository;
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(
        UniformSchedulingCustomQuestionRepository $uniformSchedulingCustomQuestionRepository,
        UniformSchedulingCustomQuestionOptionRepository $uniformSchedulingCustomQuestionOptionRepository,
        UniformSchedulingCustomQuestionOptionAllocationRepository $uniformSchedulingCustomQuestionOptionAllocationRepository,
        HelperService $helperService
    )
    {
        $this->repository = $uniformSchedulingCustomQuestionRepository;
        $this->helperService = $helperService;
        $this->customQuestionOptionRepository=$uniformSchedulingCustomQuestionOptionRepository;
         $this->customQuestionOptionAllocationRepository=$uniformSchedulingCustomQuestionOptionAllocationRepository;
    }
    public function index()
    {
        return view('admin::uniform-scheduling.custom-question');
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
            $options = $this->customQuestionOptionRepository->save($request->all(),$questions->id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e->getMessage()));
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
            return response()->json($this->helperService->returnFalseResponse($e->getMessage()));
        }
    }
}
