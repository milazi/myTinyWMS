<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Collective\Html\Eloquent\FormAccessible;

/**
 * Class Category
 *
 * @property integer id
 * @property string name
 * @method static \Illuminate\Database\Query\Builder orderedByName()
 * @package Mss\Models
 */
class Category extends AuditableModel
{
    use SoftDeletes, FormAccessible;

    protected $fillable = [
        'name', 'notes', 'show_in_to_order_on_dashboard'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'show_in_to_order_on_dashboard' => 'boolean' // Cast to boolean
    ];

    /**
     * Returns the field names for this model.
     *
     * @return array
     */
    public static function getFieldNames(): array
    {
        return [
            'name' => __('Name'),
            'notes' => __('Notes')
        ];
    }

    /**
     * Returns the audit name for this model.
     *
     * @return string
     */
    public static function getAuditName(): string
    {
        return __('Category');
    }

    /**
     * Defines the relationship with Article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Scope a query to order categories by name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderedByName($query)
    {
        $query->orderBy('name');
    }

     /**
     * Scope a query to include active articles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithActiveArticles($query)
    {
        $query->with(['articles' => function ($query) {
            $query->enabled()->orderedByArticleNumber();
        }]);
    }

    /**
     * Form access attribute for showing on dashboard.
     *
     * @return int
     */
    public function formShowInToOrderOnDashboardAttribute(): int
    {
        return $this->show_in_to_order_on_dashboard ? 1 : 0;
    }
}
