<?php

// for App\Models\Driver.php

namespace App\Support\MediaLibrary;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class DriverPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media).'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media).'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media).'/responsive-images/';
    }

    /*
     * {owner_id}/{brand}/{model}/{collection}/{media_id}
     * puts every upload "event" in its own folder for versioning
     */
    protected function getBasePath(Media $media): string
    {
        $driver = $media->model;

        $ownerId = $driver->owner_id ?? 'unassigned';
        $brand = Str::slug(data_get($driver->payload, 'meta.brand')) ?: 'unassigned';
        $model = Str::slug(data_get($driver->payload, 'meta.model')) ?: 'unassigned';

        return implode('/', [
            $ownerId,
            $brand.' '.$model,
            $media->collection_name,
            $media->getKey(),
        ]);
    }
}
