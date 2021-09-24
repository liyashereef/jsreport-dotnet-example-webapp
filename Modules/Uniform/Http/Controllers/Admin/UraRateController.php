<?php

namespace Modules\Uniform\Http\Controllers\Admin;

use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Uniform\Repositories\UraRateRepository;

class UraRateController extends Controller
{
    protected $uraRateRepository;
    protected $helperService;

    public function __construct(
        UraRateRepository $uraRateRepository,
        HelperService $helperService
    ) {
        $this->uraRateRepository = $uraRateRepository;
        $this->helperService = $helperService;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('uniform::admin.ura-rates', [
            'amount' => $this->uraRateRepository->getCurrentRate()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('uniform::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric'
        ]);
        try {
            $this->uraRateRepository->store($request);
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('uniform::show');
    }

    public function getList()
    {
        return datatables()->of($this->uraRateRepository->getAll())->addIndexColumn()->toJson();
    }

}
