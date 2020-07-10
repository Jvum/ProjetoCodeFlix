<?php

namespace Test\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryControllerStub extends BasicCrudController
{
    protected function model()
    {
        return CategoryStub::class;
    }

    protected function rulesStore()
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable'
        ];
    }
}
