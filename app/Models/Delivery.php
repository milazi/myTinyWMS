<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Delivery
 *
 * @property integer id
 * @property Order $order
 * @property Collection|DeliveryItem[] $items
 * @package Mss\Models
 */
class Delivery extends AuditableModel
{
    use SoftDeletes;

    protected $fillable = ['delivery_note_number', 'delivery_date', 'notes', 'order_id'];
    protected $dates = ['delivery_date'];

    /**
     * The "booted" method of the model.
     *
     * This method is called when the model is booted.  It defines a 'deleted' event.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($delivery) {
            /** @var Delivery $delivery */
            // Adjust the order status when a delivery is deleted.
            if ($delivery->order->isFullyDelivered()) {
                $delivery->order->status = Order::STATUS_DELIVERED;
            } elseif ($delivery->order->isPartiallyDelivered()) {
                $delivery->order->status = Order::STATUS_PARTIALLY_DELIVERED;
            } else {
                $delivery->order->status = Order::STATUS_ORDERED;
            }

            $delivery->order->save();
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
        return __('Delivery');
    }

    /**
     * Defines the relationship with Order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Defines the relationship with DeliveryItem.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(DeliveryItem::class);
    }
}
