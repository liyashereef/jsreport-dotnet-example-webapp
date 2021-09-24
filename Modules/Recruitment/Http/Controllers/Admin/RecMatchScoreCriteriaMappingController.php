<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Http\Requests\RecMatchScoreCriteriaRequest;
use Modules\Recruitment\Repositories\RecMatchScoreCriteriaMappingRepository;

class RecMatchScoreCriteriaMappingController extends Controller
{
   /**
     * Create Repository instance.
     * @param  Modules\Recruitment\Repositories\RecMatchScoreCriteriaRepository $recMatchScoreCriteriaRepository
     * @return void
     */
    public function __construct(
        RecMatchScoreCriteriaMappingRepository $recMatchScoreCriteriaMappingRepository,
        HelperService $helperService
    ) {
        $this->repository = $recMatchScoreCriteriaMappingRepository;
        $this->helperService = $helperService;
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
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCriteria($id)
    {
        
        try {
            \DB::beginTransaction();
            $result =$this->repository->getCriteria($id);
            $tooltip=array();
            if ($id==9) {
                $tooltip= [1=>'1 - Commissionaires is a temporary stop in my career. I have no long term plans.',2=>'2 - I would be interested in exploring a longer term career at Commissionaires.',3=>'3 - I am interested in a long term career with Commissionaires.',4=>'4 - Commissionaires is strategic to my long term career in security.'];
            }
            if ($id==10) {
                $tooltip= [1=>'I have never heard of Commissionaires - but I am familiar with Garda, G4S, Securitas or Palladin',2=>"I am somewhat familiar about Commissionaires, but don't know much about the company",3=>'I am very familiar with Commissionaires and know a lot about the company and what they do'];
            }
            \DB::commit();
            return response()->json(array('result' => $result, 'tooltip' => $tooltip));
           // return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
            dd($e);
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
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
    public function store(Request $request)
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
}
