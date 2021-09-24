<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\OfficeAddressRepository;
use Modules\Admin\Http\Requests\OfficeAddressRequest;

class OfficeAddressController extends Controller
{
    protected $officeaddressrepository;
    public function __construct(OfficeAddressRepository $officeaddressrepository){
        $this->officeaddressrepository = $officeaddressrepository;
    } 
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view("admin::contracts.Officeaddress");
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
    public function store(OfficeAddressRequest $request)
    {
        $id = $request->get('id');
        $officeaddresstitle = $request->get('officeaddresstitle');
        $officeaddress = $request->get('officeaddress');
        return $this->officeaddressrepository->save($id,$officeaddresstitle,$officeaddress);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        $officeaddresslist = $this->officeaddressrepository->showTotalList();
        return $officeaddresslist;
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
        $addressid = $request->get('addressid');
        return $this->officeaddressrepository->deleteOfficeaddress($addressid);
    }
}
