<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('ja_JP');
        $userIds = DB::table('users')->pluck('id')->toArray();
        $postIds = DB::table('posts')->pluck('id')->toArray();

        foreach ($postIds as $postId) {
            foreach (range(1, rand(2, 5)) as $i) {
                DB::table('comments')->insert([
                    'post_id' => $postId,
                    'user_id' => $faker->randomElement($userIds),
                    'comment' => $faker->realText(50),
                    'created_at' => Carbon::now()->subDays(rand(1, 10)),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
