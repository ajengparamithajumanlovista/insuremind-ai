<?php

namespace App\Providers;

use App\Models\User;
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
     *
     * The gates below follow the principle of least privilege: reviewers
     * receive only the permissions needed to review AI cases, while catalog,
     * account, and audit log management permissions are reserved for admins.
     */
    public function boot(): void
    {
        Gate::define('access-admin-dashboard', fn (User $user) => $user->isAdmin());

        Gate::define('access-ai-review', fn (User $user) => $user->isReviewer());

        Gate::define('manage-products', fn (User $user) => $user->isAdmin());

        Gate::define('manage-users', fn (User $user) => $user->isAdmin());

        Gate::define('view-audit-log', fn (User $user) => $user->isAdmin());

        Gate::define('export-audit-log', fn (User $user) => $user->isAdmin());

        // Reviewers cannot freely view other users' conversations or documents;
        // they can only access cases assigned to them or cases that remain open.
        Gate::define('review-case', function (User $user, ?User $owner = null) {
            if ($user->isAdmin()) {
                return true;
            }

            if (! $user->isReviewer()) {
                return false;
            }

            return $owner === null || $owner->getKey() === $user->getKey();
        });
    }
}
