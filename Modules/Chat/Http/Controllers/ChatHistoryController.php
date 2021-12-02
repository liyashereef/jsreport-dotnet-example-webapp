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
      //  $user = \Auth::user();
    //  $rr = [1,2,3];
    //    $user = User::where('id', 1)->first()->toArray();
    $user = \Auth::user();
    if ($user->can('view_all_customer_qrcode_summary')) {
        $user_list = $this->userRepository->getUserLookup(null,['admin','super_admin'],null,true,null,true)
        ->orderBy('first_name', 'asc')->get();
        $project_list = $this->customerRepository->getProjectsDropdownList('all');
    }else if($user->can('view_allocated_customer_qrcode_summary')){
        $employees = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id);
        $user_list = $this->usermodel
        ->whereIn('id',$employees)->get();
        $project_list = $this->customerRepository->getProjectsDropdownList('allocated');
    }else{
        $user_list = [];
        $project_list = [];
    }
     return view('chat::view-history', compact('user_list', 'project_list'));
    }

    public function getChatHistoryList()
    {
            $chats = Message::select(\DB::raw('*'))
            ->from(\DB::raw('(SELECT * FROM messages ORDER BY created_at DESC) t'))
            ->with('fromContact')
            ->where('to',\Auth::id())
            //->orWhere('from',\Auth::id())
            ->get()
            ->groupBy('from');
            foreach($chats as $eachChat)
        {
            $latestRecord[]=$eachChat[0];
        }
                 // Message::select('*')
                 // ->groupBy('from')->with('fromContact')->where('to', \auth()->id())->orderby('created_at','DESC')->get();
        return datatables()->of($this->prepareHistoryList($latestRecord))->addIndexColumn()->toJson();
    }

    public function prepareHistoryList($chats)
    {

        $datatable_rows = array();
        foreach ($chats as $key => $each_chat) {
            $each_row["from_id"] = $each_chat->from;
            $each_row["date"] = $each_chat->created_at->format('Y-m-d');
            $each_row["time"] = $each_chat->created_at->format('h:i A');
            $each_row["text"] = $each_chat->text;
            $each_row["from"] = $each_chat->fromContact->full_name;
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
            $text='Sent'; 
            if($id==$each_chat->from)
            {
              $text='Recieved';  
            }
            $each_row["text"] = $text .' : '.$each_chat->text;
            $each_row["type"] = $each_chat->type==0?'Chat':'Text';
            array_push($datatable_rows, $each_row);
        }
         return datatables()->of($datatable_rows)->addIndexColumn()->toJson();
    }
}
