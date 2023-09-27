<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\JsonApi\Posts;

use CSCart\Lunar\Blog\Models\Post;
use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class TagRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        $tag = $this->model();
        /** @var \App\Models\User $user */
        $user = $this->user();
        $existsRule = Rule::exists(Post::class, 'id');

        if ($user->cannot('blog-category:manage')) {
            $existsRule->where('author_id', $user->getKey());
        }

        return [
            'title'  => [$tag ? 'filled' : 'required', 'string'],
            'postId' => [$existsRule]
        ];
    }
}
