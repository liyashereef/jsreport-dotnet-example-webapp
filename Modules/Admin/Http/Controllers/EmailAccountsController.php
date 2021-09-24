<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use DB;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\EmailAccountsRequest;
use Modules\Admin\Repositories\EmailAccountsRepository;

class EmailAccountsController extends Controller
{
    protected $helperService;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @return void
     */
    public function __construct(HelperService $helperService, EmailAccountsRepository $emailAccountsRepository)
    {
        $this->helperService = $helperService;
        $this->emailAccountsRepository = $emailAccountsRepository;

    }

    public function index(){
        $data = $this->emailAccountsRepository->getAll();
        return view('admin::settings.email-accounts',compact('data'));
    }

    public function getList(){
        return datatables()->of($this->emailAccountsRepository->getEmailAccountsList())->addIndexColumn()->toJson();
    }

    public function getSingle($id)
    {
        return response()->json($this->emailAccountsRepository->getSingleEmailAccount($id));
    }

    public function store(EmailAccountsRequest $request){
        try {
            \DB::beginTransaction();
            $data = $this->emailAccountsRepository->save($request->all());
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
            $lookup_delete = $this->emailAccountsRepository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }


}
?>
