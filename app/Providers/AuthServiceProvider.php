<?php

namespace App\Providers;

use App\Models\Comment;
use App\Policies\CommentPolicy;
use App\Services\GoogleAuthService;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Comment::class => CommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register service bindings
        $this->app->singleton(UserRepository::class, function () {
            return new UserRepository();
        });
        
        $this->app->singleton(GoogleAuthService::class, function ($app) {
            return new GoogleAuthService(
                $app->make(UserRepository::class)
            );
        });
    }
}