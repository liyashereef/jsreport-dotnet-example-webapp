<?php

namespace App\Events;

use Modules\Chat\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
//use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Modules\Chat\Models\ChatContacts;

class UpdateContact implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id,$contact_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id,$contact_id)
    {
        $this->user_id = $user_id;
        $this->contact_id = $contact_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('updateContact.' . $this->user_id);
    }

     public function broadcastWith()
     {
        $contacts = ChatContacts::with('contact.employee')->where('user_id',$this->user_id)->where('contact_id', '=', $this->contact_id)->get();

         return ["contacts" =>  $contacts];
       //  return response()->json($contacts);
     }

     public function broadcastAs()
     {
            return 'contact';
     }

}