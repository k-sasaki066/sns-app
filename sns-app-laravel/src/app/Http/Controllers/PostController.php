<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PostController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $post = $user->posts()->create([
                'content' => $request->input('content'),
            ]);

            $post = Post::with(['user:id,name,firebase_uid'])
            ->withCount('likes')
            ->withExists(['likes as is_liked' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->where('id', $post->id)
            ->first();
            
            $post->user->uid = $post->user->firebase_uid;
            $post->like_count = $post->likes_count;
            unset($post->likes_count);

            return response()->json([
                'post' => $post
            ], 201);
        } catch (Exception $e) {
            Log::error('Message Store Error: ' . $e->getMessage());

            return response()->json([
                'error' => '問題が発生しました。時間を置いて再度お試しください。'
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $limit = $request->input('limit', 20);
            $offset = $request->input('offset', 0);

            $posts = Post::with(['user:id,name,firebase_uid'])
                ->select('id', 'user_id', 'content', 'created_at')
                ->withCount('likes')
                ->withExists(['likes as is_liked' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }])
                ->latest()
                ->skip($offset)
                ->take($limit)
                ->get();

            return response()->json([
                'posts' => $posts->map(function ($post) {
                    $post->user->uid = $post->user->firebase_uid;
                    $post->like_count = $post->likes_count;
                    unset($post->likes_count);

                    return $post;
                }),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch messages: ' . $e->getMessage());

            return response()->json([
                'error' => '問題が発生しました。時間を置いて再度お試しください。'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $post = Post::findOrFail($id);

            // メッセージの所有者と現在のユーザーが一致するか確認
            if ($post->user_id !== $user->id) {
                return response()->json(['error' => 'Forbidden'], 403); // 自分以外のメッセージは削除できない
            }

            $post->delete();

            return response()->json(['success' => true]);
        } catch (ModelNotFoundException $e) {
            Log::warning('Message not found: ' . $e->getMessage());

            return response()->json(['error' => 'Message not found'], 404);
        } catch (Exception $e) {
            Log::error('Message delete error: ' . $e->getMessage());

            return response()->json(['error' => '問題が発生しました。時間を置いて再度お試しください。'], 500);
        }
    }
}
