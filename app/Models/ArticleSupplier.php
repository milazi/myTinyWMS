<?php

namespace Mss\Models;

use Mss\Models\Traits\GetAudits;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleSupplier extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;  // Trait for auditing changes to this model
    use GetAudits; //Trait to get formatted audits

    protected $guarded = []; // Attributes that are not mass assignable
    protected $auditsToDisplay = 20; // Number of audits to display

    public $incrementing = true; // Indicates if the IDs are auto-incrementing.

    protected $ignoredAuditFields = [
        'id', 'article_id'  // Fields to ignore in the audit logs
    ];

    /**
     * Returns the field names for this model.
     *
     * @return array
     */
    public static function getFieldNames(): array
    {
        return [
            'price' => __('Price'),
            'delivery_time' => __('Delivery Time'),
            'order_quantity' => __('Order Quantity'),
            'article_id' => __('Article ID'),
            'supplier_id' => __('Supplier'),
            'order_number' => __('Order Number'),
        ];
    }

    /**
     * Returns the audit name for this model.
     *
     * @return string
     */
    public static function getAuditName(): string
    {
        return __('Delivery options'); // Changed from 'Lieferoptionen' to 'Delivery options'
    }

    /**
     * Defines custom formatters for audit log values.
     *
     * @return array
     */
    protected function getAuditFormatters(): array
    {
        return [
            'price' => function ($value) {
                return formatPrice($value / 100); // Formats price (assumes price is stored in cents)
            },
            'supplier_id' => function ($value) {
                return optional(Supplier::find($value))->name; // Formats supplier ID to supplier name
            }
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
     * Defines the relationship with Supplier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Gets the name of the "updated at" column.
     * Overrides the default behavior for pivot tables.
     *
     * @return string
     */
    public function getUpdatedAtColumn(): string
    {
        if ($this->pivotParent) {
            return $this->pivotParent->getUpdatedAtColumn();
        }

        return static::UPDATED_AT;
    }

     /**
     * Formats an attribute value for the audit log.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getFormattedForAudit(string $key)
    {
        if (array_key_exists($key, $this->getAuditFormatters()) && is_callable($this->getAuditFormatters()[$key])) {
            return $this->getAuditFormatters()[$key]($this->{$key}); // Use custom formatter if available
        }

        return $this->{$key}; // Otherwise, return the raw value
    }
}
