<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\User;
use Illuminate\View\View;

class ComponentController extends Controller
{
    public function index(): View
    {
        return view('components.index');
    }

    public function show(Component $component): View
    {
        $isOwner = auth()->check()
            && $component->owner_type === User::class
            && $component->owner_id === auth()->id();

        abort_unless($component->active || $isOwner, 404);

        $component->load([
            'owner',
            'designs' => fn ($q) => $q->published(),
            'media',
        ]);

        return view('components.show', ['component' => $component]);
    }
}
