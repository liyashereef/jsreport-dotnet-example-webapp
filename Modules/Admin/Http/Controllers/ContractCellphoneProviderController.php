<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\ContractCellphoneProviderRepository ;
use Modules\Admin\Http\Requests\ContractCellphoneProviderRequest;

class ContractCellphoneProviderController extends Controller
{
    protected $contractcellphoneproviderRepository;
    public function __construct(ContractCellphoneProviderRepository $contractcellphoneproviderRepository){
        $this->contractcellphoneproviderRepository = $contractcellphoneproviderRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view("admin::contracts.ContractCellphoneProvider");
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
    public function store(ContractCellphoneProviderRequest $request)
    {
        $id = $request->get('id');
        $cellphoneprovidertitle = $request->get('cellphoneprovidertitle');
        return $this->contractcellphoneproviderRepository->save($id,$cellphoneprovidertitle);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        $providerlist = $this->contractcellphoneproviderRepository->showTotalList();
        return $providerlist;
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
        $cellphoneprovider = $request->get('cellphoneprovider');
        return $this->contractcellphoneproviderRepository->deleteContractCellPhoneProvider($cellphoneprovider);
    }
}
