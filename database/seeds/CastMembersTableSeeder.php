<?php


class CastMembersTableSeeder
{
    public function run()
    {
        factory(\App\Models\CastMember::class, 100)->create();
    }
}
