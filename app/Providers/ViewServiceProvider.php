<?php

namespace App\Providers;

use App\Http\View\Composers\FrontendComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Apply FrontendComposer to all frontend views and layouts
        View::composer(['frontend.*', 'livewire.front.*'], FrontendComposer::class);
    }
}
