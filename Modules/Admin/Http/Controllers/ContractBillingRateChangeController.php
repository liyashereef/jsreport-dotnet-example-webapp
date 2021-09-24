<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\ContractBillingRateChangeRepository;
use Modules\Admin\Http\Requests\RateChangePeriodRequest;

class ContractBillingRateChangeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */

    protected $contractbillingratechangerepository;

    public function __construct(ContractBillingRateChangeRepository $contractbillingratechangerepository){
        $this->contractbillingratechangerepository = $contractbillingratechangerepository;
    } 


    public function index()
    {
        return view("admin::contracts.ContractBillingRateChange");
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
    public function store(RateChangePeriodRequest $request)
    {
        $id = $request->get('id');
        $ratechangetitle = $request->get('ratechangetitile');
        return $this->contractbillingratechangerepository->save($id,$ratechangetitle);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        $submissionreasonlist = $this->contractbillingratechangerepository->showTotalList();
        return $submissionreasonlist;
    }

    

    

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy(Request $request)
    {
        $ratechangeid = $request->get('ratechangeid');
        return $this->contractbillingratechangerepository->deleteRatechange($ratechangeid);
    }
}
