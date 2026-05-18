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
use App\Repositories\BoardRepository;
use App\Repositories\Contracts\BoardRepositoryInterface;
use App\Repositories\ListRepository;
use App\Repositories\Contracts\ListRepositoryInterface;
use App\Repositories\CardRepository;
use App\Repositories\Contracts\CardRepositoryInterface;
use App\Repositories\LabelRepository;
use App\Repositories\Contracts\LabelRepositoryInterface;
use App\Repositories\CommentRepository;
use App\Repositories\Contracts\CommentRepositoryInterface;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use App\Services\ConversationService;
use App\Services\MessageService;
use App\Repositories\ActivityRepository;
use App\Repositories\Contracts\ActivityRepositoryInterface;
use App\Repositories\ConversationRepository;
use App\Repositories\Contracts\ConversationRepositoryInterface;
use Illuminate\Support\Facades\URL;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->bind(LabelRepositoryInterface::class, LabelRepository::class);
        $this->app->bind(CardRepositoryInterface::class, CardRepository::class);
        $this->app->bind(BoardRepositoryInterface::class, BoardRepository::class);
        $this->app->bind(ListRepositoryInterface::class, ListRepository::class);
        $this->app->bind(
            AttachmentRepositoryInterface::class,
            AttachmentRepository::class
        );
        $this->app->singleton(ConversationService::class);
        $this->app->singleton(MessageService::class);
        $this->app->singleton(TimeTrackerService::class);


        $this->app->bind(ActivityRepositoryInterface::class, ActivityRepository::class);
        $this->app->bind(ConversationRepositoryInterface::class, ConversationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Board::class,      BoardPolicy::class);
        Gate::policy(Card::class,       CardPolicy::class);
        Gate::policy(Comment::class,    CommentPolicy::class);
        Gate::policy(Attachment::class, AttachmentPolicy::class);

        ResetPassword::toMailUsing(function ($notifiable, $token) {

            $url = url('/reset-password/' . $token . '?email=' . $notifiable->email);

            return (new MailMessage)
                ->subject('Reset Your Password')
                ->view('emails.reset-password', [
                    'user' => $notifiable,
                    'url' => $url,
                ]);
        });

        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}
