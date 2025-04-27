<?php

namespace Mss\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class ArticleQuantityChangelog
 *
 * @property integer id
 * @property integer type
 * @property integer change
 * @property string note
 * @property Article $article
 * @property DeliveryItem $deliveryItem
 * @package Mss\Models
 */
class ArticleQuantityChangelog extends AuditableModel
{
    use SoftDeletes;

    // Constants defining different types of quantity changes
    const TYPE_START = 0;
    const TYPE_INCOMING = 1;
    const TYPE_OUTGOING = 2;
    const TYPE_CORRECTION = 3;
    const TYPE_COMMENT = 6;  // This type is not used for quantity changes, but for comments/notes.
    const TYPE_INVENTORY = 7;
    const TYPE_REPLACEMENT_DELIVERY = 8; // No quantity change
    const TYPE_OUTSOURCING = 9;        // No quantity change
    const TYPE_SALE_TO_THIRD_PARTIES = 10;
    const TYPE_TRANSFER = 11;
    const TYPE_RETOURE = 12;

    protected $fillable = ['created_at', 'updated_at', 'user_id', 'type', 'change', 'new_quantity', 'note', 'delivery_item_id', 'unit_id', 'article_id', 'related_id'];

    protected $dates = ['deleted_at'];

    /**
     * The "booted" method of the model.
     *
     * This method is called when the model is booted.  It sets up a deleting event.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($changelog) {
            /** @var ArticleQuantityChangelog $changelog */

            // If the changelog has a related delivery item, delete that delivery item as well.
            if ($changelog->deliveryItem) {
                $changelog->deliveryItem->delete();
            }

            // Resets the article quantity based on the changelog entry being deleted.
            $changelog->article->resetQuantityFromChangelog($changelog);
        });
    }

    /**
     * Returns the field names for this model.
     *
     * @return array
     */
    public static function getFieldNames(): array
    {
        return [];
    }

    /**
     * Returns the audit name for this model.
     *
     * @return string
     */
    public static function getAuditName(): string
    {
        return __('Article Quantity Change');
    }

    /**
     * Returns the abbreviation for a given changelog type.
     *
     * @param int $key
     * @return string
     */
    public static function getAbbreviation(int $key): string
    {
        $abbreviations = collect([
            self::TYPE_INCOMING => __('GR'), // Goods Receipt
            self::TYPE_OUTGOING => __('GI'), // Goods Issue
            self::TYPE_CORRECTION => __('CB'), // Correction Booking
            self::TYPE_INVENTORY => __('INV'), // Inventory
            self::TYPE_OUTSOURCING => __('OS'), // Outsourcing
            self::TYPE_REPLACEMENT_DELIVERY => __('RD'), // Replacement Delivery
            self::TYPE_SALE_TO_THIRD_PARTIES => __('SaTP'), // Sale to Third Parties
            self::TYPE_TRANSFER => __('TR'), // Transfer
            self::TYPE_COMMENT => __('CO'), // Comment
            self::TYPE_RETOURE => __('RET'),
        ]);

        return $abbreviations->get($key, __('Unknown'));
    }

    /**
     * Returns an array of available changelog types.
     *
     * @return array
     */
    public static function getAvailableTypes(): array
    {
        return [
            self::TYPE_START,
            self::TYPE_INCOMING,
            self::TYPE_OUTGOING,
            self::TYPE_CORRECTION,
            self::TYPE_COMMENT,
            self::TYPE_INVENTORY,
            self::TYPE_REPLACEMENT_DELIVERY,
            self::TYPE_OUTSOURCING,
            self::TYPE_SALE_TO_THIRD_PARTIES,
            self::TYPE_TRANSFER,
            self::TYPE_RETOURE,
        ];
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
     * Defines the relationship with User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Defines the relationship with Unit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Defines the relationship with DeliveryItem.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryItem()
    {
        return $this->belongsTo(DeliveryItem::class);
    }

     /**
     * Defines the relationship with another ArticleQuantityChangelog entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function related()
    {
        return $this->belongsTo(ArticleQuantityChangelog::class, 'related_id', 'id');
    }
}
