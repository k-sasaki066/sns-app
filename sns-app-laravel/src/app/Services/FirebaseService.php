<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Auth\UserNotFound;

class FirebaseService
{
    protected Auth $auth;

    public function __construct(string $credentialsPath, string $projectId)
    {
        $this->auth = (new Factory)
            ->withServiceAccount($credentialsPath)
            ->withProjectId($projectId)
            ->createAuth();
    }

    public function verifyToken(string $idToken)
    {
        return $this->auth->verifyIdToken($idToken);
    }

    public function getUserByUid(string $uid)
    {
        try {
            $user = $this->auth->getUser($uid);
            return [
                'firebase_uid' => $user->uid,
                'name' => $user->displayName ?? '匿名',
            ];
        } catch (UserNotFound $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getAuth(): Auth
    {
        return $this->auth;
    }
}