<?php

namespace App\Http\Controllers\Siakad;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * List chat rooms for the current user with last message preview.
     */
    public function index(Request $request): JsonResponse
    {
        $rooms = ChatRoom::withCount('messages')
            ->with(['messages' => function ($query) {
                $query->latest()->limit(1)->with('user:id,name,avatar_url');
            }])
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($room) {
                $lastMessage = $room->messages->first();
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'messages_count' => $room->messages_count,
                    'last_message' => $lastMessage ? [
                        'content' => $lastMessage->content,
                        'user' => $lastMessage->user?->name,
                        'created_at' => $lastMessage->created_at,
                    ] : null,
                ];
            });

        return response()->json($rooms);
    }

    /**
     * Get all messages for a room, paginated.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $room = ChatRoom::findOrFail($id);
        $messages = $room->messages()
            ->with('user:id,name,avatar_url')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return response()->json([
            'room' => $room,
            'messages' => $messages,
        ]);
    }

    /**
     * Create new message and broadcast via Laravel Reverb.
     */
    public function store(Request $request, $id): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $room = ChatRoom::findOrFail($id);

        $message = Message::create([
            'chat_room_id' => $room->id,
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'type' => $request->input('type', 'text'),
        ]);

        $message->load('user:id,name,avatar_url');

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }

    /**
     * Create a new chat room.
     */
    public function createRoom(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $room = ChatRoom::create([
            'name' => $request->name,
        ]);

        return response()->json($room, 201);
    }
}
