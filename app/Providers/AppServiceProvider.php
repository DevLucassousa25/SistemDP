<?php

namespace App\Providers;

use App\Models\Manifestacao;
use App\Models\room as Room;
use App\Models\User;
use App\Policies\ManifestacaoPolicy;
use App\Policies\RoomPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Policies explícitas (além da auto-descoberta do Laravel)
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Room::class, RoomPolicy::class);
        Gate::policy(Manifestacao::class, ManifestacaoPolicy::class);
    }
}
