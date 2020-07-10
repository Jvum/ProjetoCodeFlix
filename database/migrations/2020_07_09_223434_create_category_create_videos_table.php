<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryCreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_create_videos', function (Blueprint $table) {
            $table->uuid('category_id')->index();
            $table->foreign('category_id')->references('id')->on('categories');
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
        Schema::dropIfExists('category_create_videos');
    }
}
