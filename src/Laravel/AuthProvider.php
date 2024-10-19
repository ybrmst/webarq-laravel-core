<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/30/2017
 * Time: 6:53 PM
 */

namespace Webarq\Laravel;


use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AuthProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
// Get class manager
        $manager = 0 === strpos(\Request::path(), config('webarq.system.panel-url-prefix', 'admin-cp'))
                ? 'Webarq\Manager\AdminManager'
                : 'Webarq\Manager\MemberManager';

// Enable watchdog provider in to authentication
        Auth::provider('watchdog', function () use ($manager) {
// Return an instance of Illuminate\Contracts\Auth\UserProvider...
            return new WatchdogProvider(new BcryptHasher(), new $manager);
        });
    }

}