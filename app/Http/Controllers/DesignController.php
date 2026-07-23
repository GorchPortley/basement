<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\User;
use Illuminate\View\View;

class DesignController extends Controller
{
    /** Public, searchable/filterable grid (interactivity lives in the Livewire component). */
    public function index(): View
    {
        return view('designs.index');
    }

    public function show(Design $design): View
    {
        // Only published designs are public; owners can still preview their own.
        $isOwner = auth()->check()
            && $design->owner_type === User::class
            && $design->owner_id === auth()->id();

        abort_unless($design->active || $isOwner, 404);

        $design->load([
            'owner',
            'collaborators',
            'components' => fn ($q) => $q->orderByPivot('position'),
            'media',
        ]);

        return view('designs.show', ['design' => $design]);
    }
}
