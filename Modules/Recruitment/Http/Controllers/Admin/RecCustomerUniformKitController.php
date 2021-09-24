<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Recruitment\Http\Requests\RecCustomerUniformKitRequest;
use Modules\Recruitment\Repositories\RecCustomerUniformKitRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Recruitment\Models\RecUniformItems;
use Modules\Recruitment\Models\RecCustomerUniformKit;
use Modules\Recruitment\Models\RecCustomerUniformKitMapping;

class RecCustomerUniformKitController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\RecCriteriaLookupRepository $recCriteriaLookupRepository
     * @return void
     */
    public function __construct(RecCustomerUniformKitRepository $recCustomerUniformKitrepository, RecUniformItems $uniformItemsModel, CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository, HelperService $helperService)
    {
        $this->repository = $recCustomerUniformKitrepository;
        $this->uniformItemsModel = $uniformItemsModel;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->helperService = $helperService;
    }
        /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('recruitment::masters.customer-uniform-kit');
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
     * Controller for GET method of add and edit of template
     *
     * @param int $dropdown_id
     */
    public function addUniformKit($kitId = null)
    {
        $mappingArr = [];
        $mappingArryItems = [];
         $default      = ['null' =>'Please Select'];
        $customer_list = $this->customerEmployeeAllocationRepository->getCustomersList()->sortBy('client_name');
        $uniformItemList = $this->uniformItemsModel->orderBy('item_name', 'asc')->get();
        if (isset($kitId) && !empty($kitId)) {
            $mappingArryItems = RecCustomerUniformKitMapping::where('kit_id', $kitId)->get();
            $mappingArr = RecCustomerUniformKit::with('customerUniformKitMappings')->where('id', $kitId)->first()->toArray();
        }

        return view('recruitment::masters.customer-uniform-add-kit', compact('uniformItemList', 'mappingArr', 'customer_list', 'mappingArryItems'));
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
     * @param  App\Http\Requests\CandidateExperienceRequest $request
     * @return json
     */
    public function store(RecCustomerUniformKitRequest $request)
    {
        try {
            \DB::beginTransaction();
            if (isset($request->id) && !empty($request->id)) {
                $result = $this->repository->update($request->all());
            } else {
                $result =  $this->repository->save($request->all());
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
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
