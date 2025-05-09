<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Illuminate\Support\Facades\Log;

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

    public function getAuth(): Auth
    {
        return $this->auth;
    }
}