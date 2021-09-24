<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\ClientFeedbackLookupRequest;
use Modules\Admin\Repositories\ClientFeedbackLookupRepository;

class ClientFeedbackLookupController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\ClientFeedbackLookupRepository $clientFeedbackLookupRepository
     * @return void
     */
    public function __construct(ClientFeedbackLookupRepository $clientFeedbackLookupRepository, HelperService $helperService)
    {
        $this->repository = $clientFeedbackLookupRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::client.client-feedback');
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
     * @param  App\Http\Requests\ScheduleAssignmentTypeRequest $request
     * @return json
     */
    public function store(ClientFeedbackLookupRequest $request)
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
     * @param  $id
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $details = $this->repository->get($id);
            if ($details->is_editable == 1) {
                $lookup_delete = $this->repository->delete($id);
            } else {
                return response()->json($this->helperService->returnFalseResponse());
            }
            \DB::commit();
            return $this->helperService->returnTrueResponse();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
