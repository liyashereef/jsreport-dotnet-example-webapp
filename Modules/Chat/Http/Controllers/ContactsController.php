<?php

namespace Modules\Chat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Chat\Models\Message;
use Modules\Admin\Models\User;
use Modules\Chat\Models\ChatContacts;
use Auth;
use App\Services\HelperService;

use App\Events\NewMessage;

class ContactsController extends Controller
{


    public function __construct(HelperService $helperService)
    {
        $this->helperService = $helperService;
    }

    public function get()
    {
        $contacts = ChatContacts::with('contact.employee')->where('user_id',auth()->id())->where('contact_id', '!=', auth()->id())->get();
       //  $contacts = User::with('employee')->where('id', '!=', auth()->id())->where('id','<',10)->get();
       $unreadIds = Message::select(\DB::raw('`from` as sender_id, count(`from`) as messages_count'))
       ->where('to', auth()->id())
       ->where('read', false)
       ->groupBy('from')
       ->get();
       $contacts = $contacts->map(function ($contact) use ($unreadIds) {
            $contactUnread = $unreadIds->where('sender_id', $contact->contact_id)->first();
            $contact->unread = $contactUnread ? $contactUnread->messages_count : 0;
            return $contact;
        });

        return response()->json($contacts);
    }

    public function getMessagesFor($id)
    {
        // mark all messages with the selected contact as read
        Message::where('from', $id)->where('to', \auth()->id())->update(['read' => true]);

        // get all messages between the authenticated user and the selected user
        $messages = Message::where(function ($q) use ($id) {
            $q->where('from', \auth()->id());
            $q->where('to', $id);
        })->orWhere(function ($q) use ($id) {
            $q->where('from', $id);
            $q->where('to', \auth()->id());
        })
            ->get();
        return response()->json($messages);
    }

    public function send(Request $request)
    {

        $message = Message::create([
            'from' => auth()->id(),
            'to' => $request->contact_id,
            'text' => $request->text
        ]);
        broadcast(new NewMessage($message));
        $contacts = ChatContacts::firstOrCreate([
            'user_id' => $request->contact_id,
            'contact_id' => auth()->id(),
        ]);
       // return response()->json($message);
       return response()->json($this->helperService->returnTrueResponse());
    }

    public function saveForApp(Request $request)
    {
        $message = Message::create([
            'from' => $request->from,
            'to' => $request->to,
            'text' => $request->text
        ]);
        return response()->json($message);
    }

    public function store(Request $request)
    {
      //  dd($request->all());
        try {
            \DB::beginTransaction();
            $contact_id = (int) $request->get('contact_id');
            if($contact_id != 0){
                $contacts = ChatContacts::firstOrCreate([
                    'user_id' => auth()->id(),
                    'contact_id' => $contact_id,
                ]);
            }
            \DB::commit();
            if ($contacts) {
                return response()->json($this->helperService->returnTrueResponse());
            } else {
                return response()->json($this->helperService->returnFalseResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
