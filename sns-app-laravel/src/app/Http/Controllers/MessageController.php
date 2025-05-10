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

class MessageController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user(); // Laravelの認証情報から取得

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $message = $user->messages()->create([
                'content' => $request->input('content'),
            ]);

            $message->load('user');
            $message->user->uid = $message->user->firebase_uid;

            return response()->json([
                'message' => $message->append(['like_count', 'is_liked'])
            ], 201);
        } catch (Exception $e) {
            Log::error('Message Store Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'メッセージの保存中にエラーが発生しました'
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 20);
            $offset = $request->input('offset', 0);

            $messages = Post::with('user')
                ->latest()
                ->skip($offset)
                ->take($limit)
                ->get()
                ->append(['like_count', 'is_liked']);

            return response()->json([
                'messages' => $messages->map(function ($message) {
                    $message->user->uid = $message->user->firebase_uid;
                    return $message;
                }),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch messages: ' . $e->getMessage());

            return response()->json([
                'error' => 'メッセージの取得中にエラーが発生しました'
            ], 500);
        }
    }

    public function toggleFavorite($id, Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
            }

            $post = Post::findOrFail($id);

            DB::beginTransaction();

            // ユーザーがすでにいいねしているか確認
            $like = $post->likes()->where('user_id', $user->id)->first();

            if ($like) {
                // いいねを解除
                $like->delete();
                $isLiked = false;
            } else {
                // いいねを登録
                $post->likes()->create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]);
                $isLiked = true;
            }

            DB::commit();

            // 新しいいいねの数を返す
            return response()->json([
                'success' => true,
                'is_liked' => $isLiked,
                'like_count' => $post->likes()->count(), // 最新の like_count を返す
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Post not found'], 404);
        } catch (Exception $e) {
            DB::rollBack(); // エラー時にロールバック
            Log::error('Toggle Favorite Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth()->user();
            $message = Post::findOrFail($id);

            // メッセージの所有者と現在のユーザーが一致するか確認
            if ($message->user_id !== $user->id) {
                return response()->json(['error' => 'Forbidden'], 403); // 自分以外のメッセージは削除できない
            }

            $message->delete();

            return response()->json(['success' => true]);
        } catch (ModelNotFoundException $e) {
            Log::warning('Message not found: ' . $e->getMessage());

            return response()->json(['error' => 'Message not found'], 404);
        } catch (Exception $e) {
            Log::error('Message delete error: ' . $e->getMessage());

            return response()->json(['error' => 'メッセージの削除中にエラーが発生しました'], 500);
        }
    }
}
