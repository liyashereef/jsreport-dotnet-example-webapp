<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use DB;
use Modules\Admin\Repositories\WhistleblowerStatusLookupsRepository;
use Modules\Admin\Http\Requests\WhistleblowerMasterRequest;

class WhistleblowerStatusLookupController extends Controller
{
    protected $helperService, $repository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\WorkTypeRepository $WhistleblowerRepository;
     * @return void
     */
    public function __construct(HelperService $helperService, WhistleblowerStatusLookupsRepository $whistleblowerStatusLookupsRepository)
    {
        $this->helperService = $helperService;
        $this->repository = $whistleblowerStatusLookupsRepository;
    }

    /**
     * Display a listing of the Work Types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $namesLookups = $this->repository->getNamesLookups();
        $selectedInitialValue = $this->repository->getSelectedInitialValue();
        return view('admin::masters.whistleblower-master',compact('namesLookups','selectedInitialValue'));
    }

    public function getList(){
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created bank in storage.
     *
     * @param  App\Http\Requests\UserSalutationRequest $request
     * @return json
     */
    public function store(WhistleblowerMasterRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function storeIntialStatus($id)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->storeIntialStatus($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }


     /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->repository->delete($id);
            \DB::commit();
            if($lookup_delete){
                return response()->json($this->helperService->returnTrueResponse());
            }else{
                return ["success"=>false,"message"=>"Inital Status Exists"];
            }

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }







}
