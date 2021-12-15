<?php

namespace Modules\Chat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Chat\Models\Message;
use App\Events\MessageSent;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;


class ChatHistoryController extends Controller
{


    public function __construct(CustomerRepository $customerRepository, UserRepository $userRepository, EmployeeAllocationRepository $employeeAllocationRepository)
    {

        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
    }
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
    //  $rr = [1,2,3];
    //    $user = User::where('id', 1)->first()->toArray();
     return view('chat::view-history');
    }

    public function getChatHistoryList()
    {
            $recievedChat = Message::select(\DB::raw('*'))
            ->from(\DB::raw('(SELECT * FROM messages ORDER BY created_at DESC) t'))
            ->with('fromContact')
            ->where('to',\Auth::id())
            ->get()
            ->groupBy('from');
             foreach($recievedChat as $eachChat)
        {
            $latestRecord[]=$eachChat->first();
            $from_arr[]=$eachChat->first()['from'];
        } 
            $sendChat = Message::select(\DB::raw('*'))
            ->from(\DB::raw('(SELECT * FROM messages ORDER BY created_at DESC) t'))
            ->with('toContact')
            ->where('from',\Auth::id())
            ->whereNotIn('to',$from_arr)
            ->get()
            ->groupBy('to');
           foreach($sendChat as $eachChat)
        {
            $latestRecord[]=$eachChat->first();
        }
        return datatables()->of($this->prepareHistoryList($latestRecord))->addIndexColumn()->toJson();
    }

    public function prepareHistoryList($chats)
    {

        $datatable_rows = array();
        foreach ($chats as $key => $each_chat) {
            $each_row["from_id"] = $each_chat->from!=\Auth::id()?$each_chat->from:$each_chat->to;
            $each_row["date"] = $each_chat->created_at->format('Y-m-d');
            $each_row["time"] = $each_chat->created_at->format('h:i A');
            $each_row["text"] = $each_chat->text;
            $each_row["from"] =$each_chat->from!=\Auth::id()?$each_chat->fromContact->full_name:$each_chat->toContact->full_name;
            $each_row["employee_no"] = $each_chat->from!=\Auth::id()?$each_chat->fromContact->employee->employee_no:$each_chat->toContact->employee->employee_no;
            $each_row["id"] = $each_chat->id;
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function getChatList($id)
    {
         $chats = Message::with('fromContact')->where('to', \auth()->id())->where('from',$id)->orWhere('to',$id)->orderBy('created_at','DESC')->get();
         $datatable_rows = array();
         foreach ($chats as $key => $each_chat) {
            $each_row["date"] = $each_chat->created_at->format('Y-m-d');
             $each_row["id"] = $each_chat->id;
            $each_row["time"] = $each_chat->created_at->format('h:i A');
            $icon='<i class="fas fa-arrow-circle-left fa-lg" style="vertical-align: middle;color: #2b8511;" ></i>'; 
            if($id==$each_chat->from)
            {
              $icon='<i class="fas fa-arrow-circle-right fa-lg" style="vertical-align: middle;color: #c93812;"></i>';  
            }
            $each_row["text"] = $icon . '  ' .$each_chat->text;
            $each_row["type"] = $each_chat->type==0?'Chat':'Text';
            array_push($datatable_rows, $each_row);
        }
         return datatables()->of($datatable_rows)->addIndexColumn()->escapeColumns('text')->toJson();
    }
}
