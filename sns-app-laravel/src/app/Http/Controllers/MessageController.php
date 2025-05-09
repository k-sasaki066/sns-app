<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Services\FirebaseService;

class MessageController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function store(Request $request)
    {
        $user = auth()->user(); // Laravelの認証情報から取得

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $message = $user->messages()->create([
            'content' => $request->input('content'),
        ]);

        return response()->json(['message' => $message->load('user')], 201);
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 20);
        $offset = $request->input('offset', 0);

        $messages = Message::with('user')
            ->latest()
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json([
            'messages' => $messages
        ]);
    }
}
