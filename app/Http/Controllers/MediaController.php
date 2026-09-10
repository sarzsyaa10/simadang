<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function show(string $path)
    {
        $disk = Storage::disk('public');

        abort_unless($disk->exists($path), 404);

        return response($disk->get($path), 200)
            ->header('Content-Type', $disk->mimeType($path))
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
