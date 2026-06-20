<?php

namespace App\Providers;

use App\Models\JadwalKegiatan;
use App\Policies\JadwalKegiatanPolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(JadwalKegiatan::class, JadwalKegiatanPolicy::class);

        Carbon::setLocale(config('app.locale'));
        date_default_timezone_set(config('app.timezone'));

        // Railway terminates SSL at its reverse proxy, so PHP sees HTTP.
        // Force HTTPS for all generated URLs in production.
        if ($this->app->environment('production') || env('APP_ENV') === 'production' || str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
