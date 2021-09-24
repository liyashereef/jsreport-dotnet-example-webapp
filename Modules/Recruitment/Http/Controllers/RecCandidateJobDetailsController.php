<?php

namespace Modules\Recruitment\Http\Controllers;


use Illuminate\Routing\Controller;
use Modules\Recruitment\Repositories\RecCandidateJobDetailsRepository;

class RecCandidateJobDetailsController extends Controller
{

    protected $recCandidateJobDetailsRepository;

    public function __construct(
        RecCandidateJobDetailsRepository $recCandidateJobDetailsRepository
    ) {

        $this->recCandidateJobDetailsRepository=$recCandidateJobDetailsRepository;
    }

   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('recruitment::candidatejob.index');
    }

    /**
     * Get list of jobs
     *
     * @return datatable object
     */

    public function getList()
    {
        return datatables()->of($this->recCandidateJobDetailsRepository->getCandidateJobs())->toJson();
    }

   

   


  

    
}
