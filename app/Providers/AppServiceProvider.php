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
     * Gate di bawah menerapkan prinsip least privilege: reviewer hanya
     * memperoleh hak yang dibutuhkan untuk memeriksa kasus AI, sedangkan
     * hak pengelolaan katalog, akun, dan audit log hanya dimiliki admin.
     */
    public function boot(): void
    {
        Gate::define('access-admin-dashboard', fn (User $user) => $user->isAdmin());

        Gate::define('access-ai-review', fn (User $user) => $user->isReviewer());

        Gate::define('manage-products', fn (User $user) => $user->isAdmin());

        Gate::define('manage-users', fn (User $user) => $user->isAdmin());

        Gate::define('view-audit-log', fn (User $user) => $user->isAdmin());

        Gate::define('export-audit-log', fn (User $user) => $user->isAdmin());

        // Reviewer tidak melihat percakapan atau dokumen milik pengguna lain
        // secara bebas; hanya kasus yang benar-benar ditugaskan kepadanya
        // atau yang masih terbuka.
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
