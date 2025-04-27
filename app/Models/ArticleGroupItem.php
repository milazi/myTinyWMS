<?php

namespace Mss\Models;

/**
 * Class ArticleGroupItem
 *
 * @property integer id
 * @property integer article_group_id
 * @property integer article_id
 * @property integer quantity
 * @property Article article
 * @package Mss\Models
 */
class ArticleGroupItem extends AuditableModel
{
    protected $fillable = ['article_id', 'quantity'];

    /**
     * Defines the relationship with ArticleGroup.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class);
    }

    /**
     * Defines the relationship with Article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * @inheritDoc
     */
    public static function getAuditName(): string
    {
        return __('Article group article');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldNames(): array
    {
        return [
            'quantity' => __('Quantity')
        ];
    }
}
