<?php

namespace Modules\Expense\Http\Controllers\Admin;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Expense\Repositories\TaxMasterRepository;
use Modules\Expense\Http\Requests\TaxMasterRequest;
use Modules\Expense\Models\ExpenseTaxMaster;
use Modules\Expense\Models\ExpenseTaxMasterLog;

class TaxMasterController extends Controller
{
    protected $taxMasterRepository;


    public function __construct(TaxMasterRepository $taxMasterRepository)
    {
        $this->taxMasterRepository = $taxMasterRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('expense::admin.tax-master');
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
     * @return Response
     */
    public function store(TaxMasterRequest $request)
    {
        $effective_from_date = date('Y-m-d', strtotime($request->get('effective_from_date')));

        $taxMaster = new ExpenseTaxMaster([
            'name' => $request->get('name'),
            'short_name' => $request->get('short_name')

        ]);
        $taxMaster->save();

        $taxMasterLog = new ExpenseTaxMasterLog([
            'tax_percentage' => $request->get('tax_percentage'),
            'effective_from_date' => $effective_from_date,
            'tax_master_id' => $taxMaster->id

        ]);
        $taxMasterLog->save();

        return response()->json(array(
            'success' => true,
            'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Expense tracker data has been succesfully added.</div>'
        ));
    }

    public function getList()
    {
        //$data = Carbon::today();
        $data = $this->taxMasterRepository->getAll();

        return datatables()->of($data)->addIndexColumn()->toJson();
    }
    /**
     * Show the specified resource.
     * @return Response
     */
    public function getExpenseTracker(Request $request)
    {
        $data = ExpenseTaxMaster::find($request->get('id'));
        return response()->json($data);
    }

    public function expenseTrackerupdate(TaxMasterRequest $request)
    {

        $id = $request->get('id');

        $update = ExpenseTaxMaster::find($id);
        $update->name = $request->get('name');
        $update->short_name = $request->get('short_name');
        $update->save();
        $effective_from_date = date('Y-m-d', strtotime($request->get('effective_from_date')));
        $taxMasterLog = new ExpenseTaxMasterLog([
            'tax_percentage' => $request->get('tax_percentage'),
            'effective_from_date' => $effective_from_date,
            'tax_master_id' => $id

        ]);
        $taxMasterLog->save();

        return response()
            ->json(array(
                'success' => true,
                'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Tax Master data has been succesfully updated.</div>'
            ));
    }
    public function getSingle($id)
    {
        $taxMasterShow = ExpenseTaxMaster::where('id', $id);

        return view('expense::admin.tax-master-show', compact('id'));
    }

    public function taxMasterLogShow($id)
    {
        $taxMasterLogShow = ExpenseTaxMaster::where('id', $id)->with(['taxMasterLog' => function ($query) {
            $query->where('status', 0);
        }])->get();

        return datatables()->of($taxMasterLogShow)->addIndexColumn()->toJson();
    }

    public function taxMasterLogShowTrashed($id)
    {
        $taxMasterLogShowTrashed = ExpenseTaxMasterLog::onlyTrashed()
            ->where('tax_master_id', '=', $id)
            ->latest()->get();

        return datatables()->of($taxMasterLogShowTrashed)->addIndexColumn()->toJson();
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
    { }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {

        $date = Carbon::now();

        $effective_end_date = $date->toDateString();

        $archived_by = Auth::id();

        ExpenseTaxMasterLog::where('id', $id)->update(
            [
                'effective_end_date' => $effective_end_date,
                'archived_by' => $archived_by, 'status' => 1
            ]
        );

        $taxMasterLog = ExpenseTaxMasterLog::find($id);
        $taxMasterLog->delete();

        return response()->json(array(
            'success' => true,
            'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Tax data have been succesfully removed.</div>'
        ));
    }
}
