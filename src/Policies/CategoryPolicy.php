<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\Policies;

use App\Models\User;
use CSCart\Lunar\Blog\Enums\CategoryStatus;
use CSCart\Lunar\Blog\Models\Category;
use CSCart\Lunar\Blog\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User|null $user
     * @param \CSCart\Lunar\Blog\Models\Category $category
     *
     * @return bool
     */
    public function view(?User $user, Category $category)
    {
        if ($category->status === CategoryStatus::ACTIVE) {
            return true;
        }

        return $user && $user->can('blog-category:manage');
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
        return $user->can('blog-category:manage');
    }

    /**
     * Determine whether the user can update models.
     *
     * @param \App\Models\User $user
     * @param \CSCart\Lunar\Blog\Models\Category $category
     *
     * @return bool
     */
    public function update(User $user, Category $category)
    {
        return $user->can('blog-category:manage');
    }

    /**
     * Determine whether the user can update models.
     *
     * @param \App\Models\User $user
     * @param \CSCart\Lunar\Blog\Models\Category $category
     *
     * @return bool
     */
    public function delete(User $user, Category $category)
    {
        return $user->can('blog-category:manage');
    }
}
