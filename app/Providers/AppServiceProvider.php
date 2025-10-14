<?php

namespace App\Providers;

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
        // Share news_count and ideas_count with all views
        view()->composer('*', function ($view) {
            $news_count = \App\Models\News::where('is_published', true)->count();
            $ideas_count = \App\Models\Idea::approved()->count(); // Only approved ideas

            $view->with('news_count', $news_count);
            $view->with('ideas_count', $ideas_count);
        });
    }
}
