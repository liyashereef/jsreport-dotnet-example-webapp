<?php

namespace Modules\Admin\Http\Controllers\IdsServices;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Services\HelperService;
use Modules\Admin\Http\Requests\IdsPassportPhotoRequest;
use Modules\Admin\Repositories\IdsPassportPhotoServiceRepository;

class IdsPassportPhotosController extends Controller
{

    public function __construct(
        IdsPassportPhotoServiceRepository $idsPassportPhotoServiceRepository,
        HelperService $helperService
    )
    {
        $this->repository = $idsPassportPhotoServiceRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::ids-scheduling.passport-photos');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getById($id){
        return $this->repository->getById($id);
    }

    /**
     * list all data.
     * @return Response
     */
    public function getAll()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(IdsPassportPhotoRequest $request)
    {
        try {
            \DB::beginTransaction();
            $inputs = $request->all();
            $result = $this->repository->store($inputs);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $delete = $this->repository->destroy($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

}
