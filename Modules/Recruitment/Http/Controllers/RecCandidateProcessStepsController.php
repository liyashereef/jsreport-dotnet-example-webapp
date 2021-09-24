<?php

namespace Modules\Recruitment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Models\RecCandidate;
use Modules\Recruitment\Models\RecCandidateTracking;
use Modules\Recruitment\Http\Requests\RecCandidateCredentialRequest;
use Modules\Recruitment\Repositories\RecCandidateProcessStepsRepository;
use Modules\Recruitment\Http\Requests\RecHrTrackingRequest;

class RecCandidateProcessStepsController extends Controller
{
    protected $repository;
    /**
     * Create Repository instance.
     * @param  Modules\Recruitment\Repositories\RecCandidateProcessStepsRepository $recCandidateProcessStepsRepository
     * @return void
     */
    public function __construct(RecCandidateProcessStepsRepository $recCandidateProcessStepsRepository, HelperService $helperService, RecCandidateTracking $recCandidateTracking)
    {
        $this->repository = $recCandidateProcessStepsRepository;
        $this->recCandidateTracking=$recCandidateTracking;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('recruitment::candidate-process-steps');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function track($id)
    {
        $lookups=$this->repository->getAll();
        $already_processed_track_ids=array();
        $trackings = $this->recCandidateTracking->where(['candidate_id' => $id])->whereHas('tracking_process')->with('enteredBy')->get();
        $users=[\Auth::user()->id=>\Auth::user()->full_name];
        $candidate = RecCandidate::find($id);
        $candidateName = $candidate->name;
        if (isset($trackings)) {
            foreach ($trackings as $each_track) {
                $already_processed_track_ids[$each_track->process_lookups_id] = $each_track;
            }
        }
        $showSave=true;
        if (\Auth::user()->hasPermissionTo('rec-view-allocated-candidates-tracking') && (!\Auth::user()->hasAnyPermission(["super_admin","admin"]))) {
            $showSave=false;
        }
        return view('recruitment::candidate-process-steps-track', compact('lookups', 'already_processed_track_ids', 'id', 'users', 'candidateName', 'showSave'));
    }

     /**
     * Display a listing of the resource.
     * @return Response
     */
    public function saveTracking(RecHrTrackingRequest $request)
    {
        //dd($request->all());
        try {
            \DB::beginTransaction();
            $user = \Auth::user();
            // $candidateJob = $this->candidateJobModel->where('candidate_id', '=', $candidate_id)
            //     ->where('job_id', '=', $job_id);
            // if ($candidateJob->count() > 0) {
               $completion_dates = $request->get('completion_date');
               $completion_time = $request->get('completion_time');
               $notes = $request->get('notes');
               $entered_by_ids = $request->get('entered_by_id');
               $candidate_id = $request->get('candidate_id');
               $process_lookups_id=$request->get('tracking_lookup');
               // $candidateJobRecord = $candidateJob->first();
            if (is_array($completion_dates)) {
                foreach ($completion_dates as $tracking_id => $completion_date) {
                    if (isset($completion_date) || isset($entered_by_ids[$tracking_id]) || isset($notes[$tracking_id])) {
                        if (!isset($completion_date)) {
                            return response()->json(['success' => false, "message" => "The given data was invalid.", "errors" => ["completion_date." . $tracking_id => ["Please select the date"]]], 422);
                        }
                        if (!isset($entered_by_ids[$tracking_id])) {
                            return response()->json(['success' => false, "message" => "The given data was invalid.", "errors" => ["entered_by_id." . $tracking_id => ["Please select the person"]]], 422);
                        }
                            //$data['job_id'] = $job_id;
                            $data['candidate_id'] = $candidate_id;
                            $process_id_lookup=$process_lookups_id[$tracking_id];
                            $data['process_lookups_id']=$process_id_lookup;
                            $data['process_tab_id']=$request->get('process_tab_id');
                            $data['lookup_id'] = $tracking_id;
                            $completion_time = date("H:i:s", strtotime($completion_time[$tracking_id]));
                            $data['completed_date'] = date('Y-m-d H:i:s', strtotime("$completion_dates[$tracking_id] $completion_time"));
                            $data['notes'] = isset($notes[$tracking_id]) ? $notes[$tracking_id] : '--';
                            $data['entered_by'] =\Auth::user()->id;
                            $this->recCandidateTracking->updateOrCreate(array( 'candidate_id' => $candidate_id, 'process_lookups_id' => $process_id_lookup), $data);
                    }
                }
               // }
                // $candidateJob->update(['job_reassigned_id' => (int)$request->get('job_reassigned_id')]);
                 \DB::commit();
                 return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Already Saved']);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function deleteTracking($tracking_id, $candidate_id)
    {
        try {
              \DB::beginTransaction();
            $this->recCandidateTracking->where('process_lookups_id', $tracking_id)->where('candidate_id', $candidate_id)->delete();
             \DB::commit();
             return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('recruitment::create');
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
     * @param  Request $request
     * @return Response
     */
    public function store(RecCandidateCredentialRequest $request)
    {
        try {
            \DB::connection('mysql_rec')->beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::connection('mysql_rec')->commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::connection('mysql_rec')->rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
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
