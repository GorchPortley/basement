<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams one media file from whichever disk it lives on.
 *
 * Every image the site shows comes through here — see RouteUrlGenerator for why.
 */
class MediaController extends Controller
{
    public function __invoke(Request $request, Media $media): StreamedResponse
    {
        // Everything you can reach by browsing is public right now, so there is
        // no permission check yet. When designs get a draft/private state this
        // is the single place to add one, roughly:
        //
        //     abort_unless($media->model?->active || $media->model?->owner_id === auth()->id(), 404);

        $response = $media->toInlineResponse($request);

        // Let the browser keep the image instead of asking for it on every page
        // view. Media files never change in place, a new upload gets a new id.
        $response->setMaxAge(3600);

        return $response;
    }
}
