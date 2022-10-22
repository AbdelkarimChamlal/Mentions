<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResourceUpdateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    public $reousrce;
    public $action;
    public $id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $resource, $action, $id)
    {
        $this->user = $user;
        $this->resource = $resource;
        $this->action = $action;
        $this->id = $id;
    }

    public function broadcastAs()
    {
        return 'update';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel($this->user->channel_id);
    }
}
