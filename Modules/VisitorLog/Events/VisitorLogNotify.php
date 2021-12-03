<?php

namespace Modules\VisitorLog\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VisitorLogNotify implements ShouldBroadcast
{
    use SerializesModels, Dispatchable, InteractsWithSockets;

    public $id;
    public $origin;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($customerId,$deviceId)
    {
        $this->id = $customerId;
        $this->origin = $deviceId;
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
