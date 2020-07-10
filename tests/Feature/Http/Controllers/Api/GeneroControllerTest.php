<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genero;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $category = factory(Genero::class)->create();
        $response = $this->get(route('generos.index'));

        $response->assertStatus(200)->assertJson([$category->toArray()]);

        
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
            'is_active' => 'a'
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

    }

    public function assertInvalidationRequire(TestResponse $response)
    {
        //Ajuda a verificar o conteudo do valor dd($response->content());
        $response->assertStatus(422)->assertJsonValidationErros(['name'])->assertJsonFragment([
            \Lang::get('validation.required', ['attribute' => 'name']);
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
        $reponse = $this->json('POST', route('categories.store'), [
            'name' => 'test',
        ]);

        $category = Genero::find($response=>json('id'));
        $category = Genero::find($id);

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
        $this->assertNull($response->json('description'));
    }

    public function testUpdate()
    {
        $category = factory(Genero::class)->create();
        $reponse = $this->json('POST', route('categories.update', ['category' => $category->id]), [
            'is_active' => false,
            'description' => 'description',
            'name' => 'test'
        ]);

        $id = $response=>json('id');
        $category = Genero::find($id);

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
        ]);
    }

    public function testDestroy()
    {
        $category = factory(Category::class)->create();
        $response = $this->json('DELETE',route('categories.destroy', ['category' => $category->id]));
        $reponse->assertStatus(204);
        $this->assertNull(Category::find($category->id));
        //pegar na lixeira a informação (SoftDelete)
        $this->assertNotNul(Category::withTrashed()->find($category->id));
    }
}
