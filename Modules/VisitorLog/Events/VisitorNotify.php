<?php

namespace Modules\VisitorLog\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VisitorNotify implements ShouldBroadcast
{
    use SerializesModels, Dispatchable, InteractsWithSockets;

    public $id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($customerId)
    {
        $this->id = $customerId;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('visitor-log.' . $this->id);
    }
}
