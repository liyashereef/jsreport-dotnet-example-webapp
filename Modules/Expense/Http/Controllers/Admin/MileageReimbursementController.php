<?php

namespace Modules\Expense\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Expense\Repositories\MileageReimbursementRepository;
use Modules\Expense\Http\Requests\MileageReimbursementRequest;
use Modules\Expense\Models\ExpenseMileageReimbursementSlabRate;


class MileageReimbursementController extends Controller
{
    protected $repository, $helperService;
    public function __construct(MileageReimbursementRepository $mileageReimbursementrepository, 
    HelperService $helperService)
    {
        $this->repository = $mileageReimbursementrepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($id = null)
    {      
        $option_list = ExpenseMileageReimbursementSlabRate::get();
        return view('expense::admin.mileage-reimbursement',compact('option_list'));
    
}

    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('expense::admin');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(MileageReimbursementRequest $request)
    {
        try {
        \DB::beginTransaction();
        $isactive= $request->is_active;
        if($isactive == null){
            $request->request->add(['is_active' => 0]);
        }
            $lookup = $this->repository->save($request->all());
           \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
           // dd($e);
         \DB::rollBack();
           
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }
    

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('admin::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
  
}
