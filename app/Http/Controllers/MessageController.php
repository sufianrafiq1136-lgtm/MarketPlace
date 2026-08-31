<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Ad;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $conversations = Message::with(['sender', 'receiver', 'ad'])
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->latest()
            ->get()
            ->groupBy(fn (Message $message) => $message->ad_id.'-'.min($message->sender_id, $message->receiver_id).'-'.max($message->sender_id, $message->receiver_id))
            ->map(function ($messages) use ($user) {
                $latestMessage = $messages->sortByDesc('created_at')->first();
                $hasUnread = $messages->contains(function (Message $message) use ($user) {
                    return (int) $message->receiver_id === (int) $user->id && ! $message->is_read;
                });

                $latestMessage->setAttribute('has_unread', $hasUnread);

                return $latestMessage;
            })
            ->values();

        $selectedConversation = null;
        $selectedAd = null;
        $selectedUserId = null;

        if ($request->filled('ad') && $request->filled('user')) {
            $selectedAd = Ad::with('user', 'messages.sender', 'messages.receiver')
                ->findOrFail($request->integer('ad'));
            $selectedUserId = $request->integer('user');

            $isAdOwner = (int) $selectedAd->user_id === (int) $user->id;
            $isConversationParticipant = $selectedAd->messages->contains(function (Message $message) use ($user) {
                return (int) $message->sender_id === (int) $user->id
                    || (int) $message->receiver_id === (int) $user->id;
            });
            $isStartingChatWithSeller = (int) $selectedAd->user_id === (int) $selectedUserId;

            abort_unless($isAdOwner || $isConversationParticipant || $isStartingChatWithSeller, 403);

            $selectedConversation = Message::with(['sender', 'receiver', 'ad'])
                ->where('ad_id', $selectedAd->id)
                ->where(function ($query) use ($user, $selectedUserId) {
                    $query->where(function ($inner) use ($user, $selectedUserId) {
                        $inner->where('sender_id', $user->id)
                            ->where('receiver_id', $selectedUserId);
                    })->orWhere(function ($inner) use ($user, $selectedUserId) {
                        $inner->where('sender_id', $selectedUserId)
                            ->where('receiver_id', $user->id);
                    });
                })
                ->orderBy('created_at')
                ->get();

            Message::where('ad_id', $selectedAd->id)
                ->where('receiver_id', $user->id)
                ->where('sender_id', $selectedUserId)
                ->update(['is_read' => true]);
        }

        $unreadMessages = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('messages.index', compact(
            'conversations',
            'selectedConversation',
            'selectedAd',
            'selectedUserId',
            'unreadMessages',
        ));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'ad_id' => 'required|exists:ads,id',
            'message' => 'required|string|max:5000',
        ]);

        $ad = Ad::with('user')->findOrFail($validated['ad_id']);

        abort_if((int) $validated['receiver_id'] === (int) Auth::id(), 422, 'You cannot message yourself.');

        $isOwnerSending = (int) $ad->user_id === (int) Auth::id();
        $isBuyerMessagingSeller = (int) $validated['receiver_id'] === (int) $ad->user_id;
        $isExistingParticipant = Message::where('ad_id', $ad->id)
            ->where(function ($query) use ($validated) {
                $query->where(function ($inner) use ($validated) {
                    $inner->where('sender_id', Auth::id())
                        ->where('receiver_id', $validated['receiver_id']);
                })->orWhere(function ($inner) use ($validated) {
                    $inner->where('sender_id', $validated['receiver_id'])
                        ->where('receiver_id', Auth::id());
                });
            })
            ->exists();

        abort_unless($isBuyerMessagingSeller || $isOwnerSending && $isExistingParticipant || $isExistingParticipant, 403);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'ad_id' => $validated['ad_id'],
            'message' => $validated['message'],
        ]);

        $message->load(['sender', 'receiver', 'ad']);

        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Throwable $exception) {
            Log::warning('Realtime chat broadcast unavailable; message was still saved.', [
                'message_id' => $message->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => $message,
        ], 201);
    }
}
