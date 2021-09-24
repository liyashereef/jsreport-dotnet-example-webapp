<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Services\HelperService;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Repositories\VisitorLogScreeningTemplateRepository;
use Modules\Admin\Repositories\VisitorLogScreeningTemplateCustomerAllocationRepository;
use Modules\Admin\Models\Customer;
class VisitorLogScreeningTemplateController extends Controller
{

    protected $helperService, $repository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\VisitorLogScreeningTemplateRepository $VisitorLogScreeningTemplateRepository;
     * @var \Modules\Admin\Repositories\VisitorLogScreeningTemplateCustomerAllocationRepository $VisitorLogScreeningTemplateCustomerAllocationRepository;
     * @var \Modules\Admin\Models\Customer $Customer;
     * @return void
     */
    public function __construct(
        HelperService $helperService,
        VisitorLogScreeningTemplateRepository $repository,
        VisitorLogScreeningTemplateCustomerAllocationRepository $visitorLogScreeningTemplateCustomerAllocationRepository,
        Customer $customers
        )
    {
        $this->helperService = $helperService;
        $this->repository = $repository;
        $this->visitorLogScreeningTemplateCustomerAllocationRepository = $visitorLogScreeningTemplateCustomerAllocationRepository;
        $this->customers = $customers;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $customers =  $this->customers
        ->where('visitor_screening_enabled',1)
        ->where('active',1)
        ->doesntHave('VisitorLogScreeningTemplateCustomerAllocation')
        ->get()->pluck('client_name_and_number','id')->toArray();

        return view('admin::client.visitorlog-screening-template',compact('customers'));
    }

    /**
     * List all visitor screening template in datatable.
     * @return Json
     */
    public function getList(){
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

  /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingle($id){
        return response()->json($this->repository->get($id));
    }

      /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function store(Request $request){
        try {
            \DB::beginTransaction();
            $inputs = $request->all();
            unset($inputs['customer_id']);

            if($request->filled('id')){
                $inputs['updated_by'] = Auth::id();
                $data['updated_by'] =  Auth::id();
            }else{
                $data['created_by'] =  Auth::id();
                $inputs['created_by'] = Auth::id();
            }
            $lookup = $this->repository->save($inputs);
            if($lookup && $request->filled('customer_id')){
                $data['visitor_log_screening_template_id'] = $lookup->id;
                foreach($request->input('customer_id') as $customer_id){
                    $data['customer_id'] = $customer_id;

                    $this->visitorLogScreeningTemplateCustomerAllocationRepository->save($data);
                }
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }


        /**
     * Remove the Visitor Status from storage.
     *
     * @param  $id
     * @return Json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $this->visitorLogScreeningTemplateCustomerAllocationRepository->deleteByTemplateId($id);
            $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

    public function templatesOfficeAllocationDestroy($id){
        try {
            \DB::beginTransaction();
            $this->visitorLogScreeningTemplateCustomerAllocationRepository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

}
