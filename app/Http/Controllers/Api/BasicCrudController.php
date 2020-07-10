<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BasicCrudController extends Controller
{
    protected abstract function model();
    protected abstract function rulesStore();


    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
       $validatedData = $this->validate($request, $this->rulesStore());
       $obj = $this->model()::create($request->all());
       $obj->refresh();
       return $obj;
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function show(Category $category) // Route Model Binding Implicity
    {
//        dd($category);
        return $category;
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $validatedData = $this->validate($request, $this->rulesUpdate());
        //$category->fill($request->all());
        $obj->update($validatedData);
        return $obj;
    }

    //HTTP DELETE não é uma boa prática receber informação pelo Body, enviar pela rota é o mais comum
    public function destroy(Category $category)
    {
        $category->delete();
        //return ['sucess' => true];
        return response()->noContent(); //Status Code 204 No Content
    }
}
