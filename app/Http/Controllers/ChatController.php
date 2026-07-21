<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function conversations(Request $request)
    {
        $userId = $request->user()->id;

        // Find all unique users this person has chatted with
        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $conversations = [];
        $seenUserIds = [];

        foreach ($messages as $msg) {
            $otherUserId = $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
            
            if (!in_array($otherUserId, $seenUserIds)) {
                $seenUserIds[] = $otherUserId;
                $otherUser = User::find($otherUserId);
                if ($otherUser) {
                    $conversations[] = [
                        'user' => $otherUser,
                        'lastMessage' => $msg,
                        'unreadCount' => Message::where('sender_id', $otherUserId)
                            ->where('receiver_id', $userId)
                            ->whereNull('read_at')
                            ->count()
                    ];
                }
            }
        }

        return response()->json($conversations);
    }

    public function history(Request $request, User $user)
    {
        $userId = $request->user()->id;
        $otherUserId = $user->id;

        // Mark unread messages as read
        Message::where('sender_id', $otherUserId)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where(function($q) use ($userId, $otherUserId) {
                $q->where('sender_id', $userId)->where('receiver_id', $otherUserId);
            })
            ->orWhere(function($q) use ($userId, $otherUserId) {
                $q->where('sender_id', $otherUserId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $user->id,
            'content' => $validated['content'],
        ]);

        return response()->json($message, 201);
    }
}
