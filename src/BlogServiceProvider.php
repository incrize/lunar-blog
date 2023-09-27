<?php
declare(strict_types=1);

namespace CSCart\Lunar\Blog;

use CSCart\Lunar\Blog\JsonApi\BlogServer;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Http\Controllers\JsonApiController;
use LaravelJsonApi\Laravel\Routing\Relationships;

class BlogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Register JSON:API Servers
        $this->app['config']->set('jsonapi.servers', array_merge((array) $this->app['config']->get('jsonapi.servers'), [
            'blog' => BlogServer::class
        ]));

        $this->callAfterResolving(Router::class, function () {
            JsonApiRoute::server('blog')->prefix('api/blog')->resources(function ($server) {
                $server->resource('posts', JsonApiController::class)
                    ->relationships(function (Relationships $relations) {
                        $relations->hasOne('author')->readOnly();
                        $relations->hasMany('categories');
                        $relations->hasMany('tags');
                    });
                $server->resource('categories', JsonApiController::class);
                $server->resource('tags', JsonApiController::class);
            });
        });
    }
}
