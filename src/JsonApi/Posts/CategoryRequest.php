<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\JsonApi\Posts;

use CSCart\Lunar\Blog\Enums\CategoryStatus;
use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class CategoryRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        $category = $this->model();
        $uniqueSlug = Rule::unique('lunar_blog_categories', 'slug');

        if ($category) {
            $uniqueSlug->ignoreModel($category);
        }

        return [
            'title'  => [$category ? null : 'required', 'string'],
            'status' => [$category ? null : 'required', Rule::enum(CategoryStatus::class)],
            'slug'   => [$category ? null : 'required', 'string', $uniqueSlug]
        ];
    }
}
