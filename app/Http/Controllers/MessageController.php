<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Ad;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
            ->map(fn ($messages) => $messages->sortByDesc('created_at')->first())
            ->values();

        $selectedConversation = null;
        $selectedAd = null;
        $selectedUserId = null;

        if ($request->filled('ad') && $request->filled('user')) {
            $selectedAd = Ad::with('user', 'messages.sender', 'messages.receiver')
                ->findOrFail($request->integer('ad'));
            $selectedUserId = $request->integer('user');

            abort_unless(
                $selectedAd->user_id === $user->id
                || $selectedAd->messages->contains('sender_id', $user->id)
                || $selectedAd->messages->contains('receiver_id', $user->id),
                403
            );

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
                ->latest()
                ->get();

            Message::where('ad_id', $selectedAd->id)
                ->where('receiver_id', $user->id)
                ->where('sender_id', $selectedUserId)
                ->update(['is_read' => true]);
        } elseif ($conversations->isNotEmpty()) {
            $firstConversation = $conversations->first();
            $selectedAd = $firstConversation->ad;
            $selectedUserId = $firstConversation->sender_id === $user->id
                ? $firstConversation->receiver_id
                : $firstConversation->sender_id;

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
                ->latest()
                ->get();
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

        abort_if($ad->user_id === Auth::id(), 403, 'You cannot message yourself about your own ad.');

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'ad_id' => $validated['ad_id'],
            'message' => $validated['message'],
        ]);

        $message->load(['sender', 'receiver', 'ad']);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => $message,
        ], 201);
    }
}
