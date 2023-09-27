<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\JsonApi\Posts;

use CSCart\Lunar\Blog\Models\PostTag;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

class TagSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = PostTag::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): iterable
    {
        return [
            ID::make(),
            Str::make('title')->sortable(),
            Number::make('postId'),
            DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('updatedAt')->sortable()->readOnly(),
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
}
