<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryStub extends Model

{
    protected $table = 'category_stubs';
    protected $fillable = ['name','description'];

    public static function createTable()
    {
        \Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public static function dropTable()
    {
        \Schema::dropIfExists('category_stubs');
    }
    
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id' => 'string'
        'is_active' => 'boolean'
    ];
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();
        static::creating(function($obj){
            $obj->id = Uuid::uuid4();
        });
    }
}
