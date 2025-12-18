<?php

namespace App\Events;

use App\Models\ChatsModel;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NuevoMensajeEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    
    public function __construct(public ChatsModel $chat, public User $user)
    {
        Log::info("Broadcast now enviado al canal private-chat.{$this->chat->fk_cliente_tarotista}");

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chat.'.$this->chat->fk_cliente_tarotista);
    }
    
    
    public function broadcastAs()
    {
        return 'mensaje.nuevo';
    }
}
