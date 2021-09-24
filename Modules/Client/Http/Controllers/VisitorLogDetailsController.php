<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\VisitorLogTypeLookup;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Client\Repositories\VisitorLogRepository;
use View;

class VisitorLogDetailsController extends Controller
{

    protected $visitorLogRepository;

    public function __construct(CustomerRepository $customerRepository, VisitorLogRepository $visitorLogRepository

    ) {
        $this->customerRepository = $customerRepository;
        $this->visitorLogRepository = $visitorLogRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($visitor_type_id = 0, $customer_id)
    {
        $visitor_type = VisitorLogTypeLookup::pluck('type', 'id')->toArray();
        if (\Auth::user()->can('view_all_visitorlog')) {
            $project_list = $this->customerRepository->getProjectsDropdownList('all');
        } else if (\Auth::user()->can('view_allocated_visitorlog')) {
            $project_list = $this->customerRepository->getProjectsDropdownList('allocated');
        } else {
            $project_list = $this->customerRepository->getProjectsDropdownList('all');
        }
        return view('client::visitor-log-details', compact('visitor_type', 'project_list', 'visitor_type_id', 'customer_id'));

    }

    public function listAllVisitors(Request $request)
    {
        $visitor_type = $request->type;
        $customer = $request->customer;
        $from = $request->from;
        $to = $request->to;

        $trigger = true; 

        //Checking employee have customer allocation
        if (\Auth::user()->can('view_allocated_visitorlog')) {
            $trigger = false; 
            $project_list = $this->customerRepository->getProjectsDropdownList('allocated');
            foreach($project_list as $key=>$project){
                if($key == $customer){
                    $trigger = true;   
                }
            }
        } 
        if (\Auth::user()->can('view_all_visitorlog')) {
            $trigger = true; 
        }

        $details = [];
        if($trigger == true){
            $details = $this->visitorLogRepository->list($visitor_type, $customer, $from, $to);
        }
        return datatables()->of($details)->addIndexColumn()->toJson();
    }

}
