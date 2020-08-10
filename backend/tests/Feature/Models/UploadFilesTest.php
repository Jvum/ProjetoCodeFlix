<?php


namespace Models;


use Tests\stubs\Model\UploadFilesStub;
use Tests\TestCase;

class UploadFilesTest extends TestCase
{
    private $obj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new UploadFilesStub();
        UploadFilesStub::dropTable();
        UploadFilesStub::makeTable();
    }

    public function testMakeOldFieldsOnSaving()
    {
        $this->obj->fill([
            'name' => 'test',
            'file1' => 'test1.mp4',
            'file2' => 'test2.mp4',
        ]);

        $this->obj->save();

        $this->assertCount(0, $this->obj->oldFiles);
        $this->obj->update([
            'name' => 'test_name',
            'file2' => 'test3.mp4'
        ]);

        $this->assertEqualsCanonicalizing(['test2.mp4'], $this->obj->oldFiles);

        $obj = UploadFilesStub::create([
            'name' => 'test'
        ]);

        $this->obj->fill([
            'name' => 'test',
            'file1' => 'test1.mp4',
            'file2' => 'test2.mp4'
        ]);
    }

    public function testMakeOldFilesNullOnSaving(){


        $this->obj->fill([
            'name' => 'test',
        ]).
        $this->obj->save();

        $this->obj->update([
            'name' => 'test_name',
            'file2' => 'test3.mp4'
        ]);

        $this->assertEqualsCanonicalizing([], $this->obj->oldFiles);

    }

}
