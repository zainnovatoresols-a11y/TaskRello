<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\Board;
use App\Models\Card;
use App\Models\Comment;
use App\Models\Attachment;
use App\Policies\BoardPolicy;
use App\Policies\CardPolicy;
use App\Policies\CommentPolicy;
use App\Policies\AttachmentPolicy;
use App\Repositories\AttachmentRepository;
use App\Repositories\Contracts\AttachmentRepositoryInterface;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AttachmentRepositoryInterface::class,
            AttachmentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register all policies manually
        Gate::policy(Board::class,      BoardPolicy::class);
        Gate::policy(Card::class,       CardPolicy::class);
        Gate::policy(Comment::class,    CommentPolicy::class);
        Gate::policy(Attachment::class, AttachmentPolicy::class);
    }
}
