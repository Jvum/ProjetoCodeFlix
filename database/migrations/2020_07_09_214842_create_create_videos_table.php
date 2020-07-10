<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('create_videos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->smallInteger('year_launched');
            $table->boolean('opened')->default(false);
            $table->smallInteger('duration');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer'
    ];

    public $incrementing = false;

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('create_videos');
    }
}
