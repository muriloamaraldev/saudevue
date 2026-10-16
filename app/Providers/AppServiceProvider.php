<?php

namespace App\Providers;

use App\Contracts\PublishesEvents;
use App\Messaging\AmqpEventPublisher;
use App\Messaging\NullEventPublisher;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // O controller depende da interface. Com RABBITMQ_ENABLED=false
        // (testes, CI) cai no publisher que só grava em memória.
        $this->app->bind(PublishesEvents::class, function () {
            return config('rabbitmq.enabled')
                ? new AmqpEventPublisher
                : new NullEventPublisher;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
