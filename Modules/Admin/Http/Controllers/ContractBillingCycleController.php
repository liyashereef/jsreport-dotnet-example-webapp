<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\ContractBillingCycleRepository;
use Modules\Admin\Http\Requests\BillingCycleRequest;

class ContractBillingCycleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    protected $contractbillingcyclerepository;

    public function __construct(ContractBillingCycleRepository $contractbillingcyclerepository){
        $this->contractbillingcyclerepository = $contractbillingcyclerepository;
    }

    public function index()
    {
        return view("admin::contracts.ContractBillingCycle");
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
    public function store(BillingCycleRequest $request)
    {
        $id = $request->get('id');
        $billingcycletitle = $request->get('billingcycletitle');
        return $this->contractbillingcyclerepository->save($id,$billingcycletitle);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        $submissionreasonlist = $this->contractbillingcyclerepository->showTotalList();
        return $submissionreasonlist;
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
    public function destroy(Request $request)
    {
        $billingcycleid = $request->get('billingcycleid');
        return $this->contractbillingcyclerepository->deleteRatechange($billingcycleid);
    }
}
