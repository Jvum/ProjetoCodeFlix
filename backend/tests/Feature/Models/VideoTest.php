<?php


namespace Models;


use App\Http\Controllers\CreateVideosController;
use App\Models\Category;
use App\Models\CreateVideos;
use App\Models\Genre;
use App\Models\Video;
use Doctrine\DBAL\Query\QueryException;
use http\Env\Request;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Exceptions\TestException;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use DatabaseMigrations;

    private $data;

    public function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
        ];
    }

    public function testList()
    {
        factory(Video::class)->create();
        $videos = Video::all();
        $this->assertCount(1, $videos);
        $videoKeys = array_keys($videos->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'opened',
                'rating',
                'duration',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $videoKeys
        );
    }

    public function testCreatedWithBasicFields()
    {
        $video = Video::create($this->data);
        $video->refresh();

        $this->assertEquals(36, strlen($video->id));
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => false]);

        $video = Video::create($this->data + ['opened' => true]);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', ['opened' => true]);
    }

    public function testCreatedWithRelations()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $video = CreateVideos::create($this->data + [
            'categories_id' => [$category->id],
                'genres_id' => [$genre->id]
            ]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video-id, $genre->id);
    }

    public function testRollbackStore()
    {
        $hasError = false;
        try{
            CreateVideos::create([
                'title' => 'title',
                'description' => 'description',
                'year_launched' => 2010,
                'rating' => CreateVideos::RATING_LIST[0],
                'duration' => 90,
                'categories_id' => [0, 1, 2]
            ]);

        }catch (QueryException $exception){
            $this->assertCount(0, Video::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);

    }

    public function testUpdatedWithBasicFields()
    {
        $video = factory(CreateVideos::class)->create(
            ['opened' => false]
        );
        $video->update($this->data);
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => false]);

        $video = factory(CreateVideos::class)->create(
            ['opened' => false]
        );
        $video->update($this->data);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', ['opened' => true]);
    }

    public function testUpdateddWithRelations()
    {
        $category = factory(Video::class)->create();
        $genre = factory(Genre::class)->create();
        $video = factory(Video::class)->create();
        $video->update($this->data + [
            'categories_id' => [$category->id],
                'genres_id' => [$genre->id]
            ]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video-id, $genre->id);
    }

    public function testRollbackUpdate()
    {
        $video = factory(Video::class)->create();

        $oldTtitle = $video->title;
        try{
            $video->update([
                'title' => 'title',
                'description' => 'description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'categories_id' => [0, 1, 2]
            ]);
        }
        catch (QueryException $exception){
            $this->assertDatabaseHas('videos', [
                'title' => $oldTtitle
            ]);
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public  function assertHasCategory($videosId, $categoryId)
    {
        $this->assertDatabaseHas('category_video', [
            'video_id' => $videosId,
            'category_id' => $categoryId
        ]);
    }

    public function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'video_id' => $videoId,
            'genre_id' => $genreId
        ]);
    }

    public function testHandleRelations()
    {
        $video = factory(Video::class)->create();
        Video::handleRelation($video, []);
        $this->assertCount(0, $video->categories());
        $this->assertCount(0, $video->genres());

        $category = factory(Category::class)->create();
        Video::handleRelations($video, [
            'categories_id' => [$category->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->categories());

        $genre = factory(Genre::class)->create();
        Video::handleRelations($video, [
            'genres_id' => [$genre->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->genres);

        $video->categories()->delete();
        $video->genres()->delete();

        Video::handleRelations($video, [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ]);
        $video->refresh();

        $this->assertCount(1, $video->categories);
        $this->assertCount(1, $video->genres);
    }


    public function testSyncGenres()
    {
        $genres = factory(Genre::class, 3)->create();
        $genreId = $genres->pluck('id')->toArray();
        $video = factory(Video::class)->create();
        Video::handleRelations($video, [
            'genres_id' => [$genreId[0]]
        ]);

        $this->assertDatabaseHas('genre_video', [
            'category_id' => $genreId[0],
            'video_id' => $video->id
        ]);


        $this->assertDatabaseMissing('genre_video', [
            'genre_id' => $genreId[0],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genreId[1],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genreId[2],
            'video_id' => $video->id
        ]);

    }


    public  function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3 )->create()->pluck('id')->toArray();
        $video = factory(Video::class)->create();
        Video::handleRelations($video, [
            'categories_id' => [$categoriesId[0]]
        ]);

        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $video->id
        ]);

        Video::handleRelations($video, [
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ]);

        $this->assertDatabaseMissing('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[1],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[2],
            'video_id' => $video->id
        ]);

    }
}
