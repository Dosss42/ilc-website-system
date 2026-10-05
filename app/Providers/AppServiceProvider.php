<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\Transport\BrevoTransport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Paginator::defaultView('partials.pagination');
        Paginator::defaultSimpleView('partials.pagination');

        // Disable SSL verification on local — Windows PHP lacks the CA bundle.
        // On production this block does not run.
        if (app()->environment('local')) {
            Http::globalOptions(['verify' => false]);
        }

        // Railway terminates HTTPS at its edge proxy and forwards plain HTTP
        // internally, so without this, generated asset/route URLs use http://
        // on an https:// page and get blocked as mixed content.
        if (! app()->environment('local')) {
            URL::forceScheme('https');
        }

        // Brevo has no official Laravel integration — this sends mail via
        // their HTTP API (api.brevo.com) instead of SMTP, since Railway's
        // free tier blocks outbound SMTP (port 587) but allows HTTPS.
        Mail::extend('brevo', function (array $config) {
            return new BrevoTransport($config['key'] ?? config('services.brevo.key'));
        });
    }
}
