<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lunar\Base\BaseModel;

/**
 * @property int $id
 * @property int $post_id
 * @property \CSCart\Lunar\Blog\Models\Post $post
 * @property string $title
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class PostTag extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'blog_post_tags';

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'post_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
