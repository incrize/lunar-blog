<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\Policies;

use App\Models\User;
use CSCart\Lunar\Blog\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

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
     * @param \CSCart\Lunar\Blog\Models\Post $post
     *
     * @return bool
     */
    public function update(User $user, Post $post)
    {
        return ($user->is($post->author) && $user->can('blog-post:update')) || $user->can('blog-post:manage');
    }

    /**
     * Determine whether the user can update the model's tags relationship.
     *
     * @param \App\Models\User $user
     * @param \CSCart\Lunar\Blog\Models\Post $post
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function updateTags(User $user, Post $post)
    {
        return $this->update($user, $post);
    }

    /**
     * Determine whether the user can attach tags to post
     *
     * @param \App\Models\User $user
     * @param \CSCart\Lunar\Blog\Models\Post $post
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function attachTags(User $user, Post $post)
    {
        return $this->update($user, $post);
    }

    /**
     * Determine whether the user can detach tags from post
     *
     * @param \App\Models\User $user
     * @param \CSCart\Lunar\Blog\Models\Post $post
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function detachTags(User $user, Post $post)
    {
        return $this->update($user, $post);
    }

    /**
     * Determine whether the user can update the model's categories relationship.
     *
     * @param \App\Models\User $user
     * @param \CSCart\Lunar\Blog\Models\Post $post
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function updateCategories(User $user, Post $post)
    {
        return $this->update($user, $post);
    }

    /**
     * Determine whether the user can attach categories to post
     *
     * @param \App\Models\User $user
     * @param \CSCart\Lunar\Blog\Models\Post $post
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function attachCategories(User $user, Post $post)
    {
        return $this->update($user, $post);
    }

    /**
     * Determine whether the user can detach categories from post
     *
     * @param \App\Models\User $user
     * @param \CSCart\Lunar\Blog\Models\Post $post
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function detachCategories(User $user, Post $post)
    {
        return $this->update($user, $post);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User|null $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(?User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User|null          $user
     * @param \CSCart\Lunar\Blog\Models\Post $post
     *
     * @return bool
     */
    public function view(?User $user, Post $post)
    {
        if ($post->published_at) {
            return true;
        }

        return $user && $user->is($post->author);
    }

    /**
     * Determine whether the user can view the post's tags.
     *
     * @param \App\Models\User|null $user
     * @param \CSCart\Lunar\Blog\Models\Post $post
     *
     * @return bool
    */
    public function viewTags(?User $user, Post $post)
    {
        return $this->view($user, $post);
    }

    /**
     * Determine whether the user can view the post's categories.
     *
     * @param \App\Models\User|null $user
     * @param \CSCart\Lunar\Blog\Models\Post $post
     *
     * @return bool
     */
    public function viewCategories(?User $user, Post $post)
    {
        return $this->view($user, $post);
    }

    /**
     * Determine whether the user can view the post's author.
     *
     * @param \App\Models\User|null $user
     * @param \CSCart\Lunar\Blog\Models\Post $post
     *
     * @return bool
     */
    public function viewAuthor(?User $user, Post $post)
    {
        return $this->view($user, $post);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \CSCart\Lunar\Blog\Models\Post $post
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Post $post)
    {
        return $this->update($user, $post);
    }
}
