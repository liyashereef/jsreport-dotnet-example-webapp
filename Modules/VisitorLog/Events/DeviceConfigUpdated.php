<?php

namespace Modules\VisitorLog\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeviceConfigUpdated implements ShouldBroadcast
{
    use SerializesModels, Dispatchable, InteractsWithSockets;

    public $id;
    public $payload;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($config)
    {
        $this->id = $config->uid;
        $this->payload = $config;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('visitor-log-device.' . $this->id);
    }
}
