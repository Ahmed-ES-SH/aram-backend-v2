<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;

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
    // public function boot(): void
    // {
    //     ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
    //         return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
    //     });

    //     Relation::morphMap([
    //         'user' => \App\Models\User::class,
    //         'organization' => \App\Models\Organization::class,
    //     ]);
    // }


    public function boot()
    {
        Relation::morphMap([
            'user' => \App\Models\User::class,
            'organization' => \App\Models\Organization::class,
        ]);

        Storage::extend('google', function ($app, $config) {
            $client = new \Google_Client();
            $client->setAuthConfig($config['service_account_json']);
            $client->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
            $service = new \Google_Service_Drive($client);
            $adapter = new GoogleDriveAdapter($service, $config['folderId']);

            return new Filesystem($adapter);
        });
    }
}
