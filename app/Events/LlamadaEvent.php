<?php

namespace App\Events;

use App\Models\LlamadasModel;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LlamadaEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public LlamadasModel $llamada, public User $user, public array $payload = [])
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new PrivateChannel('llamada.' . $this->llamada->fk_cliente_tarotista);
    }

    public function broadcastAs()
    {
        return 'llamada.change';
    }
    
    public function broadcastWith()
    {
        return array_merge([
            'llamada' => $this->llamada,
        ], $this->payload);
    }
}
