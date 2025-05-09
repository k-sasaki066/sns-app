<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\FirebaseService;
use Kreait\Firebase\Auth as FirebaseAuth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class VerifyFirebaseToken
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function handle($request, Closure $next)
    {
        $idToken = $request->bearerToken();
        // HTTP リクエストの Authorization ヘッダーから Bearer トークンを抽出

        try {
            // FirebaseService を通じて認証
            $verifiedIdToken = $this->firebaseService->verifyToken($idToken);

            $uid = $verifiedIdToken->claims()->get('sub');
            $userRecord = $this->firebaseService->getAuth()->getUser($uid);
            // Firebase UID を使ってユーザーを取得

            $name = $userRecord->displayName;

            $user = User::firstOrCreate(
                ['firebase_uid' => $uid],
                ['name' => $name]
            );
            auth()->login($user);

        } catch (\Throwable $e) {
            Log::error('Firebase token verification failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return $next($request);
    }
}
