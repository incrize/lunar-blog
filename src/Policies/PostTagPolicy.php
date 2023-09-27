<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\Policies;

use App\Models\User;
use CSCart\Lunar\Blog\Models\PostTag;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostTagPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User|null $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(?User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User|null $user
     * @param \CSCart\Lunar\Blog\Models\PostTag $tag
     *
     * @return bool
     */
    public function view(?User $user, PostTag $tag)
    {
        return ($user->is($tag->post->author) && $user->can('blog-post:update')) || $user->can('blog-post:manage');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('blog-post:create') || $user->can('blog-post:manage');
    }

    /**
     * Determine whether the user can update models.
     *
     * @param \App\Models\User $user
     * @param \CSCart\Lunar\Blog\Models\PostTag $tag
     *
     * @return bool
     */
    public function update(User $user, PostTag $tag)
    {
        return ($user->is($tag->post->author) && $user->can('blog-post:update')) || $user->can('blog-post:manage');
    }
}
