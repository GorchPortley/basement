<?php

namespace App\Support\MediaLibrary;

use DateTimeInterface;
use Spatie\MediaLibrary\Support\UrlGenerator\BaseUrlGenerator;

/**
 * Turns every media URL into an app URL, e.g. /media/12, instead of a storage URL.
 *
 * The default generator asks the disk for a URL. That does not work here: the
 * "uploads" disk is Garage (S3), which the app reaches at http://garage_dev:3900
 * — a name that only exists inside the docker network, so a browser can never
 * load it. The bucket is private too, so even a reachable link would be denied.
 *
 * Pointing at our own route sidesteps both problems: the browser talks to
 * Laravel, and Laravel streams the file from Garage using its credentials.
 * Media Library uses this class everywhere, so browse cards and the file
 * previews inside Filament forms are both fixed by it.
 */
class RouteUrlGenerator extends BaseUrlGenerator
{
    public function getUrl(): string
    {
        return route('media.show', ['media' => $this->media->getKey()]);
    }

    public function getPath(): string
    {
        return $this->getPathRelativeToRoot();
    }

    /**
     * The route is already ours to control, so there is nothing to sign.
     */
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        return $this->getUrl();
    }

    /**
     * Responsive image srcsets are not served through this route.
     */
    public function getResponsiveImagesDirectoryUrl(): string
    {
        return '';
    }
}
