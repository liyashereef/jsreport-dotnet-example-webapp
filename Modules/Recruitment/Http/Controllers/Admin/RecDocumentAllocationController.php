<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Recruitment\Http\Requests\RecDocumentAllocationRequest;
use Modules\Recruitment\Repositories\RecDocumentAllocationRepository;
use Modules\Recruitment\Models\RecDocumentAllocation;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Recruitment\Models\RecOnboardingDocuments;
use Modules\Recruitment\Models\RecProcessTab;
use Illuminate\Support\Facades\Log;

class RecDocumentAllocationController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\TrackingProcessLookupRepository $TrackingProcessLookupRepository
     * @return void
     */
    public function __construct(RecDocumentAllocationRepository $recDocumentAllocationRepository, HelperService $helperService,CustomerRepository $customerRepository)
    {
        $this->repository = $recDocumentAllocationRepository;
        $this->helperService = $helperService;
        $this->customerRepository = $customerRepository;

    }



    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $default = [0 => 'All Customers'];
        $customer_list = $default + $this->customerRepository->getProjectsDropdownList('all');
        $onboarding_documents = RecOnboardingDocuments::orderBy('document_name','asc')->get();
        $process_tabs = RecProcessTab::orderBy('display_name','asc')->get();
        return view('recruitment::customer.document-allocation',compact('customer_list','onboarding_documents','process_tabs'));
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList($cid)
    {
        $customer_id = $cid;
        $customer_list = $this->customerRepository->getProjectsDropdownList('all');
        $onboarding_documents = RecOnboardingDocuments::orderBy('document_name','asc')->get();
        $process_tabs = RecProcessTab::orderBy('display_name','asc')->get();
        $document_list = RecDocumentAllocation::where('customer_id', $customer_id)->get();
        return view('recruitment::customer.document-allocation',compact('customer_list','onboarding_documents','process_tabs','customer_id','document_list'));
    }

    /**
     * Get document list according to category and customer id
     * @param Request $custid, $catid
     * @param Response
     */
    public function getCustCatList($custid, $catid)
    {
        $customer_id = $custid;
        $category_id = $catid;
        $default = [0 => 'All Customers'];
        $customer_list = $default + $this->customerRepository->getProjectsDropdownList('all');
        $onboarding_documents = RecOnboardingDocuments::orderBy('document_name','asc')->get();
        $process_tabs = RecProcessTab::orderBy('display_name','asc')->get();
        $document_list = RecDocumentAllocation::where('customer_id', $customer_id)
        ->where('process_tab_id', $category_id)->get();
        return view('recruitment::customer.document-allocation',compact('customer_list','onboarding_documents','process_tabs','customer_id','category_id','document_list'));
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
     * @param  App\Http\Requests\RecDocumentAllocationRequest $request
     * @return json
     */
    public function store(RecDocumentAllocationRequest $request)
    {
        try {
            \DB::beginTransaction();

                for ($i = 0; $i < count($request->get('document_name')); $i++) {
                    $allocation_data = [
                            'customer_id' => $request->customer_id,
                            'process_tab_id' => $request->category_id,
                            'document_name' => $request->document_name[$i],
                            'document_id' => $request->document_id[$i],
                            'order' => $request->order[$i],
                        ];
                        RecDocumentAllocation::updateOrCreate(array('id' => $request->id[$i]), $allocation_data);
                    }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            Log::info($e);
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

 //   public function customerDocumentAllocation($cid) {
 //      return $this->repository->singleCustomerDocuments($cid);
 //   }
}
