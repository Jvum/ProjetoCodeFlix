<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;

class GeneroController extends Controller
{
    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
        'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL'
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
        $validatedData = $this->validate($request, $this->rulesStore());
        $self = $this;
        //Allow us to not save the Video without save Categories and Genres, because we need to maintain the persistence
        $obj = \DB::transacation(function () use ($request, $validatedData, $self) {
            $obj = $this->model()::create($validatedData);
            $self->handleRelations($obj, $request);
            throw new \Exception();
            return $obj;
        });

        $obj->refresh();
        return $obj;
    }

    public function show(Genero $genero)
    {
        return $genero;
    }

    public function edit(Genero $genero)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $validateData = $this->validate($request, $this->rulesUpdate());
        $self = $this;
        \DB::transaction(function () use ($self, $request, $obj, $validateData)
        {
           $obj->update($validateData);
           $self->handleRelations($obj,$request);
        });

        return $obj;
    }

    protected function handleRelations($video, Request $request)
    {
        $video->categories()->sync($request->get('categories_id'));
    }

    public function destroy(Genero $genero)
    {
        $genero->delete();
        return response()->noContent();
    }
}
