<?php

namespace Modules\Uniform\Http\Controllers;

use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Uniform\Http\Requests\UraTransactionRequest;
use Modules\Uniform\Repositories\UraOperationTypeRepository;
use Modules\Uniform\Repositories\UraTransactionRepository;
use Modules\Uniform\Utils\TransactionType;

class UraTransactionController extends Controller
{
    protected $uraTransactionRepository;
    protected $uraOperationTypeRepository;
    protected $helperService;
    protected $employeeAllocationRepository;

    public function __construct(
        UraTransactionRepository $uraTransactionRepository,
        UraOperationTypeRepository $uraOperationTypeRepository,
        HelperService $helperService,
        EmployeeAllocationRepository $employeeAllocationRepository
    ) {
        $this->uraTransactionRepository = $uraTransactionRepository;
        $this->uraOperationTypeRepository = $uraOperationTypeRepository;
        $this->helperService = $helperService;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $users  = $this->employeeAllocationRepository->getUserAllocationList(auth()->user()->id);
        $uraOperationTypes = $this->uraOperationTypeRepository->getUnrestrictedList();

        //Todo replace
        return view('uniform::ura-transactions', [
            'users' => $users,
            'uraOperationTypes' => $uraOperationTypes
        ]);
    }

    public function list(Request $request)
    {
        return datatables()->of($this->uraTransactionRepository->getList($request))
            ->addIndexColumn()
            ->toJson();
    }


    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(UraTransactionRequest $request)
    {
        $tt = $request->input('transaction_type');
        $user = auth()->user();
        // dd($user->can('add_ura_credit_transaction'),$tt,TransactionType::CREDIT);
        if (
            ($tt == 1 && !$user->can('add_ura_debit_transaction')) || //debit
            ($tt == 2 && !$user->can('add_ura_credit_transaction'))   //credit
        ) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to create URA transaction"
            ]);
        }


        try { //TODO::check transaction
            DB::beginTransaction();
            $this->uraTransactionRepository->store($request);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function getBalanceInfo(Request $request)
    {
        /**
         * @var User $user
         */
        $user = auth()->user();

        if ($user == null || !$user->can('view_ura_balance')) {
            return response()->json([
                'success' => false,
                'error' => 'User does not have the right permissions to view URA Balance'
            ], 402);
        }

        $request->validate([
            'user_id' => 'required|numeric'
        ]);

        $uid = $request->input('user_id');

        return response()->json($this->uraTransactionRepository->getUserBalanceInfo($uid));
    }
}
