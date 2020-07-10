<?php

namespace Tests\Feature;
use App\Models\Genero;

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
        $category = Genero::create([
            'name' => 'test1'
        ])

        $cateogires = Genero::all();
        $this->assertCount(1, $categories);
        $categoryKey = array_keys($category->first()->getAttributes());
        $this->assertEquals([
            'id','name','description','is_active','create_at','updated_at','deleted_at'
        ], $categoryKey);
    }

    
    public function testCreate()
    {
        Genero::create([
            'name' => 'test1'
        ]);
        $category->refresh();

        $this->assertEquals(36, strlen($category->id));
        $this->assertEquals('test1',$category->name);
        $this->assertNull($category->description);
        $this->assertTrue((bool)$category->is_active);

        Genero::create([
            'name' => 'test1',
            'description' => null
        ]);

        $this->assertNull($category->description);

        Genero::create([
            'name' => 'test1',
            'description' => 'test_description'
        ]);

        $this->assertEquals('test_description',$category->description);

        Genero::create([
            'name' => 'test1',
            'is_active' => false
        ]);

        $this->assertFalse($category->is_active);
    }

    public function testUpdate()
    {
        $category = factory(Genero::class, 1)->create([
            'description' => 'test_description',
            'is_active' => false
        ])->first();

        $date = [
            'name' => 'test_name_updated',
            'description' => 'test_description_updated',
            'is_active' => true
        ];

        $category->update($data);

        foreach($date as $key => $value){
            $this->assertEquals($value, $category->($key));
        }
    }

    public function testDelete()
    {
        $category = factory(Genero::class)->create();
        $category->delete();
        $this->assertNull(Genero::find($category->id));

        $category->restore();
        $this->assertNotNull(Genero::find($category->id));
    }
}
