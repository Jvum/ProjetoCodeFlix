<?php

namespace Tests\Feature;
use App\Models\Genero;

use App\Models\Genre;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        //Cria informação de modo rápido
        //factory(Category::class, 1)->create();
        $category = Genre::create([
            'name' => 'test1'
        ]);

        $categories = Genre::all();
        $this->assertCount(1, $categories);
        $categoryKey = array_keys($category->first()->getAttributes());
        $this->assertEquals([
            'id','name','description','is_active','create_at','updated_at','deleted_at'
        ], $categoryKey);
    }


    public function testCreate()
    {
        $genre = Genre::create([
            'name' => 'test1'
        ]);
        $genre->refresh();

        $this->assertEquals(36, strlen($genre->id));
        $this->assertEquals('test1',$genre->name);
        $this->assertNull($genre->description);
        $this->assertTrue((bool)$genre->is_active);

        Genre::create([
            'name' => 'test1',
            'description' => null
        ]);

        $this->assertNull($genre->description);

        Genre::create([
            'name' => 'test1',
            'description' => 'test_description'
        ]);

        $this->assertEquals('test_description',$genre->description);

        Genre::create([
            'name' => 'test1',
            'is_active' => false
        ]);

        $this->assertFalse($genre->is_active);
    }

    public function testUpdate()
    {
        $category = factory(Genre::class, 1)->create([
            'description' => 'test_description',
            'is_active' => false
        ])->first();

        $data = [
            'name' => 'test_name_updated',
            'description' => 'test_description_updated',
            'is_active' => true
        ];

        $category->update($data);

        foreach($data as $key => $value){
            $this->assertEquals($value, $category->key);
        }
    }

    public function testDelete()
    {
        $category = factory(Genre::class)->create();
        $category->delete();
        $this->assertNull(Genre::find($category->id));

        $category->restore();
        $this->assertNotNull(Genre::find($category->id));
    }
}
