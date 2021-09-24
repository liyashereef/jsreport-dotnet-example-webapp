<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Admin\Repositories\HolidayPaymentAllocationRepository;

class HolidayPaymentAllocationController extends Controller
{
    protected $helperService, $holidaypaymentAllocationRepository;
    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\HolidayRepository $holidayRepository;
     * @return void
     */
    public function __construct(HelperService $helperService, HolidayPaymentAllocationRepository $holidaypaymentAllocationRepository)
    {
        $this->helperService = $helperService;
        $this->holidaypaymentAllocationRepository = $holidaypaymentAllocationRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        //return view('admin::index');
        return view('admin::masters.holidaypaymentallocation');
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
    public function store(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function getList(Request $request)
    {
        $holidayallocationarray = $this->holidaypaymentAllocationRepository->getAll();
        foreach ($holidayallocationarray as $holidayallocation) {
            
        }
        return datatables()->of($this->holidaypaymentAllocationRepository->getAll())->addIndexColumn()->toJson();
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
}
