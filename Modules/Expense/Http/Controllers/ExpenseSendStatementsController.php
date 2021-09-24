<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\UserRepository;
use App\Services\HelperService;
use Modules\Expense\Models\ExpenseSendStatement;
use App\Repositories\AttachmentRepository;
use Auth;
use Modules\Admin\Models\User;
use App\Repositories\MailQueueRepository;
use Modules\Expense\Repositories\ExpenseClaimRepository;
use Modules\Expense\Models\ExpenseSettings;
use Carbon\Carbon;

class ExpenseSendStatementsController extends Controller
{
    public function __construct(
        UserRepository $userRepository,
        HelperService $helperService,
        ExpenseSendStatement $expenseSendstatements,
        AttachmentRepository $attachmentRepository,
        MailQueueRepository $mailQueueRepository,
        ExpenseClaimRepository $expenseClaimRepository
    ) {
        $this->userRepository = $userRepository;
        $this->helperService = $helperService;
        $this->expenseSendstatements = $expenseSendstatements;
        $this->attachmentRepository = $attachmentRepository;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->expenseClaimRepository = $expenseClaimRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $userLists = $this->userRepository->getUsersDropdownList(null, ['super_admin'],true);

        $users_arr = array();
        foreach ($userLists as $users) {
            $name = $users['name'];
            $id = $users['id'];
            $users_arr[$id] = $name;
        }
        $userList = $users_arr;

        $recentUserSendLists = ExpenseSendStatement::where('financial_controller_id', \Auth::id())
            ->with(['user' => function ($query) {
                $query->select('id', 'email', \DB::raw("CONCAT(first_name,' ',COALESCE(last_name,'')) as name"));
            }])->latest()->take(10)->get();

        $unique = $recentUserSendLists->unique('user_id');
        $recentUserLists = $unique->values()->all();

        $expenseSettings = ExpenseSettings::first();
        $expenseSettingValue = $expenseSettings->sent_statement_attachment;

        return view('expense::expense_send_statement', compact('userList', 'recentUserLists', 'expenseSettingValue'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    { }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if ($request->expense_value == 1) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'expense_send_statements' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf',
                ],
                [
                    'expense_send_statements.required' => 'Please upload a file',
                    'expense_send_statements.mimes' => 'Supported file format are jpeg,bmp,png,gif,svg,pdf'
                ]
            );

            if ($validator->fails()) {
                return \Response::json(array("errors" => $validator->getMessageBag()->toArray()), 422);
            } else {
                $file_upload = $this->attachmentRepository->saveAttachmentFile('expense-send-statements', $request, 'expense_send_statements');
                $send_statements['attachment_id'] = $file_upload['file_id'];
                $send_statements['user_id'] = $request->user_id;
                $send_statements['financial_controller_id'] =  Auth::id();

                $expenseSendStatement =   ExpenseSendStatement::create($send_statements);

                $lastSavedId = $expenseSendStatement->id;
                $expenseSendStatementDetails = ExpenseSendStatement::where('id', $lastSavedId)->first();

                $toGetUserEmail = User::where('id', $expenseSendStatementDetails->user_id)->first();
                $to   = $toGetUserEmail->email;
                $attachment_id = $expenseSendStatementDetails->attachment_id;
                $model_name = 'Expense statements';
                $subject = 'Expense Statement';
                $message = 'Hi, <br> You received an expense statement.';

                $mail_queue = $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name, null, null, null, null, null, $attachment_id);

                return response()->json($this->helperService->returnTrueResponse());
            }
        } else {
            if ($request->expense_send_statements != null) {
                $file_upload = $this->attachmentRepository->saveAttachmentFile('expense-send-statements', $request, 'expense_send_statements');
                $send_statements['attachment_id'] = $file_upload['file_id'];
            }
            $send_statements['user_id'] = $request->user_id;
            $send_statements['financial_controller_id'] =  Auth::id();

            $expenseSendStatement =   ExpenseSendStatement::create($send_statements);

            $lastSavedId = $expenseSendStatement->id;
            $expenseSendStatementDetails = ExpenseSendStatement::where('id', $lastSavedId)->first();

            $toGetUserEmail = User::where('id', $expenseSendStatementDetails->user_id)->first();
            $to   = $toGetUserEmail->email;
            $attachment_id = ($expenseSendStatementDetails->attachment_id) ? $expenseSendStatementDetails->attachment_id : '';
            $model_name = 'Expense send statements';
            $subject = 'Expense Statement';
            $message = 'Hi, <br> You received an expense statement.';

            $mail_queue = $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name, null, null, null, null, null, $attachment_id);

            return response()->json($this->helperService->returnTrueResponse());
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('expense::expense_send_log_details');
    }

    public function getList()
    {
        return datatables()->of($this->expenseClaimRepository->expenseSendLog())->addIndexColumn()->toJson();
    }
    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('expense::edit');
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
      // $removings =  \DB::table('expense_send_statements')->where('user_id', $id)->delete();
       $removings= ExpenseSendStatement::where('user_id', $id)
          ->update(['deleted_at' => $date]);
       return response()->json(array(
        'success' => true,
        'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Succesfully removed.</div>'
    ));

    }
}
