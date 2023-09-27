<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\JsonApi\Posts;

use CSCart\Lunar\Blog\Enums\CategoryStatus;
use CSCart\Lunar\Blog\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

class CategorySchema extends Schema
{
    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Category::class;

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
            Str::make('status')->sortable()->hidden(static fn($request) => !$request->user()?->can('blog-category:manage')),
            Str::make('slug')->sortable(),
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
        /** @var \App\Models\User|null $user */
        $user = optional($request)->user();

        if (!$user || $user->cannot('blog-category:manage')) {
            $query->where('status', '=', CategoryStatus::ACTIVE);
        }

        return $query;
    }

    /**
     * Build a "relatable" query for this resource.
     *
     * @param \Illuminate\Http\Request|null $request
     * @param \Illuminate\Database\Eloquent\Relations\Relation $query
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function relatableQuery(?Request $request, Relation $query): Relation
    {
        /** @var \App\Models\User|null $user */
        $user = optional($request)->user();

        if (!$user || !$user->cannot('blog-category:manage')) {
            $query->where('status', '=', CategoryStatus::ACTIVE);
        }

        return $query;
    }
}
