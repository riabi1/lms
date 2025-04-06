<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Models\SiteSetting;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $siteSettings = SiteSetting::firstOrCreate(
                ['id' => 1],
                [
                    'phone' => '+216 28-587-753',
                    'email' => 'lmspfee@gmail.com',
                    'logo' => 'frontend/images/logo.png',
                ]
            );
            $view->with('siteSettings', $siteSettings);
        });
    }

    public function register()
    {
        //
    }
}