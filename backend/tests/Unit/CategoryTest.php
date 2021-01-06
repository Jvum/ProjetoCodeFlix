<?php

namespace Tests\Unit;

use App\Models\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    //use DatabaseMigrations;

    private $category;

    /*
    public static function setUpBeforeClass()
    {
        //Incluir processos iniciais únicos aqui
        parent::setUpBeforeClass();
    }*/

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }


    protected function tearDown(): void
    {
        parent::tearDown();
    }
/*
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }
  */
    public function testFillable()
    {

        $this->assertEquals(
            ['name','description','is_active'],
            $this->$category->getFillable()
        );
    }


    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];

        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits, $categoryTraits);
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach($dates as $date){
            $this->assertContains($date, $this->$category->getDates());
        }

        $this->assertCount(count($dates), $this->$category->getDates());
    }

    public function testCastsAttribute()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];

        $casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals(
            $traits,
            $this->category->getCasts()
        );
    }

    public function testIncrementingAttribute()
    {
        $category = new Category();
        $this->assertFalse(
            $this->category->incrementing
        );
    }
}