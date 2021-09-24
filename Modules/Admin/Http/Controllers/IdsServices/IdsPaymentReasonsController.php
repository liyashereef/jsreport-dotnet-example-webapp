<?php

namespace Modules\Admin\Http\Controllers\IdsServices;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Illuminate\Support\Carbon;
use App\Services\HelperService;

use Modules\Admin\Http\Requests\IdsPaymentReasonsRequest;
use Modules\Admin\Repositories\IdsPaymentReasonsRepository;


class IdsPaymentReasonsController extends Controller
{

    public function __construct(
        IdsPaymentReasonsRepository $idsPaymentReasonsRepository,
        HelperService $helperService
    )
    {
        $this->repository = $idsPaymentReasonsRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::ids-scheduling.payment-reason');
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
    public function store(IdsPaymentReasonsRequest $request)
    {

        try {
            \DB::beginTransaction();
            $inputs = $request->all();
            $result = $this->repository->store($inputs);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
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
            $this->repository->destroy($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }

    public function getById($id){
        return $this->repository->getById($id);
    }
}
