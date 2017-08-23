<?php

namespace App\Models;

use App\Models\Traits\Listable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Post extends BaseModel
{
    use SoftDeletes, Listable;

    protected $fillable = ['title', 'user_id', 'author_info', 'excerpt', 'type', 'views_count', 'cover', 'status', 'template', 'top', 'published_at', 'category_id'];
    protected $dates = ['deleted_at', 'top', 'published_at', 'created_at', 'updated_at'];
    protected static $allowSearchFields = ['title', 'author_info', 'excerpt'];
    protected static $allowSortFields = ['title', 'status', 'views_count', 'top', 'order', 'published_at', 'category_id'];

    const STATUS_PUBLISH = 'publish', STATUS_DRAFT = 'draft';

    public function scopeRecent($query)
    {
        return $query->orderBy('published_at', 'desc')->orderBy('created_at', 'desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeApplyFilter($query, $data)
    {
        $data = $data->only('q', 'status', 'orders', 'only_trashed');
        $query->orderByTop();
        $query->post();

        if (isset($data['q'])) {
            $query->withSimpleSearch($data['q']);
        }

        if (isset($data['orders'])) {
            $query->withSort($data['orders']);
        }

        switch ($data['status']) {
            case 'publish':
                $query->publish();
                break;
            case 'draft':
                $query->draft();
            default:
                $query->publishAndDraft();
        }
        if (isset($data['only_trashed']) && $data['only_trashed']) {
            $query->onlyTrashed();
        }
        return $query->ordered()->recent();
    }

    public function scopeByType($query, $type)
    {
        if (in_array($type, [Category::TYPE_POST, Category::TYPE_PAGE]))
            return $query->where('type', $type);
        return $query;
    }

    public function scopeByStatus($query, $status)
    {
        if (in_array($status, [static::STATUS_PUBLISH, static::STATUS_DRAFT]))
            return $query->where('status', $status);
        return $query;
    }

    /**
     * 获取已发布或草稿的文章的查询作用域
     * @param $query
     * @return mixed
     */
    public function scopePublishOrDraft($query)
    {
        return $query->where('status', static::STATUS_PUBLISH)->orWhere('status', static::STATUS_DRAFT);
    }

    /**
     * 已发布文章的查询作用域
     * @param $query
     * @return mixed
     */
    public function scopePublishPost($query)
    {
        return $query->where(function ($query) {
            $query->byType(Category::TYPE_POST)->byStatus(static::STATUS_PUBLISH);
        });
    }

    public function scopeOrderByTop($query)
    {
        return $query->orderBy('top', 'DESC');
    }

    public function addViewCount()
    {
        Post::where($this->getKeyName(), $this->getKey())->increment('views_count');
        $this->views_count++;
    }

    /**
     * 一对一关联 post_content 表
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function postContent()
    {
        return $this->hasOne(PostContent::class);
    }

    /**
     * 添加附加表数据
     * @param $data
     */
    public function addition($data)
    {
        if (isset($data['content'])) {
            $this->postContent()->updateOrCreate(
                [], [
                    'content' => $data['content']
                ]
            );
        }
    }

    /**
     * 文章是否置顶
     */
    public function isTop()
    {
        return !is_null($this->top);
    }

    /**
     * 获取下一篇文章
     * @return mixed
     */
    public function getNextPost()
    {
        return $this->category->posts()->publishPost()->where('id', '>', $this->id)->first();
    }

    public function isPublish()
    {
        return $this->status == static::STATUS_PUBLISH;
    }

    public function isDraft()
    {
        return $this->status == static::STATUS_DRAFT;
    }
}