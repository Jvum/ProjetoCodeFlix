<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\GeneroController;
use App\Http\Resources\GenreResource;
use App\Models\Category;
use App\Models\Genero;
use http\Env\Request;
use Tests\Exceptions\TestException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestResource;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves, TestResource;

    private $genre;
    private $fieldsSerialized = [
        'id',
        'name',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
        'categories' => [
            '*' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ]
        ]
    ];

    public function setUp(): void
    {
        parent::setUp();
        $category = factory(Genero::class)->create();

    }

    public function testIndex()
    {
        $response = $this->get(route('generos.index'));
        $response->assertStatus(200)->
        assertJsonStructure([
            'data' => [
                '*' => $this->fieldsSerialized
            ],
            'meta' => [],
            'links' => []
        ]);
        $this->assertResource($response, GenreResource::collection(collect([$this->genre])));

    }

    public function testShow()
    {
        $category = factory(Genero::class)->create();
        $response = $this->get(route('generos.show',[ 'genero' => $category->id]));

        $response->assertStatus(200)->assertJson($category->toArray());

    }

    public function testInvalidationData()
    {
        //Fazer dessa forma para receber como JSON a resposta
        $response = $this->json('POST',route('generos.store'), []);
        $this->assertInvalidationRequire($response);

        $response = $this->json('POST',route('generos.store'), [
            'name' => str_repeat('a',256),
            'categories_id' => ''
        ]);

        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $category = factory(Category::class)->create();

        $response = $this->json('PUT', route('generos.update', ['category' => $category->id]), []);
        $this->assertInvalidationRequire($response);

        $response = $this->json('PUT', route('generos.update', ['category' => $category->id]), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $data = [
            'categories_id' => 'a'
        ];

        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');


    }

    public function assertInvalidationRequire(TestResponse $response)
    {
        //Ajuda a verificar o conteudo do valor dd($response->content());
        $response->assertStatus(422)->assertJsonValidationErros(['name'])->assertJsonFragment([
            \Lang::get('validation.required', ['attribute' => 'name'])
        ])->assertJsonMissingValidationErros(['is active']);

    }

    public function assertInvalidationMax(TestResponse $response)
    {
        //Remover o underscore na hora de validar, pois o phpunit não reconhece
        $response->assertStatus(422)->assertJsonValidationErros(['name','is_active'])->assertJsonFragment([
            \Lang::get('validation.max.string', ['attribute' => 'name']);
        ]);
    }

    public function assertInvalidationBoolean(TestResponse $response)
    {
        //Remover o underscore na hora de validar, pois o phpunit não reconhece
        $response->assertStatus(422)->assertJsonValidationErros(['is_active'])->assertJsonFragment([
            \Lang::get('validation.boolean', ['attribute' => 'is active']);
        ]);
    }

    public function testStore()
    {
        $categoryId = factory(Category::class)->create()->id;
        $data = [
            'name' => 'test'
        ];
        $response = $this->assertStore(
            $data + ['categories_id' => [$categoryId]],
            $data + ['is_active' => true, 'deleted_at' => null]
        );

        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $this->assertHasCategory($response->json('id'), $categoryId);

        $data = [
            'name' => 'test',
            'is_active' => false
        ];

        $this->assertStore(
            $data + ['categories_id' => [$categoryId]],
            $data + ['is_active' => false]
        );
    }

    public function testUpdate()
    {
        $categoryId = factory(Category::class)->create()->id;
        $data = [
            'name' => 'test',
            'is_active' => true
        ];
        $response = $this->assertUpdate(
            $data + ['categories_id' => [$categoryId]],
            $data + ['is_active' => true, 'deleted_at' => null]
        );

        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $this->assertHasCategory($response->json('id'), $categoryId);

    }

    protected  function  asserHasCategory($genreId, $categoryId)
    {
        $this->assertDatabaseHas('category_genero', [
            'genero_id',
            'category_id' => $categoryId
        ]);
    }

    public  function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3 )->create()->pluck('id')->toArray();

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[0]]
        ];

        $response = $this->json('POST', $this->routeStore(), $sendData);
        $this->assertDatabaseHas('category_genero', [
            'category_id' => $categoriesId[0],
            'genero_id' => $response->json('id')
        ]);

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ];

        $response = $this->json(
            'PUT',
            route('generos.update', ['genero' => $response->json(['id'])]),
            $sendData
        );

        $this->assertDatabaseMissing('category_genero', [
            'category_id' => $categoriesId[0],
            'genero_id' => $response->json('id')
        ]);
        $this->assertDatabaseHas('category_genero', [
            'category_id' => $categoriesId[1],
            'genero_id' => $response->json('id')
        ]);
        $this->assertDatabaseHas('category_genero', [
            'category_id' => $categoriesId[2],
            'genero_id' => $response->json('id')
        ]);

    }

    public function testRollbackStore()
    {
        $controller = \Mockery::mock(GeneroController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn([
                'name' => 'test'
            ]);

        $controller
            ->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $controller
            ->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());

        $request = \Mockery::mock(Request::class);

        $hasError = false;
        try{
            $controller->store($request);
        } catch (TestException $exception)
        {
            $this->assertCount(1, Genero::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate()
    {
        $controller = \Mockery::mock(GeneroController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('findOrFail')
            ->withAnyArgs()
            ->andReturn($this->genre);

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn([
                'name' => 'test'
            ]);

        $controller
            ->shouldReceive('rulesUpdate')
            ->withAnyArgs()
            ->andReturn([]);

        $controller
            ->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());

        $request = \Mockery::mock(Request::class);

        $hasError = false;
        try{
            $controller->update($request, 1);
        } catch (TestException $exception)
        {
            $this->assertCount(1, Genero::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testDestroy()
    {
        $category = factory(Category::class)->create();
        $response = $this->json('DELETE',route('categories.destroy', ['category' => $category->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($category->id));
        //pegar na lixeira a informação (SoftDelete)
        $this->assertNotNul(Category::withTrashed()->find($category->id));
    }
}
