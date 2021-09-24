<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\PostOrderTopicRequest;
use Modules\Admin\Repositories\PostOrderTopicRepository;

class PostOrderTopicController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  Modules\Admin\Repositories\PostOrderTopicRepository
     * @return void
     */
    public function __construct(PostOrderTopicRepository $postordertopicRepository, HelperService $helperService)
    {
        $this->repository = $postordertopicRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::contracts.post-order-topics');
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
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\PostOrderTopicRequest $request
     * @return json
     */
    public function store(PostOrderTopicRequest $request)
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
