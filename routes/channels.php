<?php

use App\Models\ChatsModel;
use App\Models\ClienteTarotistaModel;
use App\Models\LlamadasModel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $clienteTarotista = ClienteTarotistaModel::find($chatId);
    $userTarotista = $clienteTarotista->tarotista->fk_user;
    $userCliente = $clienteTarotista->cliente->fk_user;
    
    return ($userTarotista === $user->id || $userCliente === $user->id);
}, ['guards' => ['sanctum']]);

Broadcast::channel('llamada.{llamadaId}', function ($user, $llamadaId) {
    $llamada = LlamadasModel::find($llamadaId);
    if(!isset($llamada)){
        return false;
    }
    $userTarotista = $llamada->cliente_tarotista->tarotista->fk_user;
    $userCliente = $llamada->cliente_tarotista->cliente->fk_user;
    
    return ($userTarotista === $user->id || $userCliente === $user->id);
}, ['guards' => ['sanctum']]);
