<?php

namespace App\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class CreateVideos extends Model
{
    use SoftDeletes, Uuid, UploadFiles;

    const NO_RATING = 'L';
    const RATING_LIST = [self::NO_RATING, '10', '12', '14', '16', '18'];

    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration'
    ];


    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer'
    ];

    public $incrementing = false;
    public static $fileFields = ['video_file'];

    public static function create(array $attributes = [])
    {
        $files = self::extractFiles($attributes);
        try{
            \DB::beginTransaction();
            $obj = static::query()->create($attributes);
            static::handleRelations($obj, $attributes);
            $obj->uploadFiles($files);
            \DB::commit();
        }catch (\Exception $e)
        {
            if(isset($obj)){

            }
            \DB::rollback();
            throw $e;
        }

        return $obj;
    }

    public function update(array $attributes = [], array $options = [])
    {
        try{
            \DB::beginTransaction();
            $saved = static::update($attributes, $options);
            static::handleRelations($this, $attributes);
            if($saved){

            }
            return $saved;
            \DB::commit();
        }catch (\Exception $e)
        {
            if(isset($obj)){

            }
            \DB::rollback();
            throw $e;
        }
    }


    protected static function handleRelations(CreateVideos $video, array $attributes)
    {
        if(isset($attributes['categories_id'])){
            $video->categories()->sync($attributes['categories_id']);
        }
        if(isset($attributes['genres_id'])){
            $video->generos()->sync($attributes['generos_id']);
        }

    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function generos()
    {
        return $this->belongsToMany(Genero::class)->withTrashed();
    }

    protected function uploadDir()
    {
        return $this->id;
    }

}
