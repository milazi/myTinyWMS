<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ArticleGroup
 *
 * @property integer id
 * @property string name
 * @property string external_article_number
 * @property ArticleGroupItem[]|Collection items
 * @method static ArticleGroup first()
 * @package Mss\Models
 */
class ArticleGroup extends AuditableModel
{
    protected $fillable = ['name', 'external_article_number'];

    /**
     * Defines the relationship with ArticleGroupItem.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(ArticleGroupItem::class);
    }

    /**
     * @inheritDoc
     */
    public static function getAuditName(): string
    {
        return __('Article group');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldNames(): array
    {
        return [
            'name' => __('Name')
        ];
    }

    /**
     * Generates a formatted article number.
     *
     * @return string
     */
    public function getArticleNumber(): string
    {
        return sprintf("AG%08s\n", $this->id);
    }
}
