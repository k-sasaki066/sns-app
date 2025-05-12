<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $message = Post::with('user')->findOrFail($id)->append(['like_count', 'is_liked']);

        $comments = Comment::where('post_id', $id)
        ->with('user:id,name')
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'messages' => [
                [
                    'id' => $message->id,
                    'content' => $message->content,
                    'like_count' => $message->like_count,
                    'is_liked' => $message->is_liked,
                    'user' => [
                        'name' => optional($message->user)->name ?? '匿名',
                        'uid' => optional($message->user)->firebase_uid ?? null,
                    ],
                ],
            ],
            'comments' => $comments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $comment = Comment::create([
                'post_id' => $id,
                'user_id' => $user->id,
                'comment' => $request->comment,
            ]);

            return response()->json([
                'comment' => $comment->load('user:id,name'),
            ], 201);
        } catch (Exception $e) {
            Log::error('Comment Store Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'コメントの保存中にエラーが発生しました'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
