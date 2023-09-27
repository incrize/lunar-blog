<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\Models;

use CSCart\Lunar\Blog\Enums\CategoryStatus;
use Lunar\Base\BaseModel;

/**
 * @property int $id
 * @property string $title
 * @property \CSCart\Lunar\Blog\Enums\CategoryStatus $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Category extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'blog_categories';

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'status', 'slug'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => CategoryStatus::class,
    ];
}
