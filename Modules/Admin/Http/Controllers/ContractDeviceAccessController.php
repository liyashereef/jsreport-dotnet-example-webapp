<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\ContractDeviceAccessRepository;
use Modules\Admin\Http\Requests\DeviceAccessRequest;

class ContractDeviceAccessController extends Controller
{
    protected $contractdeviceaccessrepository;

    public function __construct(ContractDeviceAccessRepository $contractdeviceaccessrepository){
        $this->contractdeviceaccessrepository = $contractdeviceaccessrepository;
    } 
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view("admin::contracts.ContractDeviceAccess");
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
    public function store(DeviceAccessRequest $request)
    {
        $id = $request->get('id');
        $devicetitle = $request->get('devicetitle');
        return $this->contractdeviceaccessrepository->save($id,$devicetitle);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        $deviceaccesslist = $this->contractdeviceaccessrepository->showTotalList();
        return $deviceaccesslist;
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
        $deviceid = $request->get('deviceid');
        return $this->contractdeviceaccessrepository->deleteDeviceaccess($deviceid);
    }
}
