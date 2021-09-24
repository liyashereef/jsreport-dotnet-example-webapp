<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use DB;
use Illuminate\Http\Request;
// use Modules\Admin\Http\Requests\VisitorStatusLookupsRequest;
use Modules\Admin\Repositories\VisitorStatusLookupsRepository;

class VisitorStatusLookupsController extends Controller
{
    protected $helperService, $repository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\VisitorStatusLookupsRepository $VisitorStatusLookupsRepository;
     * @return void
     */
    public function __construct(
        HelperService $helperService, 
        VisitorStatusLookupsRepository $repository
        )
    {
        $this->helperService = $helperService;
        $this->repository = $repository;
    }

    /**
     * redirect to visitorlog-status page.
     * @return view
     */

    public function index(){
        return view('admin::client.visitorlog-status');
    }

     /**
     * List all Visitor Status in datatable.
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
            if(!isset($inputs['is_authorised'])){
                $inputs['is_authorised'] = 0;
            }
            $lookup = $this->repository->save($inputs);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
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
            $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }




}

?>