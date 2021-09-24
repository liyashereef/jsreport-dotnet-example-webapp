<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\ReasonForSubmissionRequest;
use App\Services\HelperService;

use Modules\Admin\Repositories\ReasonForSubmissionRepository;

class ContractsController extends Controller
{
    protected $reasonforsubmissionrepository;

    public function __construct(ReasonForSubmissionRepository $reasonforsubmissionrepository){
        $this->reasonforsubmissionrepository = $reasonforsubmissionrepository;
    } 
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('admin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(ReasonForSubmissionRequest $request)
    {
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
     * @return Response
     */
    public function destroy()
    {
    }

    public function getSubmissionReasonList(Request $request){
        
        return view("admin::contracts.submissionreason");
    }

    public function getReasonList(Request $request){
        $submissionreasonlist = $this->reasonforsubmissionrepository->getReasonList();
        return $submissionreasonlist;
    }

    public function reasonforsubmissionupdate(Request $request){
        $submissionreasonlist = $this->reasonforsubmissionrepository->getReasonList();
        return $submissionreasonlist;
    }

    public function savesubmissionreason(ReasonForSubmissionRequest $request){
        $submissionreason = $request->get('submissionreason');
        $submissionid = $request->get('reasonid');
        $previoussequence = $request->get('previoussequence');
        return $this->reasonforsubmissionrepository->saveSubmissionReason($submissionreason,$submissionid,$previoussequence);
        
    }

    public function updatesubmissionreason(ReasonForSubmissionRequest $request){
        $submissionreason = $request->get('submissionreason');
        $submissionid = $request->get('reason-id');
        $previoussequence = $request->get('reason-sequence');
        return $this->reasonforsubmissionrepository->updateSubmissionReason($submissionreason,$submissionid,$previoussequence);
        
    }

    public function deletesubmissionreason(Request $request){
        $reasonid = $request->get('reasonid');
        $current_sequence = $request->get('reason-sequence');
        return $this->reasonforsubmissionrepository->deleteSubmissionReason($reasonid,$current_sequence);
    }

    
}
