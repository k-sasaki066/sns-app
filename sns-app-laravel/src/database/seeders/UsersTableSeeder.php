<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\FirebaseService;

class UsersTableSeeder extends Seeder
{
    protected $firebaseService;

    // コンストラクタでFirebaseServiceを注入
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $firebaseUids = [
            env('FIREBASE_UID_1'),
            env('FIREBASE_UID_2'),
            env('FIREBASE_UID_3'),
        ];

        foreach ($firebaseUids as $firebaseUid) {
            $userInfo = $this->firebaseService->getUserByUid($firebaseUid);

            if ($userInfo) {
                DB::table('users')->insert([
                    'firebase_uid' => $userInfo['firebase_uid'],
                    'name' => $userInfo['name'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
