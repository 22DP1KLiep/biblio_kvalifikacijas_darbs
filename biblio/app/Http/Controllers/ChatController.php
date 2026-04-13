<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Notification;

class ChatController extends Controller
{
    /**
     * Chats list (sidebar)
     */
    public function index(Request $request)
{
    $user = $request->user();

    return Inertia::render('Chats/Index', [
        ...$this->sidebarData($user),
        'activeConversationId' => null,
    ]);
}


    /**
     * Show single chat
     */
    public function show(Request $request, Conversation $conversation)
    {

        $user = $request->user();

        // security check
        if (! $conversation->isMember($user)) {
            abort(403);
        }

        $conversation->users()->updateExistingPivot(
            $user->id,
            ['last_read_at' => now()]
        );


        $conversation->users()->updateExistingPivot(
        $user->id,
        ['last_read_at' => now()]
        );

        // chat title
        if ($conversation->isPrivate()) {
            $otherUser = $conversation->users
                ->firstWhere('id', '!=', $user->id);

            $title = $otherUser?->username ?? 'Private chat';
        } else {
            $title = $conversation->title;
        }

        // messages (read-only)
        $messages = $conversation->messages()
            ->with('user:id,username')
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'username' => $message->user->username,
                    'isMine' => $message->user_id === $user->id,
                    'created_at' => $message->created_at->format('H:i'),
                ];
            });

        $sidebar = $this->sidebarData($user);

        return Inertia::render('Chats/Show', [
            'conversationId' => $conversation->id,
            'title' => $title,
            'type' => $conversation->type,
            'messages' => $messages,
            ...$sidebar,
        ]);
    }

    /**
     * Store new message
     */
    public function storeMessage(Request $request, Conversation $conversation)
{
    $user = $request->user();

    if (! Message::canSend($user, $conversation)) {
        abort(403);
    }

    $validated = $request->validate([
        'body' => 'required|string|max:2000',
    ]);

    $message = Message::create([
        'conversation_id' => $conversation->id,
        'user_id' => $user->id,
        'body' => $validated['body'],
    ]);

    // 🔔 IZVEIDO NOTIFIKĀCIJAS VISIEM CITIEM DALĪBNIEKIEM
    foreach ($conversation->users as $participant) {
        if ($participant->id !== $user->id) {

            Notification::create([
                'user_id' => $participant->id,
                'from_user_id' => $user->id,
                'type' => 'message',
                'data' => $conversation->id,
            ]);
        }
    }

    return redirect()->back();
}

    /**
     * Sidebar data (private chats + channels)
     */
   private function sidebarData($user)
{
    $conversations = $user->conversations()
        ->with(['users', 'messages'])
        ->get();

    $privateChats = [];
    $channels = [];

    foreach ($conversations as $conversation) {

        $pivot = $conversation->users
            ->firstWhere('id', $user->id)
            ->pivot;

        $lastReadAt = $pivot->last_read_at;

        $unreadCount = $conversation->messages
            ->where('user_id', '!=', $user->id)
            ->filter(function ($message) use ($lastReadAt) {
                if (!$lastReadAt) return true;
                return $message->created_at > $lastReadAt;
            })
            ->count();

        if ($conversation->isPrivate()) {

            $otherUser = $conversation->users
                ->firstWhere('id', '!=', $user->id);

            if ($otherUser) {
                $privateChats[] = [
                    'id' => $conversation->id,
                    'username' => $otherUser->username,
                    'unread' => $unreadCount,
                ];
            }
        }

        if ($conversation->isChannel()) {

            $channels[] = [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'unread' => $unreadCount,
            ];
        }
    }

    return [
        'privateChats' => $privateChats,
        'channels' => $channels,
    ];
}





    public function start(Request $request, \App\Models\User $user)
{
    $authUser = $request->user();

    // nevar sākt čatu ar sevi
    if ($authUser->id === $user->id) {
        return redirect()->back();
    }

    // meklē vai jau eksistē private čats starp šiem 2
    $existingConversation = \App\Models\Conversation::where('type', 'private')
        ->whereHas('users', function ($q) use ($authUser) {
            $q->where('user_id', $authUser->id);
        })
        ->whereHas('users', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->first();

    if ($existingConversation) {
        return redirect()->route('chats.show', $existingConversation->id);
    }

    // izveido jaunu private conversation
    $conversation = \App\Models\Conversation::create([
        'type' => 'private',
        'title' => null,
        'owner_id' => $authUser->id,
        'join_type' => 'open',
    ]);

    // piesaista abus
    $conversation->users()->attach($authUser->id, ['role' => 'member']);
    $conversation->users()->attach($user->id, ['role' => 'member']);

    return redirect()->route('chats.show', $conversation->id);
}

public function new(Request $request)
{
    $authUser = $request->user();

    $users = \App\Models\User::where('id', '!=', $authUser->id)
        ->where('username', '!=', 'welcomebot')
        ->select('id', 'username')
        ->orderBy('username')
        ->get();

    

    return Inertia::render('Chats/New', [
        'users' => $users,
        ...$this->sidebarData($authUser),
    ]);
}

}
