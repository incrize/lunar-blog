<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\JsonApi\Posts;

use CSCart\Lunar\Blog\Enums\CategoryStatus;
use CSCart\Lunar\Blog\JsonApi\Filters\ContainsFilter;
use CSCart\Lunar\Blog\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsToMany;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereHas;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

class PostSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Post::class;

    /**
     * The maximum include path depth.
     *
     * @var int
     */
    protected int $maxDepth = 3;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            ID::make(),
            Str::make('title')->sortable(),
            Str::make('content'),
            DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('updatedAt')->sortable()->readOnly(),
            DateTime::make('publishedAt')->sortable(),
            BelongsTo::make('author')->type('users')->readOnly(),
            HasMany::make('tags')->type('tags')->deleteDetachedModels(),
            BelongsToMany::make('categories')->type('categories')
        ];
    }

    /**
     * Get the resource filters.
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            WhereIdIn::make($this),
            Where::make('title'),
            ContainsFilter::make('content'),
            WhereHas::make($this, 'categories')
        ];
    }

    /**
     * Get the resource paginator.
     *
     * @return \LaravelJsonApi\Eloquent\Contracts\Paginator|null
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }


    /**
     * Build an index query for this resource.
     *
     * @param \Illuminate\Http\Request|null $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function indexQuery(?Request $request, Builder $query): Builder
    {
        if ($user = optional($request)->user()) {
            return $query->where(function (Builder $q) use ($user) {
                return $q->whereNotNull('published_at')->orWhere('author_id', $user->getKey());
            });
        }

        $query->whereHas('categories', function (Builder $q) {
            $q->where('status', '=', CategoryStatus::ACTIVE);
        });

        return $query->whereNotNull('published_at');
    }
}
