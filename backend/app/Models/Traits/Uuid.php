<?php

namespace App\Models\Traits;


use function App\Models\Traits\uuid4 as uuid4Alias;

trait Uuid
{
    public static function boot()
    {
        parent::boot();
        static::creating(function($obj){
            $obj->id = uuid4Alias();
        });
    }
}
