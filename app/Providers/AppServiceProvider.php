<?php

namespace App\Providers;

use App\Models\Attendee;
use App\Models\Event;
use App\Policies\AttendeePolicy;
use App\Policies\EventPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;


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
        Schema::defaultStringLength(191);
        // Gate::define('update-event', function ($user, Event $event) {
        //     return $user->id === $event->user_id;
        // });
        // Gate::define('delete-attendee', function ($user, Event $event, Attendee $attendee) {
        //     return $user->id === $event->user_id || $user->id === $attendee->user_id;
        // });

        Gate::policy(Event::class, EventPolicy::class);
        Gate::policy(Attendee::class, AttendeePolicy::class);
    }
}
