<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification;

class ChatController extends Controller
{
    /**
     * Attēlo lietotāja čatu sarakstu.
     * Tiek ielādēti sidebar dati ar privātajiem čatiem.
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
     * Atver konkrētu čatu un ielādē tā ziņas.
     */
    public function show(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        // Pārbauda, vai lietotājs ir čata dalībnieks
        if (!$conversation->isMember($user)) {
            abort(403);
        }

        // Atzīmē ziņas kā izlasītas
        $conversation->users()->updateExistingPivot(
            $user->id,
            ['last_read_at' => now()]
        );

        // Nosaka čata nosaukumu
        if ($conversation->isPrivate()) {
            $otherUser = $conversation->users
                ->firstWhere('id', '!=', $user->id);

            $title = $otherUser?->username ?? 'Private chat';
        } else {
            $title = $conversation->title;
        }

        // Iegūst ziņas no datubāzes
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
     * Saglabā jaunu ziņu čatā.
     */
    public function storeMessage(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        // Pārbauda, vai lietotājs drīkst sūtīt ziņu
        if (!Message::canSend($user, $conversation)) {
            abort(403);
        }

        // Validē ievadīto tekstu
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        // Izveido ziņu datubāzē
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        // Izveido paziņojumus citiem čata dalībniekiem
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
     * Sagatavo sidebar datus (privātie čati).
     */
    private function sidebarData($user)
    {
        $conversations = $user->conversations()
            ->with(['users', 'messages'])
            ->get();

        $privateChats = [];

        foreach ($conversations as $conversation) {

            // Iegūst informāciju par pēdējo izlasīto ziņu
            $pivot = $conversation->users
                ->firstWhere('id', $user->id)
                ->pivot;

            $lastReadAt = $pivot->last_read_at;

            // Aprēķina neizlasīto ziņu skaitu
            $unreadCount = $conversation->messages
                ->where('user_id', '!=', $user->id)
                ->filter(function ($message) use ($lastReadAt) {
                    if (!$lastReadAt) return true;
                    return $message->created_at > $lastReadAt;
                })
                ->count();

            // Apstrādā privātos čatus
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
        }

        return [
            'privateChats' => $privateChats,
        ];
    }

    /**
     * Uzsāk jaunu privāto čatu.
     */
    public function start(Request $request, \App\Models\User $user)
    {
        $authUser = $request->user();

        // Neļauj sākt čatu ar sevi
        if ($authUser->id === $user->id) {
            return redirect()->back();
        }

        // Pārbauda, vai čats jau eksistē
        $existingConversation = \App\Models\Conversation::where('type', 'private')
            ->whereHas('users', fn($q) => $q->where('user_id', $authUser->id))
            ->whereHas('users', fn($q) => $q->where('user_id', $user->id))
            ->first();

        if ($existingConversation) {
            return redirect()->route('chats.show', $existingConversation->id);
        }

        // Izveido jaunu čatu
        $conversation = \App\Models\Conversation::create([
            'type' => 'private',
            'title' => null,
            'owner_id' => $authUser->id,
            'join_type' => 'open',
        ]);

        // Pievieno lietotājus čatam
        $conversation->users()->attach($authUser->id, ['role' => 'member']);
        $conversation->users()->attach($user->id, ['role' => 'member']);

        return redirect()->route('chats.show', $conversation->id);
    }

    /**
     * Attēlo jauna čata izveides lapu.
     */
    public function new(Request $request)
    {
        $authUser = $request->user();

        $followingIds = $authUser->following()->pluck('following_id');

        $users = \App\Models\User::whereIn('id', $followingIds)
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