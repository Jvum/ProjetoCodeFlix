<?php


namespace App\Http\Controllers\Api;


use App\Models\CastMember;

class CastMemberController extends BasicCrudController
{
    private $rules;

    public function _construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'type' => 'required|in:' . implode(',', [CastMember::TYPE_ACTOR, CastMember::TYPE_DIRECTOR])
        ];
    }

    public function model()
    {
        return CastMember::class;
    }

    public function rulesStore()
    {
        return $this->rules;
    }

    public function rulesUpdate()
    {
        return $this->rules;
    }

    public function resource()
    {
        return CastMemberResource::class();
    }

    public function resourceCollection()
    {
        return $this->resource();
    }
}
