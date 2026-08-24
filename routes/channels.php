<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Ad;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('messages.ad.{adId}', function ($user, $adId) {
    $ad = Ad::with('messages')->find($adId);

    if (! $ad) {
        return false;
    }

    return (int) $user->id === (int) $ad->user_id
        || $ad->messages->contains('sender_id', $user->id)
        || $ad->messages->contains('receiver_id', $user->id);
});
