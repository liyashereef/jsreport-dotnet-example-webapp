<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\CustomerIncidentPriorityRequest;
use Modules\Admin\Repositories\CustomerIncidentPriorityRepository;
use Modules\Admin\Repositories\IncidentPriorityLookupRepository;

class CustomerIncidentPriorityController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\IncidentPriorityLookupRepository $incidentPriorityLookupRepository
     * @return void
     */
    public function __construct(CustomerIncidentPriorityRepository $customerIncidentPriorityRepository, HelperService $helperService,IncidentPriorityLookupRepository $incidentPriorityLookupRepository)
    {
        $this->repository = $customerIncidentPriorityRepository;
        $this->helperService = $helperService;
        $this->incidentPriorityLookupRepository =$incidentPriorityLookupRepository;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::masters.incident-priority');
    }

    /**
     * To check customer priority
     *
     * @return \Illuminate\Http\Response
     */
    public function checkPriority($id)
    {
        $incident_priorities = $this->repository->getCustomerIncidentPriority($id);
        if(!empty($incident_priorities)){
            $arr = [];
            foreach ($incident_priorities as $key => $value) {
                $arr[$key]['id'] =  $value['id'];
                $arr[$key]['priority_id'] =  $value['priority_id'];
                $arr[$key]['value'] =  $value['priority']['value'];
                $arr[$key]['response_time'] =  isset($value['response_time']) ? $value['response_time']/60 : null;
            }
          return response()->json(array('status' => 1, 'response' =>$arr));
        }else{
            $incident_priorities =  $this->incidentPriorityLookupRepository->getAll();
            $arr = [];
            foreach ($incident_priorities as $key => $value) {
               $arr[$key]['id'] =  null;
               $arr[$key]['priority_id'] =  $value->id;
               $arr[$key]['value'] =  $value->value;
               $arr[$key]['response_time'] =  null;

            }
          return response()->json(array('status' => 2, 'response' =>$arr));
        }
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return json
     */
    public function store(CustomerIncidentPriorityRequest $request)
    {

        try {
            \DB::beginTransaction();
          //  dd($request->all());
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            dd($e);
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
