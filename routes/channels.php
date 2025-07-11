<?php

use App\Models\ChatsModel;
use App\Models\LlamadasModel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = ChatsModel::find($chatId);
    if(!isset($chat)){
        return false;
    }
    $userTarotista = $chat->cliente_tarotista->tarotista->fk_user;
    $userCliente = $chat->cliente_tarotista->cliente->fk_user;
    
    return ($userTarotista === $user->id || $userCliente === $user->id);
});

Broadcast::channel('llamada.{llamadaId}', function ($user, $llamadaId) {
    $llamada = LlamadasModel::find($llamadaId);
    if(!isset($llamada)){
        return false;
    }
    $userTarotista = $llamada->cliente_tarotista->tarotista->fk_user;
    $userCliente = $llamada->cliente_tarotista->cliente->fk_user;
    
    return ($userTarotista === $user->id || $userCliente === $user->id);
});
