<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Patient;
use App\Models\User;

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
        //
        Schema::defaultStringLength(191); 

        // Map sender_type values used in chat messages to actual model classes for morphTo
        Relation::morphMap([
            'patient' => Patient::class,
            'doctor' => User::class,
            'admin' => User::class,
            'staff' => User::class,
            'system' => User::class,
        ]);
    }
}
