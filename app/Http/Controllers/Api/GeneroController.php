<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;

class GeneroController extends Controller
{
    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean'
    ];

    public function index(Request $request)
    {
        return Genero::all();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        return Genero::create($request->all());
    }

    public function show(Genero $genero)
    {
        return $genero;
    }

    public function edit(Genero $genero)
    {
        //
    }

    public function update(Request $request, Genero $genero)
    {
        $this->validate($request, $this->rules);
        $genero->update($request->all());
        return $genero;
    }

    public function destroy(Genero $genero)
    {
        $genero->delete();
        return response()->noContent();
    }
}
