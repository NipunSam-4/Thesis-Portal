<?php

namespace App\Providers;

use App\Models\DraftSynopsisCirculation;
use App\Models\Pts1Form;
use App\Models\Pts2Extension;
use App\Models\Pts2Form;
use App\Models\Pts3Form;
use App\Models\Pts4Extension;
use App\Models\Pts4Form;
use App\Policies\DraftSynopsisCirculationPolicy;
use App\Policies\Pts1Policy;
use App\Policies\Pts2ExtensionPolicy;
use App\Policies\Pts2Policy;
use App\Policies\Pts3Policy;
use App\Policies\Pts4ExtensionPolicy;
use App\Policies\Pts4Policy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // Register any application services.
    public function register(): void
    {
        //
    }

    // Bootstrap any application services.
    public function boot(): void
    {
        Gate::policy(Pts1Form::class, Pts1Policy::class);
        Gate::policy(Pts2Form::class, Pts2Policy::class);
        Gate::policy(Pts3Form::class, Pts3Policy::class);
        Gate::policy(Pts4Form::class, Pts4Policy::class);
        Gate::policy(Pts2Extension::class, Pts2ExtensionPolicy::class);
        Gate::policy(Pts4Extension::class, Pts4ExtensionPolicy::class);
        Gate::policy(DraftSynopsisCirculation::class, DraftSynopsisCirculationPolicy::class);
    }
}
