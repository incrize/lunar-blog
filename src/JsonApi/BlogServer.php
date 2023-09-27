<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\JsonApi;

use CSCart\Lunar\Blog\JsonApi\Posts\CategorySchema;
use CSCart\Lunar\Blog\JsonApi\Posts\PostSchema;
use CSCart\Lunar\Blog\JsonApi\Posts\TagSchema;
use CSCart\Lunar\Blog\JsonApi\Posts\UserSchema;
use CSCart\Lunar\Blog\Models\Post;
use Illuminate\Support\Facades\Auth;
use LaravelJsonApi\Core\Server\Server;

class BlogServer extends Server
{
    /**
     * The base URI namespace for this server.
     *
     * @var string
     */
    protected string $baseUri = '/api/blog';

    /**
     * Bootstrap the server when it is handling an HTTP request.
     *
     * @return void
     */
    public function serving(): void
    {
        Auth::shouldUse('sanctum');

        Post::creating(static function (Post $post): void {
            $post->author()->associate(Auth::user());
        });
    }

    /**
     * Get the server's list of schemas.
     *
     * @return array
     */
    protected function allSchemas(): array
    {
        return [
            PostSchema::class,
            CategorySchema::class,
            TagSchema::class,
            UserSchema::class
        ];
    }
}
