<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use App\Models\Traits\Listable;

class Tag extends BaseModel
{
    use HasSlug, Listable;

    public static $allowSearchFields = ['name', 'slug'];
    protected $fillable = ['name', 'slug', 'creator_id'];

    /**
     * 获得拥有此 tag 的文章。
     */
    public function posts()
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }

    public function slugMode()
    {
        return setting('tag_slug_mode');
    }
}
