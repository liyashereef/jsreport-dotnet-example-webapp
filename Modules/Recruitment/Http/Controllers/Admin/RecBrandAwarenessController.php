<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Recruitment\Http\Requests\RecBrandAwarenessRequest;
use Modules\Recruitment\Repositories\RecBrandAwarenessRepository;

class RecBrandAwarenessController extends Controller
{

    /**
     * Repository instance.
     * @var \App\Repositories\RecBrandAwarenessRepository
     *
     */
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\RecBrandAwarenessRepository $candidateBrandAwarenessRepository
     * @return void
     */
    public function __construct(RecBrandAwarenessRepository $recBrandAwarenessRepository, HelperService $helperService)
    {
        $this->repository = $recBrandAwarenessRepository;
        $this->helperService = $helperService;
    }

    /**
     *  Load the Request Type Masters Page
     *
     * @return view
     */
    public function index()
    {
        return view('recruitment::masters.brand-awareness');
    }

    /**
     *Get a listing of the Request Type Master for Datatable.
     *
     * @return Json
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function lookupList()
    {
        return $this->repository->getLookupList();
    }

    /**
     * Store a newly created Brand Awareness in storage.
     *
     * @param  App\Http\Requests\CandidateBrandAwarenessRequest $request
     * @return json
     */
    public function store(RecBrandAwarenessRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            //$lookup->save();
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     *Get details of single Brand awareness Master
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
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
