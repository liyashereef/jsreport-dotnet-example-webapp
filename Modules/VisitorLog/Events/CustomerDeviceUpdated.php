<?php

namespace Modules\VisitorLog\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CustomerDeviceUpdated implements ShouldBroadcast
{
    use SerializesModels, Dispatchable, InteractsWithSockets;

    public $id;
    public $payload;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->id = $payload['config']->customer_id;
        $this->payload = $payload;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('visitor-log.' . $this->id);
    }
}
