<?php


class CreateVideosTableSeeder
{
    public function run()
    {
        factory(\App\Models\CreateVideos::class, 100)->create();
    }
}
