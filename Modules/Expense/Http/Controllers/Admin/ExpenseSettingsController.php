<?php

namespace Modules\Expense\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Admin\Repositories\UserRepository;
use Modules\Expense\Models\ExpenseSettings;
use Modules\Expense\Models\ExpenseSettingsFinanceControllers;
use Modules\Expense\Models\ExpenseEmailUpdate;

class ExpenseSettingsController extends Controller
{
    protected $repository, $helperService;
    public function __construct(
        UserRepository $userrepository,
        HelperService $helperService,
        ExpenseSettings $expenseSettings,
        ExpenseSettingsFinanceControllers $expenseSettingsFinanceControllers,
        ExpenseEmailUpdate $expenseEmail
    ) {
        $this->helperService = $helperService;
        $this->userrepository = $userrepository;
        $this->expenseSettings = $expenseSettings;
        $this->expenseEmail=$expenseEmail;
        $this->expenseSettingsFinanceControllers =$expenseSettingsFinanceControllers;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $users_arr = $this->userrepository->getUsersDropdownList();
        $userslist = array();
        foreach ($users_arr as $key => $users) {
            $id=$users['id'];
            $userslist[$id]=$users['name'];
        }
        $expense_settings = $this->expenseSettings->first();
        $finance_controllers = $this->expenseSettingsFinanceControllers->pluck('financial_controller')->toArray();
        $reminderEmailInterval = $this->expenseEmail->first();
        return view('expense::admin.expense-settings', compact('expense_settings', 'userslist', 'finance_controllers', 'reminderEmailInterval'));
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
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            \DB::beginTransaction();
           
            $settings = $this->expenseSettings->first();
            if ($settings) {
                $this->expenseSettings->find($settings->id)->update(['sent_statement_attachment' => $request->sent_statement_attachment]);
            } else {
                $this->expenseSettings->insert(['sent_statement_attachment' => $request->sent_statement_attachment]);
            }
            
            $ids=$this->expenseSettingsFinanceControllers->pluck('id')->toArray();
            $this->expenseSettingsFinanceControllers->destroy($ids);
            if (isset($request['financial_controller'])) {
                for ($i = 0; $i < count($request['financial_controller']); $i++) {
                    $data['financial_controller'] = $request['financial_controller'][$i];
                    if ($data['financial_controller']!=0) {
                        $this->expenseSettingsFinanceControllers->create($data);
                    }
                }
            }
            $data['interval'] = $request['email_reminder'];
            if ($this->expenseEmail->first()!=null) {
                $this->expenseEmail->first()->update($data);
            } else {
                $this->expenseEmail->create($data);
            }
            // $emailIds=$this->expenseEmail->pluck('id')->toArray();
            // $this->expenseEmail->whereIn('id', $emailIds)->delete();
            // if (isset($request['email_reminder'])) {
            //     for ($i = 0; $i < count($request['email_reminder']); $i++) {
            //         $data['interval'] = $request['email_reminder'][$i];
            //         $this->expenseEmail->create($data);
            //     }
            // }
               
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
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
    public function destroy()
    {
    }
}
