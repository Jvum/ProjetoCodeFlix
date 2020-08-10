<?php


namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\UploadFiles;
use Illuminate\Http\UploadedFile;
use Tests\stubs\Model\UploadFilesStub;
use Tests\TestCase;
use Tests\Traits\TestStorages;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TraitProd;

class UploadedFilesUnitTest extends TestCase
{
    use TestStorages, TraitProd;

    //use DatabaseMigrations;

    private $obj;


    protected function setUp(): void
    {
        parent::setUp();
        $this->skipTestIfNotProd();
        $this->obj = new UploadFilesStub();
        \Config::set('filesystems.default', 'gcs');
        $this->deleteAllFiles();

    }

    public function testUploadFile()
    {
        $this->markTestSkipped('Testes de Produção');
        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        \Symfony\Component\HttpFoundation\Tests\Session\Storage::assertExists("1/{$file->hasName()}");
    }

    public function testUploadFiles()
    {
        $file1 = UploadedFile::fake()->create('video1.mp4');
        $file2 = UploadedFile::fake()->create('video2.mp4');
        $this->obj->uploadFiles([$file1,$file2]);
        \Symfony\Component\HttpFoundation\Tests\Session\Storage::assertExists("1/{$file1->hasName()}");
        \Symfony\Component\HttpFoundation\Tests\Session\Storage::assertExists("1/{$file2->hasName()}");
    }

    public function testDeleteOldFiles()
    {
        $file1 = UploadedFile::fake()->create('video1.mp4');
        $file2 = UploadedFile::fake()->create('video2.mp4');
        $this->obj->uploadFiles([$file1,$file2]);
        $this->obj->deleteOldFiles();
        $this->assertCount(2, \Storage::allFiles());

        $this->obj->oldFiles = [$file1->hashName()];
        $this->obj->deleteOldFiles();
        \Storage::assertMissing("1/{$file1->hashName()}");
        \Storage::assertExists("1/{$file2->hashName()}");

    }

    public function testDeleteFile()
    {
        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        $fileName = $file->hashName();
        $this->obj->deleteFile($fileName);
        \Storage::assertMissing("1/{$fileName}");

        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        $this->obj->deleteFile($file);
        \Storage::assertMissing("1/{$file->hashName()}");
    }

    public function testDeleteFiles()
    {
        $file1 = UploadedFile::fake()->create('video1.mp4');
        $file2 = UploadedFile::fake()->create('video2.mp4');
        $this->obj->uploadFiles([$file1,$file2]);
        $this->obj->deleteFile([$file1->hashName(), $file2]);
        \Storage::assertMissing("1/{$file1->hasName()}");
        \Storage::assertMissing("1/{$file2->hasName()}");
    }

}
