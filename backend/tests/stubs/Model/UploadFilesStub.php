<?php


namespace Tests\stubs\Model;

use App\Models\Traits\UploadFiles;
use Illuminate\Database\Eloquent\Model;

class UploadFilesStub extends Model
{
    use UploadFiles;

    protected $table = 'upload_file_stubs';
    protected $fillable = ['name', 'file1', 'file2'];

    public static $fileFields = ['file1', 'file2'];

    public static function makeTable()
    {
        \Illuminate\Database\Schema::create('upload_file_stubs', function ($table) {
            $table->bigIncrement('id');
            $table->string('name');
            $table->string('file1')->nullable();
            $table->string('file2')->nullable();
            $table->timestamps();
        });
    }

    public static function dropTable()
    {
        \Schema::dropIfExists('upload_file_stubs');
    }

    protected function uploadDir()
    {
        return "1";
    }
}
