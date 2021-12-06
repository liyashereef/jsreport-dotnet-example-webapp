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
        $chatData = $this->message->select('id','from','text','created_at')
        ->with(['fromContact' => function($query){
           $query->select('id','first_name', 'last_name');
             }])
            ->from(\DB::raw('(SELECT * FROM messages ORDER BY created_at DESC) t'))
            ->where('to',\Auth::id())
            //->orWhere('from',\Auth::id())
            ->get()
            ->groupBy('from');
        foreach($chatData as $eachChat)
        {
            $latestRecord[]=$eachChat[0];
        }

        if (count($chatData)) {
            $successcontent['success'] = true;
            $successcontent['message'] = 'Retrieved successfully';
            $successcontent['data'] = $latestRecord;
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
        $from = $request->input('from');
        $chatData = $this->message->select('id','from','text','created_at')->with(['fromContact' => function($query){
           $query->select('id','first_name', 'last_name');
             }])->where('from',$from)->orWhere('to',$from)->where('to',\Auth::id())->orderBy('created_at','DESC')->get();   
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
}
