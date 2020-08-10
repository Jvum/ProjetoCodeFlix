<?php


class VideosTableSeeder
{
    private $allGenres;
    private $relations = [
        'genres_id' => [],
        'categories_id' => []
    ];

    public function run()
    {
        $dir = \Storage::getDriver()->getAdapter()->getPathPrefix();
        \File::deleteDirectory($dir);

        $self = $this;
        $this->allGenres = \App\Models\Genre::all();
        Model::reguard();
        factory(\App\Models\Video::class, 100)
            ->make()
            ->each(function (\App\Models\Video $videos) use ($self){
                $self->fetchRelations();
                \App\Models\Video::create(
                    array_merge(
                        $videos->toArray(),
                        [
                            'thumb_file' => $self->getImageFile(),
                            'banner_file' => $self->getImageFile(),
                            'video_file' => $self->getImageFile(),
                            'trailer_file' => $self->getImageFile()
                        ],
                        $this->relations
                    )
                );

            });
        Model::unguard();
    }

    public  function fetchRelations()
    {
        $subGenres = $this->allGenres->random(5)->load('categories');
        $categoriesId = [];
        foreach ($subGenres as $genre){
            array_push($categoriesId, $genre->categories->pluck('id')->toArray());
        }
        $categoriesId = array_unique($categoriesId);
        $genresId = $subGenres->pluck('id')->toArray();
        $this->relations['categories_id'] = $categoriesId;
        $this->relations['genres_id'] = $genresId;
    }

    public function getImageFile()
    {
        return new \Symfony\Component\HttpFoundation\File\UploadedFile(
            storage_path('faker/thumbs/Laravel Framework.png'),
            'Laravel Framework.png'
        );
    }

    public function getVideoFile()
    {
        return new \Symfony\Component\HttpFoundation\File\UploadedFile(
            storage_path('faker/videos/Laravel Framework.mp4'),
            'Laravel Framework.mp4'
        );
    }
}
