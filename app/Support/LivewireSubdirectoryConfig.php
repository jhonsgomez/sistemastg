<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class LivewireSubdirectoryConfig
{
    public static function configure(string $prefix): void
    {
        Livewire::setUpdateRoute(
            fn($handle) =>
            Route::post("{$prefix}/livewire/update", $handle)
        );

        Livewire::setScriptRoute(
            fn($handle) =>
            Route::get("{$prefix}/livewire/livewire.js", $handle)
        );

        Livewire::setUploadRoute(
            fn($handle) =>
            Route::post("{$prefix}/livewire/upload-file", $handle)
                ->middleware(['web'])
        );

        Livewire::setPreviewRoute(
            fn($handle) =>
            Route::get("{$prefix}/livewire/preview-file/{filename}", $handle)
                ->middleware(['web'])
        );
    }
}
