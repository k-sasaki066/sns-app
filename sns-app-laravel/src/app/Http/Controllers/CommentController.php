<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Events\CommentSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $post = Post::with(['user:id,name,firebase_uid', 'comments.user:id,name'])
                ->select('id', 'user_id', 'content', 'created_at')
                ->withCount('likes')
                ->withExists(['likes as is_liked' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }])
                ->findOrFail($id);
            
            $comments = $post->comments;

            return response()->json([
                'posts' => [
                    [
                        'id' => $post->id,
                        'content' => $post->content,
                        'like_count' => $post->like_count,
                        'is_liked' => $post->is_liked,
                        'user' => [
                            'name' => optional($post->user)->name ?? '匿名',
                            'uid' => optional($post->user)->firebase_uid ?? null,
                        ],
                    ],
                ],
                'comments' => $comments,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (Exception $e) {
            Log::error('Comment Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
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

            broadcast(new CommentSent($comment));

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
}
