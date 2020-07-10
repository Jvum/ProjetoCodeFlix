<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use App\Models\CreateVideos;
use Illuminate\Http\Request;

/*
 * Auto Commit - Padrão de bancos de dados relacionais
 * Modo Transação
 *
 * -begin transaction - Marca inicio da transação
 * -transaction - executa todas as transaçõe s pertinentes
 * -commit - persiste as transações no banco
 * -rollback - desfaz todas as transações do checkpoint
 */
class CreateVideosController extends BasicCrudController
{
    private $rules;

    public function _construct()
    {
        $this->rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'year_launched' => 'required|data_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:'.implode(',', CreateVideos::RATING_LIST),
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists::categories.id',
            'generos_id' => 'required|array|exists::generos.id'
        ];
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

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);

        $validatedData = $this->validate($request, $this->rulesUpdate());
        $self = $this;
        $obj = \DB::transacation(function () use ($request, $validatedData, $self, $obj) {
            $obj->update($validatedData);
            $self->handleRelations($obj, $request);
            throw new \Exception();
            return $obj;
        });
        $obj->categories()->sync($request->get('categories_id'));
        $obj->generos()->sync($request->get('generos_id'));
        return $obj;
    }

    protected function handleRelations($video, Request $request)
    {
        $video->categories()->sync($request->get('categories_id'));
        $video->generos()->sync($request->get('generos_id'));
    }


    protected function model()
    {
        return CreateVideos::class;
    }

    protected function rulesStore()
    {
        // TODO: Implement rulesStore() method.
    }

    protected function rulesUpdate()
    {

    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }


    public function show(CreateVideos $createVideos)
    {
        //
    }

    public function edit(CreateVideos $createVideos)
    {
        //
    }

    public function destroy(CreateVideos $createVideos)
    {
        //
    }
}
