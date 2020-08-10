<?php

namespace App\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Self_;

class Video extends Model
{
    use SoftDeletes, Uuid, UploadFiles;

    const NO_RATING = 'L';
    const RATING_LIST = [self::NO_RATING, '10', '12', '14', '16', '18'];

    const THUMB_FILE_MAX_SIZE = 1024 * 5;
    const BANNER_FILE_MAX_SIZE = 1024 * 10;
    CONST TRAILER_FILE_MAX_SIZE = 1024 * 1024 * 1;
    const VIDEO_FILE_MAX_SIZE = 1024 * 1025 * 50;

    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'video_file',
        'thumb_file',
        'banner_file',
        'trailer_file'
    ];


    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer'
    ];

    public $incrementing = false;
    public static $fileFields = ['video_file', 'thumb_file'];

    public static function create(array $attributes = [])
    {
        $files = self::extractFiles($attributes);
        try{
            \DB::beginTransaction();
            $obj = static::query()->create($attributes);
            static::handleRelations($obj, $attributes);
            $obj->uploadFiles($files);
            \DB::commit();
            return $obj;
        }catch (\Exception $e)
        {
            if(isset($obj)){
                $obj->deleteFiles($files);
            }
            \DB::rollback();
            throw $e;
        }

        return $obj;
    }

    public function update(array $attributes = [], array $options = [])
    {
        $files = self::extractFiles($attributes);

        try{
            \DB::beginTransaction();
            $saved = static::update($attributes, $options);
            static::handleRelations($this, $attributes);
            if($saved){
                $this->uploadFile($files);
            }
            \DB::commit();
            if($saved && count($files)){
                $this->deleteOldFiles();
            }
            return $saved;
        }catch (\Exception $e)
        {
            $this->deleteFiles($files);
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
            $video->generos()->sync($attributes['genres_id']);
        }

    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)->withTrashed();
    }

    protected function uploadDir()
    {
        return $this->id;
    }

    public function getThumbFileUrlAttribute()
    {
        return $this->thumb_file ? $this->getFileUrl($this->thumb_file): null;
    }

    public function getBannerFileUrlAttribute()
    {
        return $this->banner_file ? $this->getFileUrl($this->banner_file): null;
    }

    public function getVideoFileUrlAttribute()
    {
        return $this->video_file ? $this->getFileUrl($this->video_file): null;
    }

    public function getTrailerFileUrlAttribute()
    {
        return $this->trailer_file ? $this->getFileUrl($this->trailer_file): null;
    }

}
