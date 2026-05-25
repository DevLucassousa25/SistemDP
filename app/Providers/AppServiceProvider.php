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
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Room::class, RoomPolicy::class);
        Gate::policy(Manifestacao::class, ManifestacaoPolicy::class);
    }
}
