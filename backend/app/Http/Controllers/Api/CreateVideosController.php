<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use App\Models\CreateVideos;
use App\Models\Video;
use App\Rules\GenresHasCategoriesRule;
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
            'rating' => 'required|in:'.implode(',', Video::RATING_LIST),
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists::categories.id,deleted_at,NULL',
            'genres_id' => ['required',
                'array',
                'exists::genres.id,deleted_at,NULL'],
            'video_file' => 'required'
        ];
    }

    public function store(Request $request)
    {
        $this->addRuleGenreHasCategories($request);
        $validatedData = $this->validate($request, $this->rulesStore());
        $obj = $this->model()::create($validatedData);
        $obj->refresh();
        return $obj;
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $this->addRuleGenreHasCategories($request);
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $obj->update($validatedData);
        return $obj;
    }

    protected function handleRelations($video, Request $request)
    {
        $video->categories()->sync($request->get('categories_id'));
        $video->genres()->sync($request->get('generos_id'));
    }

    protected function model()
    {
        return CreateVideos::class;
    }

    protected function rulesStore()
    {
        // TODO: Implement rulesStore() method.
    }

    protected function addRuleGenreHasCategories(Request $request)
    {
        $categoriesId = $request->get('categories_id');
        $categoriesId = is_array($categoriesId) ? $categoriesId : [];
        $this->rules['genres_id'][] = new GenresHasCategoriesRule(
            $request->get('categories_id')
        );
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
