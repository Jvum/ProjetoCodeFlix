<?php


namespace Http\Controllers;


use App\Http\Controllers\CreateVideosController;
use App\Models\Category;
use App\Models\CreateVideos;
use App\Models\Genero;
use http\Env\Request;
use Tests\Exceptions\TestException;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CreateVideoControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $video;
    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = factory(CreateVideos::class)->create([
            'opened' => false
        ]);
        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => CreateVideos::RATING_LIST[0],
            'duration' => 90,
        ];
    }

    public function testIndex()
    {
        $response = $this->get(route('create_videos.index'));

        $response->assertStatus(200)->assertJson([$this->video->toArray()]);


    }

    public function testInvalidationRequired()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'generos_id' => ''
        ];

        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationInteger()
    {
        $data = [
            'duration' => 's'
        ];

        $this->assertInvalidationInStoreAction($data, 'integer');
        $this->assertInvalidationInUpdateAction($data, 'integer');
    }

    public function testInvalidationYearLaunchedField()
    {
        $data = [
            'year_launched' => 'a'
        ];

        $this->assertInvalidationInStoreAction($data, 'data_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data,'data_format', ['format' => 'Y']);
    }

    public function testInvalidationCategoriesIdField()
    {
        $data = [
            'categories_id' => 'a'
        ];

        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data,'array');

        $data = [
            'categories_id' => [100]
        ];

        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data,'exists');
    }

    public function testInvalidationGeneroesIdField()
    {
        $data = [
            'generos_id' => 'a'
        ];

        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data,'array');

        $data = [
            'generos_id' => [100]
        ];

        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data,'exists');
    }

    public function testInvalidationOpenedField()
    {
        $data = [
            'opened' => 's'
        ];

        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testInvalidationRatingField()
    {
        $data = [
            'rating' => 0
        ];

        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }


    public function testInvalidationData()
    {
        $data = [
            'name' => ''
        ];
        //Fazer dessa forma para receber como JSON a resposta
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');


        $data = [
            'name' => str_repeat('a',256)
        ];

        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [
            'is_active' => 'a'
        ];

        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');

        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);


    }

    public function assertInvalidationRequire(TestResponse $response)
    {
        //Ajuda a verificar o conteudo do valor dd($response->content());
        $this->assertInvalidationFields($response, ['name'], 'required', []);
        $response->assertJsonMissingValidationErros(['is active']);

    }

    public function assertInvalidationMax(TestResponse $response)
    {
        //Remover o underscore na hora de validar, pois o phpunit não reconhece
        $this->assertInvalidationFields($response, ['name'], 'max.string', ['max' => 255]);
    }

    public function assertInvalidationBoolean(TestResponse $response)
    {
        //Remover o underscore na hora de validar, pois o phpunit não reconhece
        $this->assertInvalidationFields($response, ['is_active'], 'boolean');

    }

    public function testSave()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genero::class)->create();

        $data = [
            [
                'send_data' => $this->sendData + [
                    'categories_id' => [$category->id],
                        'genres_id' => [$genre->id],
                    ],
                'test_data' => $this->sendData + ['opened' => false]
            ],
            [
                'send_data' => $this->sendData + [
                    'opened' => true,
                        'categories_id' => [$category->id],
                        'genres_id' => [$genre->id],
                    ],
                'test_data' => $this->sendData + ['opened' => true]
            ],
            [
                'send_data' => $this->sendData + [
                    'rating' => CreateVideos::RATING_LIST[1],
                        'categories_id' => [$category->id],
                        'genres_id' => [$genre->id],
                    ],
                'test_data' => $this->sendData + ['rating' => CreateVideos::RATING_LIST[1]]
            ],
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'created_at',
                'updated_at'
            ]);

            $response = $this->assertUpdate([
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            ]);

            $response->assertJsonStructure([
                'created_at',
                'updated_at'
            ]);
        }
    }

    public function testStore()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genero::class)->create();

        $response = $this->assertStore($this->sendData, $this->sendData + ['opened' => false]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);
        $this->assertStore($this->sendData + ['opened' => true], $this->sendData + ['opened' => true]);
        $this->assertStore($this->sendData + ['rating' => CreateVideos::RATING_LIST[1]], $this->sendData + ['rating' => CreateVideos::RATING_LIST[1]]);

    }

    public function testRollbackStore()
    {
        $controller = \Mockery::mock(CreateVideosController::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('validate')->withAnyArgs()->andReturn($this->sendData);

        $controller->shouldReceive('rulesStore')->withAnyArgs()->andReturn([]);


        $request = \Mockery::mock(Request::class);
        $controller->shouldReceive('handleRelations')->once()->andThrow(new TestException());

        try{
            $controller->store($request);;
        }catch (TestException $exception){
            $this->assertCount(1, CreateVideos::all());
        }

    }

    public function testUpdate()
    {
        $response = $this->assertUpdate($this->sendData, $this->sendData + ['opened' => false]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);
        $this->assertUpdate($this->sendData + ['opened' => true], $this->sendData + ['opened' => true]);
        $this->assertUpdate($this->sendData + ['rating' => CreateVideos::RATING_LIST[1]], $this->sendData + ['rating' => CreateVideos::RATING_LIST[1]]);

    }

    public function testShow()
    {
        $response = $this->json('GET', route('create_videos.show', ['create_videos' => $this->video->id]));
        $response->assertStatus(200)->assertJson($this->video->toArray());
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE',route('create_videos.destroy', ['create_videos' => $this->video->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($this->video->id));
        $this->assertNotNul(Category::withTrashed()->find($this->video->id));
    }

    protected function routeStore()
    {
        return route('create_videos.store');
    }

    protected function routeUpdate()
    {
        return route('create_videos.update', ['create_videos' => $this->video->id]);
    }

    protected function model()
    {
        return Category::class;
    }
}
