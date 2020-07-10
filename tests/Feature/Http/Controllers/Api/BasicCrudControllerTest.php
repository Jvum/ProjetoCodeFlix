<?php

namespace Tests\Feature\Http\Controllers\Api;

class BasicCrudControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void 
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
    }

    protected function tearDown(): void 
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex()
    {
        $category = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);
        $controller = new CategoryControllerStub();
        $result = $category->index()->toArray();
        $this->assertEquals([$category->toArray()], $result);
    }

    public function testInvalidationDataInStore()
    {
        $this->expectException(ValidationException::class);
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name' => '']);
        //dd($request->all());
        $this->controller->store($request);
    }

    public function testStore()
    {
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name' => 'test_name', 'description' => 'test_description']);
        //dd($request->all());
        $obj = $this->controller->store($request);
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $obj->toArray()
        );
    }

    public function testIfFindOrFailFetchModel()
    {
        $category = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        //Reflection é criado para acessar o método protected
        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function testIfFindOrFailFetchModelThrowExceptionWhenIdInvalid()
    {
        $category = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        //Reflection é criado para acessar o método protected
        $result = $reflectionMethod->invokeArgs($this->controller, [0]);
    }
}