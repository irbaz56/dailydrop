<?php


namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Auth::class, function ($app) {
            $factory = (new Factory)
                ->withServiceAccount(storage_path('app/firebase_credentials.json'));
                
            return $factory->createAuth();
        });
    }
}
