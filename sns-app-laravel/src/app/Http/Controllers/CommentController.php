<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
                    'id' => $post->id,
                    'content' => $post->content,
                    'like_count' => $post->likes_count,
                    'is_liked' => $post->is_liked,
                    'created_at' => $post->created_at,
                    'user' => [
                        'name' => optional($post->user)->name ?? '匿名',
                        'uid' => optional($post->user)->firebase_uid ?? null,
                    ],
                ],
                'comments' => $comments,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Page not found'], 404);
        } catch (Exception $e) {
            Log::error('Comment Index Error: ' . $e->getMessage());
            return response()->json(['error' => '問題が発生しました。時間を置いて再度お試しください。'], 500);
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

            $validator = Validator::make($request->all(), [
                'comment' => 'required|string|max:120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'コメント送信中にエラーが発生しました。',
                ], 422);
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
                'error' => '問題が発生しました。時間を置いて再度お試しください。'
            ], 500);
        }
    }
}
