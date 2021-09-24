<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Models\RecScoreCriteria;
use Modules\Recruitment\Http\Requests\RecMatchScoreCriteriaRequest;
use Modules\Recruitment\Repositories\RecMatchScoreCriteriaRepository;
use PhpParser\Node\Expr\FuncCall;

class RecMatchScoreCriteriaController extends Controller
{
   /**
     * Create Repository instance.
     * @param  Modules\Recruitment\Repositories\RecMatchScoreCriteriaRepository $recMatchScoreCriteriaRepository
     * @return void
     */
    public function __construct(
        RecMatchScoreCriteriaRepository $recMatchScoreCriteriaRepository,
        HelperService $helperService
    ) {
        $this->repository = $recMatchScoreCriteriaRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $criteria=$this->repository->getAll()->toArray();
        $match_types = config('globals.match_type');
        $score_lookups=RecScoreCriteria::pluck('criteria_name', 'id')->toArray();
        $score_type=RecScoreCriteria::pluck('type_id', 'id')->toArray();
        return view('recruitment::masters.match-score-criteria', compact('criteria', 'match_types', 'score_lookups', 'score_type'));
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
    public function get($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Recruitment\Http\Requests\RecCompetencyMatrixLookupRequest; $request
     * @return json
     */
    public function store(RecMatchScoreCriteriaRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            dd($e);
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
            $lookup_delete = $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function getScoreCriteria()
    {
        return view('recruitment::masters.score-criteria');
    }

    public Function getScoreCriteriaList()
    {
        return datatables()->of($this->repository->getScoreCriteriaAll())->addIndexColumn()->toJson();
    }
}
