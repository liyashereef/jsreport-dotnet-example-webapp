<?php

namespace Modules\Client\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\ClientFeedbackLookupRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\EmployeeRatingLookupRepository;
use Modules\Admin\Repositories\SeverityLookupRepository;
use Modules\Client\Http\Requests\ClientConcernRequest;
use Modules\Client\Repositories\ConcernRepository;
use Modules\Admin\Models\WhistleblowerStatusLookup;

class ClientConcernController extends Controller
{

    public function __construct(
        ConcernRepository $concernRepository,
        EmployeeRatingLookupRepository $employeeRatingLookupRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        ClientFeedbackLookupRepository $clientFeedbackLookupRepository,
        SeverityLookupRepository $severityLookupRepository,
        WhistleblowerStatusLookup $whistleblowerStatusLookup
    ) {
        $this->concernRepository = $concernRepository;
        $this->employeeRatingLookupRepository = $employeeRatingLookupRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->clientFeedbackLookupRepository = $clientFeedbackLookupRepository;
        $this->severityLookupRepository = $severityLookupRepository;
        $this->whistleblowerStatusLookup = $whistleblowerStatusLookup;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $project_list = $this->concernRepository->getProjects();
        $statusList = $this->whistleblowerStatusLookup->orderBy('status')->get();
        return view(
            'client::client-concern',
            [
                'severity_lookups' => $this->severityLookupRepository->getList(),
                'project_list' => $project_list,
                'statusList' => $statusList,

            ]
        );
    }

    public function getEmployeeList($project_id)
    {
        return response()->json($this->clientRepository->getEmployees([$project_id]));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('client::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(ClientConcernRequest $request)
    {
        // Current User
        $login_client = Auth::user();
        // Store to DB
        return $this->concernRepository->store($login_client->id, $request);
    }

    /**
     * Get table list
     */
    public function getTableList(Request $request)
    {

        return datatables()->of($this->concernRepository->getTableList([], true, $request))->toJson();
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('client::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        return $this->concernRepository->getSingle($id);
        //return view('client::edit');
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
}
