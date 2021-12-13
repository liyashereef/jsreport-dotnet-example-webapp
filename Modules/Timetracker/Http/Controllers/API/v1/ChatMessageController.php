<?php

namespace Modules\Timetracker\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Chat\Models\Message;

class ChatMessageController extends Controller
{
    protected $message;
    public function __construct()
    {
        $this->message = new Message();
    }
    public function getAllChat()
    {
        $chatData = $this->message->select('id','from','to','text','created_at')
        ->with(['fromContact' => function($query){
           $query->select('id','first_name', 'last_name');
             }])
            ->from(\DB::raw('(SELECT * FROM messages ORDER BY created_at DESC) t'))
            ->where('to',\Auth::id())    
            ->get();     
        $chatData =$chatData->groupBy('from');
        $unreadIds = Message::select(\DB::raw('`from` as sender_id, count(`from`) as messages_count'))
       ->where('to', auth()->id())
       ->where('read', false)
       ->groupBy('from')
       ->get();
       $chatData = $chatData->map(function ($eachChat) use ($unreadIds) {
            $eachChat=$eachChat->first();
            $contactUnread = $unreadIds->where('sender_id', $eachChat->from)->first();
            $eachChat->unread = $contactUnread ? $contactUnread->messages_count : 0;
            return $eachChat;
        });
       foreach($chatData as $each_chat)
       {
        $result[]=$each_chat;
       }
        if (count($result)) {
            $successcontent['success'] = true;
            $successcontent['message'] = 'Retrieved successfully';
            $successcontent['data'] = $result;
            $successcontent['code'] = 200;
        } else {
            $successcontent['success'] = false;
            $successcontent['message'] = 'No Data';
            $successcontent['code'] = 406;
        }

        return response()->json($successcontent);
    }

    public function getPersonalChat(Request $request)
    {
        $id = $request->input('from');
        $chatData = $this->message->select('id','from','to','text','created_at')->with(['fromContact' => function($query){
           $query->select('id','first_name', 'last_name');
             }])
        ->where(function ($q) use ($id) {
            $q->where('from', \auth()->id());
            $q->where('to', $id);
        })->orWhere(function ($q) use ($id) {
            $q->where('from', $id);
            $q->where('to', \auth()->id());
        })
        ->orderBy('id','ASC')->get();   
        if (count($chatData)) {
            $successcontent['success'] = true;
            $successcontent['message'] = 'Retrieved successfully';
            $successcontent['data'] = $chatData;
            $successcontent['code'] = 200;
        } else {
            $successcontent['success'] = false;
            $successcontent['message'] = 'No Data';
            $successcontent['code'] = 406;
        }

        return response()->json($successcontent);
    }

     public function updateReadStatus(Request $request)
    {
       $id = $request->input('from');
       $update=$this->message->where('from', $id)->where('to', \auth()->id())->update(['read' => true]);
        if ($update) {
            $successcontent['success'] = true;
            $successcontent['message'] = 'Read status updated successfully';
            $successcontent['code'] = 200;
        } else {
            $successcontent['success'] = false;
            $successcontent['message'] = 'Something went wrong';
            $successcontent['code'] = 406;
        }
         return response()->json($successcontent);
    }
}
