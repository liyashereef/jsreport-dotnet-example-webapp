<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Modules\Admin\Repositories\CustomerQrcodeLocationRepository;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\CustomerQrCodeRequest;
use DB;


class CustomerQrCodeLocationController extends Controller
{
    protected $repository;
    /**
     * Create Repository instance.
     * @param  \App\Repositories\EnglishRatingLookupRepository $englishRatingLookupRepository
     * @return void
     */
    public function __construct(CustomerQrcodeLocationRepository $customerQrcodeLocationRepository,HelperService $helperService)
    {
        $this->repository = $customerQrcodeLocationRepository;
        $this->helperService = $helperService;
    }

    /**
     *Get a listing of the Request Type Master for Datatable.
     *
     * @return Json
     */
    public function getList($id=null)
    {
        return datatables()->of($this->repository->getAll($id))->addIndexColumn()->toJson();
    }
    
    public function store(CustomerQrCodeRequest $request)
    {
        try {
            DB::beginTransaction();
            $customerQrCode =   $this->repository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
        
        
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
