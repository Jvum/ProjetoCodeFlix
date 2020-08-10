<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $category;
    private $serializedFields = [
        'id',
        'name',
        'description',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }

    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)
            ->assertJson([
                'meta' => ['per_page' => 15]
            ])
        ->assertJsonStructure([
            'data' => [
                '*' => $this->serializedFields
                ],
            'meta' => [],
            'links' => [],
        ]);

        $resource = CategoryResource::collection(collect([$this->category]));
        $response->assertResource($response, $resource);

    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show',[ 'category' => $category->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);

        $id = $response->json('data.id');
        $resource = new CategoryResource(Category::find($id));
        $this->assertResource($response, $resource);


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

    public function testStore()
    {
        $data = [
            'name' => 'test'
        ];
        $response = $this->assertStore($data, $data + ['description' => null, 'is_active' => true, 'deleted_at' => null, $data + ['description' => null, 'is_active' => true, 'deleted_at' => null]]);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $data = [
            'name' => 'test',
            'description' => 'description',
            'is_active' => false
        ];
        $this->assertStore($data, $data + ['description' => null, 'is_active' => true, 'deleted_at' => null, $data + ['description' => null, 'is_active' => false, 'deleted_at' => null]]);
        $id = $response->json(`data.id`);
        $resource = new CategoryResource(Category::find($id));
        //dump(new CategoryResource(Category::first()));
        $response->assertResource($response, $resource);
        /*
        $reponse = $this->json('POST', route('categories.store'), [
            'name' => 'test',
        ]);


        $reponse = $this->json('POST', route('categories.store'), [
            'name' => 'test',
        ]);

        $category = Category::find($response=>json('id'));
        $category = Category::find($id);

        $response->assertStatus(201)->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $reponse = $this->json('POST', route('categories.store'), [
            'name' => 'test',
            'description' => 'description',
            'is_active' => false
        ]);

        $response->assertJsonFragment([
            'is_active' => false,
            'description' => 'description'
        ]);
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));*/
    }

    public function testUpdate()
    {
        $this->category = factory(Category::class)->create([
            'description' => 'description',
            'is_active' => false
        ]);
        $data = [
            'name' => 'test',
            'description' => 'test',
            'is_active' => true
        ];

        $response = $this->assertUpdate(
            $data, $data + ['deleted_at' => null]
        );
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $id = $response->json(`data.id`);
        $resource = new CategoryResource(Category::find($id));
        $response->assertResource($response, $resource);

        $data = [
            'name' => 'test',
            'description' => '',
        ];

        $response = $this->assertUpdate(
            $data, array_merge($data, ['description' => null])
        );

        $data['description'] = 'test';
        $response = $this->assertUpdate(
            $data, array_merge($data, ['description' => 'test'])
        );

        $data['description'] = null;
        $response = $this->assertUpdate(
            $data, array_merge($data, ['description' => null])
        );

        /*

        $id = $response=>json('id');
        $category = Category::find($id);

        $response->assertStatus(200)->assertJson($category->toArray())->assertJsonFragment([
            'description' => 'test',
            'is_active' => true
        ]);

        $reponse = $this->json('POST', route('categories.update', ['category' => $category->id]), [
            'description' => '',
            'name' => 'test'
        ]);

        $response->assertJsonFragment([
            'description' => null,
        ]);

        $category->description = 'test';
        $category->save();

        $reponse = $this->json('POST', route('categories.update', ['category' => $category->id]), [
            'description' => null,
            'name' => 'test'
        ]);

        $response->assertJsonFragment([
            'description' => null,
        ]);*/
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE',route('categories.destroy', ['category' => $this->$category->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($category->id));
        //pegar na lixeira a informação (SoftDelete)
        $this->assertNotNul(Category::withTrashed()->find($category->id));
    }

    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function model()
    {
        return Category::class;
    }
}
