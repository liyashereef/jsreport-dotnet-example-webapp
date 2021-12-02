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
        $chatData = $this->message->select(\DB::raw('*'))
            ->from(\DB::raw('(SELECT * FROM messages ORDER BY created_at DESC) t'))
            ->with('fromContact')
            ->where('to',\Auth::id())
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
        $chatData = $this->message->where('from',$from)->with('fromContact')->where('to',\Auth::id())->orderBy('created_at','DESC')->get();   
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
