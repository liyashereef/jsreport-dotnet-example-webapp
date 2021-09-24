<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Expense\Repositories\MileageClaimRepository;
use Carbon\Carbon;
use Modules\Expense\Models\MileageClaim;
use Modules\Expense\Models\ExpenseSettingsFinanceControllers;
use Modules\Expense\Models\ExpenseAllowableForUser;
use DB;
use Illuminate\Support\Facades\Auth;

class MileageDashboardController extends Controller
{

    protected $mileageClaimRepository;
    public function __construct(
        MileageClaimRepository $mileageClaimRepository,
        MileageClaim $mileageClaim,
        ExpenseSettingsFinanceControllers $expenseSettingsFinanceControllers
    ) {
        $this->mileageClaimRepository = $mileageClaimRepository;
        $this->mileageClaim = $mileageClaim;
        $this->expenseSettingsFinanceControllers = $expenseSettingsFinanceControllers;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        if (!isset($request->viewmyexpense)) {
            $viewMyExpense = 0;
        } else {
            $viewMyExpense = $request->viewmyexpense;
        }

        $current = Carbon::now();
        if($current->day > 15){
            $startdate = $current->firstOfMonth()->addDays(14)->toDateString();
            $enddate   =  $current->firstOfMonth()->addDays(13)->addMonth()->toDateString();
        }else{
            $startdate = $current->firstOfMonth()->addDays(14)->addMonth(-1)->toDateString();
            $enddate   = $current->firstOfMonth()->addMonth(1)->addDays(13)->toDateString();
        }
        $mileage_counts = $this->mileageClaimRepository->getMileageCounts($viewMyExpense,$startdate,$enddate);
        $user_list = $this->mileageClaimRepository->employeeLookUps();
        $finance_controller_status = $this->expenseSettingsFinanceControllers
        ->where('financial_controller', \Auth::id())->count();
        return view('expense::mileage_dashboard',compact(
            'mileage_counts',
            'startdate',
            'enddate',
            'finance_controller_status',
            'viewMyExpense',
            'user_list'
        ));
    }


    public function getList(Request $request)
    {
        $viewmyexpense = $request->viewmyexpense;
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $status = empty($request->status)?'':$request->status;
        $employee = empty($request->employee)?'':$request->employee;
        $mileage_list=$this->mileageClaimRepository->getAll($viewmyexpense,$startdate, $enddate, $status, $employee);
        return datatables()->of($this->mileageClaimRepository
        ->prepareDataForMileageClaim($mileage_list))
        ->addIndexColumn()
        ->toJson();
    }

    /**
     * Show the form fourl
     * @return Responseurl
     */
    public function create()
    {
        return view('expense::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('expense::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $result    = $this->mileageClaimRepository->getSingle($id);
        $mileage_id = $id;
        $finance_controller_status = $this->expenseSettingsFinanceControllers
        ->where('financial_controller', \Auth::id())
        ->count();
        $approver_status = ExpenseAllowableForUser::where('user_id', $result->created_by)
        ->where('reporting_to_id', \Auth::id())
        ->count();
        return view('expense::mileage_view',
        compact(
            'result',
            'finance_controller_status',
            'approver_status',
            'mileage_id'
        ));

    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        try {
             DB::beginTransaction();
             $this->mileageClaimRepository->updateMileageClaim($request);
             DB::commit();
            return response()->json(array('success' => true));
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }

    public function getCounts($viewmyexpense, $startdate, $enddate, $employee = null){
        $employee = empty($employee)?'':$employee;
       return $this->mileageClaimRepository
       ->getMileageCounts($viewmyexpense, $startdate, $enddate, $employee);

    }
}
