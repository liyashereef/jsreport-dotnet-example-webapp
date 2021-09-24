<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use DB;
use Illuminate\Http\Request;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmailGroupRepository;
use Modules\Admin\Http\Requests\EmailGroupRequest;

class EmailGroupsController extends Controller
{
    protected $helperService, $customerRepository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @return void
     */
    public function __construct(HelperService $helperService, EmailGroupRepository $emailGroupRepository, CustomerRepository $customerRepository)
    {
        $this->helperService = $helperService;
        $this->repository = $emailGroupRepository;
        $this->customerRepository=$customerRepository;

    }

    public function index(){
        $customer = $this->customerRepository->getProjectsDropdownList('all');
        return view('admin::email-groups.email-groups',compact('customer'));
    }

    public function getEmailGroupsList(){
        return datatables()->of($this->repository->getEmailGroups())->addIndexColumn()->toJson();
    }

    public function getSingle($id)
    {
        return response()->json($this->repository->getSingleGroupDetails($id));
    }

    public function store(EmailGroupRequest $request){
        // dd($request->all());
        try {
            \DB::beginTransaction();
            $data = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function destroy($id){
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


?>
