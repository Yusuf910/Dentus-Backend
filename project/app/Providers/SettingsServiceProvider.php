<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Cache\Factory;
// use App\Models\Generalsetting;
class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Factory $cache/*, Generalsetting $settings*/)
    {
        // $GSsettings = Generalsetting::first();
        // config()->set('GSsettings', $GSsettings);
    }
}
