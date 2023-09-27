<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\JsonApi\Posts;

use CSCart\Lunar\Blog\Enums\CategoryStatus;
use CSCart\Lunar\Blog\Models\Category;
use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class PostRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        $post = $this->model();

        return [
            'title'           => [$post ? 'filled' : 'required', 'string'],
            'content'         => [$post ? 'filled' : 'required', 'string'],
            'publishedAt'     => ['nullable', JsonApiRule::dateTime()],
            'tags'            => [JsonApiRule::toMany()],
            'categories'      => [$post ? null : 'required', JsonApiRule::toMany()],
            'categories.*.id' => [Rule::exists(Category::class)->where('status', CategoryStatus::ACTIVE)],
        ];
    }
}
