<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneroCreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genero_create_videos', function (Blueprint $table) {
            $table->uuid('genero_id')->index();
            $table->foreign('genero_id')->references('id')->on('generos');
            $table->uuid('create_videos_id')->index();
            $table->foreign('video_id')->references('id')->on('videos');
            $table->unique(['category_id', 'create_videos_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genero_create_videos');
    }
}
