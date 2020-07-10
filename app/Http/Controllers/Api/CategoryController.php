<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    private $rules = [
        'name' => 'required|max:255',
        'description' => 'nullable',
        'is_active' => 'boolean'
    ];

    public function index()
    {
        //Return SoftDeletes only
        /*if($request->has('only_trashed')){
            return Category::onlyTrashed()->get();
        }*/
        return Category::all();
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        $category = Category::create($request->all());
        $category->refresh();
        return $category;

    }

    public function show(Category $category) // Route Model Binding Implicity
    {
//        dd($category);
        return $category;
    }

    public function update(Request $request, Category $category)
    {
        $this->validate($request, $this->rules);
        //$category->fill($request->all());
        $category->update($request->all());
        return $category;
    }

    //HTTP DELETE não é uma boa prática receber informação pelo Body, enviar pela rota é o mais comum
    public function destroy(Category $category)
    {
        $category->delete();
        //return ['sucess' => true];
        return response()->noContent(); //Status Code 204 No Content
    }
}
