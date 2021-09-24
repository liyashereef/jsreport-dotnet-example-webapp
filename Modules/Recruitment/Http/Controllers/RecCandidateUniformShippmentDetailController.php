<?php

namespace Modules\Recruitment\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Repositories\RecCandidateUniformShippmentDetailRepository;
use Modules\Recruitment\Repositories\RecCustomerUniformKitRepository;
use Modules\Recruitment\Models\RecUniformMeasurementPoint;
use Modules\Recruitment\Models\RecCandidate;

class RecCandidateUniformShippmentDetailController extends Controller
{
    public function __construct(
        RecCandidateUniformShippmentDetailRepository $recCandidateUniformShippmentDetailRepository,
        HelperService $helperService,
        RecCustomerUniformKitRepository $recCustomerUniformKitRepository
    ) {
        $this->recCandidateUniformShippmentDetailRepository = $recCandidateUniformShippmentDetailRepository;
        $this->helperService = $helperService;
        $this->recCustomerUniformKitRepository=$recCustomerUniformKitRepository;
    }

    /**
     * Display a candidate uniform shippment detail page
     * @return Response
     */
    public function index()
    {

        $measuringPoints = RecUniformMeasurementPoint::select('id', 'name')->get()->toArray();
        $candidate=RecCandidate::get()->pluck('full_address', 'id')->toArray();
        return view('recruitment::candidate-uniform-shippment-detail', compact('measuringPoints', 'candidate'));
    }

    /**
     * Get list of candidate uniform shippment details
     * @return Response
     */
    public function getList()
    {
        return datatables()->of($this->recCandidateUniformShippmentDetailRepository->getList())->toJson();
    }

    /**
     * Update shippment status
     * @param  Request $request
     * @return Response
     */
    public function saveStatus(Request $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->recCandidateUniformShippmentDetailRepository->saveShippingStatus($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            dd($e);
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
    /**
     * Get Kit details
     * @param  Request $request
     * @return Response
     */
    public function getKitDetails($id, $candidate_id)
    {
        try {
            \DB::beginTransaction();
            $data = $this->recCustomerUniformKitRepository->getKitDetails($id, $candidate_id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse($data));
        } catch (\Exception $e) {
            dd($e);
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
