<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class LikeController extends Controller
{
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
            return response()->json(['error' => '問題が発生しました。時間を置いて再度お試しください。'], 500);
        }
    }
}
