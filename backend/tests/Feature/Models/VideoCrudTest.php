<?php


namespace Models;


use App\Models\Video;

class VideoCrudTest extends BaseVideoTestCase
{
    public function testList()
    {
        factory(Video::class)->create();
        $videos = Video::all();
        $this->assertCount(1, $videos);
        $videoKeys = array_key($videos->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration',
            'video_file',
            'created_at',
            'updated_at',
            'deleted_at'
        ],
        $videoKeys);
    }
}
