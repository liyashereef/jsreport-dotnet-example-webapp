<?php

namespace Modules\Client\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\ClientFeedbackLookupRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\EmployeeRatingLookupRepository;
use Modules\Client\Http\Requests\ClientEmployeeRatingRequest;
use Modules\Client\Repositories\ClientRepository;
use Modules\Admin\Models\WhistleblowerStatusLookup;

class ClientEmployeeFeedbackController extends Controller
{

    public function __construct(
        ClientRepository $clientRepository,
        EmployeeRatingLookupRepository $employeeRatingLookupRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        ClientFeedbackLookupRepository $clientFeedbackLookupRepository,
        WhistleblowerStatusLookup $whistleblowerStatusLookup
    ) {
        $this->clientRepository = $clientRepository;
        $this->employeeRatingLookupRepository = $employeeRatingLookupRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->clientFeedbackLookupRepository = $clientFeedbackLookupRepository;
        $this->whistleblowerStatusLookup = $whistleblowerStatusLookup;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $employee_rating_list = $this->employeeRatingLookupRepository->getList();
        $project_list = $this->clientRepository->getProjects();
        $client_feedback_list = $this->clientFeedbackLookupRepository->getList();
        $statusList= $this->whistleblowerStatusLookup->orderBy('status')->get();
        return view(
            'client::client-employee-feedback',
            [
                'employee_list' => $this->clientRepository->getEmployees(),
                'rating_lookups' => $employee_rating_list,
                'project_list' => $project_list,
                'client_feedback_list' => $client_feedback_list,
                'statusList' => $statusList,
            ]
        );
    }

    public function getEmployeeList($project_id)
    {
        return response()->json($this->clientRepository->getEmployees([$project_id],['client'],true));
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
    public function store(ClientEmployeeRatingRequest $request)
    {
        // Current User
        $login_client = Auth::user();
        // Store to DB
        return $this->clientRepository->store($login_client->id, $request);
    }

    /**
     * Get table list
     */
    public function getTableList()
    {
        return datatables()->of($this->clientRepository->getTableList())->toJson();
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
        return $this->clientRepository->getSingle($id);
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
