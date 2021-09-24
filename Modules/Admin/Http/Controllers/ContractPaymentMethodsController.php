<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\ContractPaymentMethodRepository ;
use Modules\Admin\Http\Requests\PaymentMethodRequest;

class ContractPaymentMethodsController extends Controller
{
    protected $contractpaymentmethodrepository;
    public function __construct(ContractPaymentMethodRepository $ContractPaymentMethodRepository){
        $this->ContractPaymentMethodRepository = $ContractPaymentMethodRepository;
    } 
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view("admin::contracts.ContractPaymentMethods");
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
    public function store(PaymentMethodRequest $request)
    {
        $id = $request->get('id');
        $paymentmethodtitle = $request->get('paymentmethodtitle');
        return $this->ContractPaymentMethodRepository->save($id,$paymentmethodtitle);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        $paymentmethodlist = $this->ContractPaymentMethodRepository->showTotalList();
        return $paymentmethodlist;
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
        $paymentmethodid = $request->get('paymentmethodid');
        return $this->ContractPaymentMethodRepository->deletePaymentMethod($paymentmethodid);
    }
}
