<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Base\BaseModel;

/**
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property string $content
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $published_at
 * @property \App\Models\User $author
 * @property \Illuminate\Database\Eloquent\Collection<array-key, \CSCart\Lunar\Blog\Models\Category> $categories
 */
class Post extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'blog_posts';

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'content', 'published_at'];

    /**
     * @var array
     */
    protected $casts = ['published_at' => 'datetime'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'lunar_blog_post_categories')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags(): HasMany
    {
        return $this->hasMany(PostTag::class);
    }
}
