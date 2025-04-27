<?php

namespace Mss\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Mss\Models\Traits\Taggable;
use Mss\Notifications\NewCorrectionForChangeFromDifferentMonth;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class Article
 *
 * @property integer id
 * @property string internal_article_number
 * @property string external_article_number
 * @property integer quantity
 * @property integer min_quantity
 * @property integer category_id
 * @property integer outsourcing_quantity
 * @property integer replacement_delivery_quantity
 * @property ArticleSupplier currentSupplierArticle
 * @property Category category
 * @property ArticleQuantityChangelog[]|Collection quantityChangelogs
 * @method static Builder active()
 * @method static Builder enabled()
 * @method static Builder withCurrentSupplierArticle()
 * @method static Builder withCurrentSupplier()
 * @method static Article first()
 * @package Mss\Models
 */
class Article extends AuditableModel
{
    use SoftDeletes, Taggable;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_NO_ORDERS = 2;

    protected $auditsToDisplay = 50;

    const INVENTORY_TYPE_SPARE_PARTS = 0;
    const INVENTORY_TYPE_CONSUMABLES = 1;

    const PACKAGING_CATEGORY_PAPER = 'paper';
    const PACKAGING_CATEGORY_PLASTIC = 'plastic';
    const PACKAGING_CATEGORY_METAL = 'metal';

    protected $fillable = [
        'name', 'internal_article_number', 'external_article_number', 'unit_id', 'category_id', 'status', 'quantity',
        'min_quantity', 'usage_quantity', 'issue_quantity', 'sort_id', 'inventory', 'notes', 'order_notes',
        'free_lines_in_printed_list', 'cost_center', 'weight', 'packaging_category', 'delivery_notes'
    ];

    protected $casts = [
        'files' => 'array' // Cast 'files' attribute to an array
    ];

    protected $dates = ['deleted_at']; // Treat 'deleted_at' as a Carbon date

    protected $hidden = ['files']; // Hide 'files' attribute from serialization

    /**
     * Returns an array of field names and their corresponding translatable labels.
     *
     * @return array
     */
    public static function getFieldNames(): array
    {
        return [
            'name' => __('Name'),
            'notes' => __('Notes'),
            'external_article_number' => __('External Article Number'),
            'internal_article_number' => __('Internal Article Number'),
            'article_number' => __('Internal Article Number'), // for the old column name
            'status' => __('Status'),
            'unit_id' => __('Unit'),
            'quantity' => __('Stock'),
            'min_quantity' => __('Minimum Stock'),
            'issue_quantity' => __('Issue Quantity'),
            'order_notes' => __('Order Notes'),
            'category_id' => __('Category'),
            'sort_id' => __('Sort'),
            'inventory' => __('Inventory Type'),
            'inventory_text' => __('Inventory Type'),
            'files' => __('Files'),
            'cost_center' => __('Cost Center'),
            'packaging_category' => __('Packaging Category'),
            'free_lines_in_printed_list' => __('Free Lines in Printed List'),
            'delivery_notes' => __('Delivery Notes')
        ];
    }

    /**
     * Returns the translatable name for auditing.
     *
     * @return string
     */
    public static function getAuditName(): string
    {
        return __('Article');
    }

    /**
     * Defines the relationship with ArticleGroupItems.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articleGroupItems()
    {
        return $this->hasMany(ArticleGroupItem::class);
    }

    /**
     * Defines the relationship with ArticleQuantityChangelogs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quantityChangelogs()
    {
        return $this->hasMany(ArticleQuantityChangelog::class);
    }

     /**
     * Defines the relationship with Tags.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
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
     * Defines the relationship with ArticleSuppliers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supplierArticles()
    {
        return $this->hasMany(ArticleSupplier::class);
    }

    /**
     * Defines the relationship with Suppliers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class)
            ->withTimestamps()
            ->withPivot('order_number', 'price', 'delivery_time', 'order_quantity')
            ->using(ArticleSupplier::class);
    }

     /**
     * Defines the relationship with the current Supplier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentSupplier()
    {
        return $this->hasOne(Supplier::class, 'id', 'current_supplier_id')->withTrashed();
    }

    /**
      * Scope a query to include supplier name.
      *
      * @param  \Illuminate\Database\Eloquent\Builder  $query
      * @return \Illuminate\Database\Eloquent\Builder
      */
    public function scopeWithCurrentSupplierName($query)
    {
        $query->addSubSelect('supplier_name', Supplier::select('name')
            ->whereRaw('current_supplier_id = suppliers.id')
        );
    }

    /**
     * Scope a query to include the current supplier.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentSupplier($query)
    {
        $query->addSubSelect('current_supplier_id', ArticleSupplier::select('supplier_id')
            ->whereRaw('article_id = articles.id')
            ->latest()
        )->with('currentSupplier');
    }

    /**
     * @return ArticleSupplier
     */
    public function currentSupplierArticle()
    {
        return $this->hasOne(ArticleSupplier::class, 'id', 'current_supplier_article_id');
    }

    public function scopeWithCurrentSupplierArticle($query)
    {
        $query->addSubSelect('current_supplier_article_id', ArticleSupplier::select('id')
            ->whereRaw('article_id = articles.id')
            ->latest()
        )->with('currentSupplierArticle');
    }

     /**
     * Retrieves the current supplier article.
     *
     * @return ArticleSupplier
     */
    public function getCurrentSupplierArticle()
    {
        return Article::where('id', $this->id)->withCurrentSupplierArticle()->first()->currentSupplierArticle;
    }

    /**
     * Defines the relationship with Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Defines the relationship with ArticleNotes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articleNotes()
    {
        return $this->hasMany(ArticleNote::class);
    }

    /**
     * Defines the relationship with OrderItems.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Formats the quantity with the unit name.
     *
     * @param mixed $value
     * @return string
     */
    public function formatQuantity($value): string
    {
        return (!empty($value) || $value === 0) ? $value . ' ' . $this->unit->name : $value;
    }

    /**
     * Sets a new article number for the article.
     *
     * @return bool
     */
    public function setNewArticleNumber(): bool
    {
        if (!$this->category) {
            return false;
        }

        $this->internal_article_number = null;
        $this->save();

        $categoryPart = $this->category->id + 10;
        $latestArticleNumber = Article::where('internal_article_number', 'like', $categoryPart . '%')->max('internal_article_number');
        if ($latestArticleNumber) {
            $number = intval(substr($latestArticleNumber, strlen($categoryPart)));
            $newNumber = ++$number;
        } else {
            $newNumber = 1;
        }

        $this->internal_article_number = $categoryPart . sprintf('%03d', $newNumber);
        $this->save();
        return true;
    }

    /**
     * Changes the quantity of the article and creates a changelog entry.
     *
     * @param integer $change
     * @param integer $type
     * @param string $note
     * @param DeliveryItem|null $deliveryItem
     * @param integer|null $relatedId
     * @return void
     */
    public function changeQuantity(int $change, int $type, string $note = '', DeliveryItem $deliveryItem = null, ?int $relatedId = null): void
    {
        switch ($type) {
            case ArticleQuantityChangelog::TYPE_REPLACEMENT_DELIVERY:
                $newQuantity = $this->quantity;
                $this->replacement_delivery_quantity = ($this->replacement_delivery_quantity - $change);
                break;

            case ArticleQuantityChangelog::TYPE_OUTSOURCING:
                $newQuantity = $this->quantity;
                $this->outsourcing_quantity = ($this->outsourcing_quantity - $change);
                break;

            default:
                $newQuantity = ($this->quantity + $change);
                $this->quantity = ($this->quantity + $change);
                break;
        }

        if ($type == ArticleQuantityChangelog::TYPE_CORRECTION && !empty($relatedId)) {
            $relatedItem = ArticleQuantityChangelog::find($relatedId);
            if ($relatedItem && $relatedItem->created_at->format('Y-m') !== Carbon::now()->format('Y-m')) {
                Notification::send(
                    UserSettings::getUsersWhereTrue(UserSettings::SETTING_NOTIFY_ABOUT_CORRECTION_ON_CHANGE_OF_OTHER_MONTH),
                    new NewCorrectionForChangeFromDifferentMonth($this)
                );
            }
        }

        if ($newQuantity < 0) {
            $newQuantity = 0;
        }

        $this->quantityChangelogs()->create([
            'user_id' => Auth::id(),
            'type' => $type,
            'change' => $change,
            'new_quantity' => $newQuantity,
            'note' => $note,
            'delivery_item_id' => optional($deliveryItem)->id,
            'unit_id' => $this->unit_id,
            'related_id' => $relatedId
        ]);

        $this->save();
    }

     /**
     * Resets the quantity based on a given ArticleQuantityChangelog.
     *
     * @param ArticleQuantityChangelog $articleQuantityChangelog
     * @return void
     */
    public function resetQuantityFromChangelog(ArticleQuantityChangelog $articleQuantityChangelog): void
    {
        if (!in_array($articleQuantityChangelog->type, [ArticleQuantityChangelog::TYPE_REPLACEMENT_DELIVERY, ArticleQuantityChangelog::TYPE_OUTSOURCING])) {
            $change = $articleQuantityChangelog->change * -1;
            $this->quantity += $change;
        } elseif ($articleQuantityChangelog->type == ArticleQuantityChangelog::TYPE_OUTSOURCING) {
            $this->outsourcing_quantity += $articleQuantityChangelog->change;
        } elseif ($articleQuantityChangelog->type == ArticleQuantityChangelog::TYPE_REPLACEMENT_DELIVERY) {
            $this->replacement_delivery_quantity += $articleQuantityChangelog->change;
        }
        $this->save();
    }

    /**
     * Scope a query to include only enabled articles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled($query): Builder
    {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_NO_ORDERS]);
    }

    /**
     * Scope a query to include only active articles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to order articles by name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderedByName($query): Builder
    {
        return $query->orderBy('name');
    }

     /**
     * Scope a query to order articles by article number.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderedByArticleNumber($query): Builder
    {
        return $query->orderBy('internal_article_number');
    }

    /**
     * Returns an array of status texts.
     *
     * @return array
     */
    public static function getStatusTextArray(): array
    {
        return [
            self::STATUS_ACTIVE => __('active'),
            self::STATUS_INACTIVE => __('inactive'),
            self::STATUS_NO_ORDERS => __('Order stop')
        ];
    }

    /**
     * Returns an array of inventory type texts.
     *
     * @return array
     */
    public static function getInventoryTextArray(): array
    {
        return [
            self::INVENTORY_TYPE_SPARE_PARTS => __('Spare parts'),
            self::INVENTORY_TYPE_CONSUMABLES => __('Consumables')
        ];
    }

     /**
     * Retrieves a short changelog for the article.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getShortChangelog()
    {
        return $this->quantityChangelogs()->with(['user', 'deliveryItem.delivery.order', 'unit', 'related'])->latest()->take(30)->get();
    }

     /**
     * Retrieves open order items for the article.
     *
     * @return mixed
     */
    public function openOrderItems()
    {
        return $this->belongsToMany(Order::class, 'order_items', 'article_id', 'order_id')->with('items.order.deliveries')->statusOpen();
    }

     /**
     * Retrieves open orders for the article.
     *
     * @return \Illuminate\Support\Collection
     */
    public function openOrders()
    {
        return $this->openOrderItems->filter(function ($order) {
            /* @var $order Order */
            return $order->items->where('article_id', $this->id)->filter(function ($item) {
                /* @var $item OrderItem */
                return ($item->quantity != $item->getQuantityDelivered());
            })->count() > 0;
        });
    }

     /**
     * {@inheritdoc}
     */
    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.unit_id')) {
            $data['old_values']['unit_id'] = optional(Unit::find($this->getOriginal('unit_id')))->name;
            $data['new_values']['unit_id'] = optional(Unit::find($this->getAttribute('unit_id')))->name;
        }

        if (Arr::has($data, 'new_values.category_id')) {
            $data['old_values']['category_id'] = optional(Category::find($this->getOriginal('category_id')))->name;
            $data['new_values']['category_id'] = Category::find($this->getAttribute('category_id'))->name;
        }

        if (Arr::has($data, 'new_values.status')) {
            $data['old_values']['status'] = (array_key_exists($this->getOriginal('status'), Article::getStatusTextArray())) ? Article::getStatusTextArray()[$this->getOriginal('status')] : null;
            $data['new_values']['status'] = Article::getStatusTextArray()[$this->getAttribute('status')];
        }

        if (Arr::has($data, 'new_values.inventory')) {
            unset($data['old_values']['inventory']);
            unset($data['new_values']['inventory']);
            $data['old_values']['inventory_text'] = !empty($this->getOriginal('inventory')) ? Article::getInventoryTextArray()[$this->getOriginal('inventory')] : null;
            $data['new_values']['inventory_text'] = !empty($this->getAttribute('inventory')) ? Article::getInventoryTextArray()[$this->getAttribute('inventory')] : null;
        }

        return $data;
    }

     /**
     * Defines the audit mappings.
     *
     * @return array
     */
    protected function getAuditMappings(): array
    {
        return [
            'status' => [
                0 => [0, '0', 'inactive', 'inactive'],
                1 => [1, '1', 'active', 'active'],
                2 => [2, '2', 'Order stop', 'Order stop']
            ]
        ];
    }

     /**
     * Gets the latest receipt for the article.
     *
     * @return ArticleQuantityChangelog|null
     */
    public function getLatestReceipt(): ?ArticleQuantityChangelog
    {
        return $this->quantityChangelogs()->where('type', ArticleQuantityChangelog::TYPE_INCOMING)->latest()->first();
    }

     /**
     * Scope a query to include average usage.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param int $months
     * @return void
     */
    public function scopeWithAverageUsage($query, int $months = 12): void
    {
        $query->addSubSelect(
            'average_usage_' . $months,
            ArticleQuantityChangelog::select(DB::raw('COALESCE(ROUND(ABS(SUM(`change`)) / ' . $months . '), 0)'))
                ->whereRaw('articles.id = article_quantity_changelogs.article_id')
                ->whereIn('type', [ArticleQuantityChangelog::TYPE_OUTGOING, ArticleQuantityChangelog::TYPE_CORRECTION])
                ->where('change', '<', 0)
                ->where('created_at', '>', Carbon::now()->subMonth($months))
        );
    }

     /**
     * Scope a query to include the last receipt date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeWithLastReceipt($query): void
    {
        $query->addSubSelect(
            'last_receipt',
            ArticleQuantityChangelog::select('created_at')
                ->whereRaw('articles.id = article_quantity_changelogs.article_id')
                ->where('type', ArticleQuantityChangelog::TYPE_INCOMING)
                ->latest()
        );
    }

     /**
     * Scope a query to include changelog sum within a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param Carbon $start
     * @param Carbon $end
     * @param mixed $type
     * @param string $fieldname
     * @return void
     */
    public function scopeWithChangelogSumInDateRange($query, Carbon $start, Carbon $end, $type, string $fieldname): void
    {
        $type = (!is_array($type)) ? [$type] : $type;
        $query->addSubSelect(
            $fieldname,
            ArticleQuantityChangelog::select(DB::raw('SUM(`change`)'))
                ->whereNotIn('type', [ArticleQuantityChangelog::TYPE_REPLACEMENT_DELIVERY, ArticleQuantityChangelog::TYPE_OUTSOURCING])
                ->whereRaw('articles.id = article_quantity_changelogs.article_id')
                ->whereBetween('created_at', [$start, $end->copy()->endOfDay()])
                ->whereIn('type', $type)
        );
    }

     /**
     * Retrieves all audits related to the article, including supplier article audits.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllAudits()
    {
        $previousArticleSupplierAudits = null;
        $articleSupplierAudits = $this->supplierArticles->map(function ($item) use (&$previousArticleSupplierAudits) {
            /* @var $item ArticleSupplier */
            $audits = $item->getAudits()->sortBy('timestamp');

            if ($previousArticleSupplierAudits && $audits->count() && collect($audits->first())->get('modified')->has('supplier_id')) {
                $audits->transform(function ($audit) use ($previousArticleSupplierAudits) {
                    $audit['modified']->transform(function ($value, $key) use ($previousArticleSupplierAudits) {
                        if (!array_key_exists('old', $value)) {
                            $value['old'] = $previousArticleSupplierAudits->getFormattedForAudit($key);
                        }

                        return $value;
                    });

                    return $audit;
                });
            }

            $previousArticleSupplierAudits = $item;
            return $audits;
        })->flatten(1);

        $audits = $this->getAudits();

        return collect($audits->toArray())->merge($articleSupplierAudits)->sortByDesc('timestamp');
    }

     /**
     * Retrieves the supplier article at a given date.
     *
     * @param Carbon|string $date
     * @param bool $useEndOfDay
     * @return ArticleSupplier|null
     */
    public function getSupplierArticleAtDate($date, bool $useEndOfDay = true): ?ArticleSupplier
    {
        $date = ($date instanceof Carbon) ? $date : Carbon::parse($date);

        if ($useEndOfDay) {
            $date = $date->endOfDay();
        }

        $supplierArticles = $this->supplierArticles->sortByDesc('created_at');

        // article didn't exists before requested date
        $ignoreArticleCreatedDate = (!empty(env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT')) && $this->id <= env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT'));
        if (!$ignoreArticleCreatedDate && $date->lt($this->created_at)) {
            return null;
        }

        // no changes to after requested date, use current value
        if ($supplierArticles->count() === 1) {
            return $supplierArticles->first();
        }

        // search for first change after requested date, use old value
        $firstSupplierArticleAfterDate = $supplierArticles->firstWhere('created_at', '<', $date);
        if ($firstSupplierArticleAfterDate) {
            return $firstSupplierArticleAfterDate;
        }

        // we have a imported article, return oldest supplierArticle Item
        if ($ignoreArticleCreatedDate && $this->created_at->lt($supplierArticles->first()->created_at)) {
            return $supplierArticles->last();
        }

        Log::error('No SupplierArticle found', compact('supplierArticles'));
        return null;
    }

    /**
     * Retrieves an attribute's value at a given date.
     *
     * @param string $attribute
     * @param Carbon|string $date
     * @return mixed|null
     */
    public function getAttributeAtDate($attribute, $date)
    {
        if ($attribute === 'quantity') {
            return $this->getQuantityAtDate($date);
        } else {
            return parent::getAttributeAtDate($attribute, $date);
        }
    }

     /**
     * Scope a query to include the article quantity at a specific date.  Uses a subquery.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param Carbon $date
     * @param string $fieldname
     * @return void
     */
    public function scopeWithQuantityAtDate($query, Carbon $date, string $fieldname): void
    {
        $query->addSubSelect(
            $fieldname,
            ArticleQuantityChangelog::select('new_quantity')
                ->whereRaw('articles.id = article_quantity_changelogs.article_id')
                ->whereIn('type', [
                    ArticleQuantityChangelog::TYPE_START, ArticleQuantityChangelog::TYPE_CORRECTION,
                    ArticleQuantityChangelog::TYPE_INCOMING, ArticleQuantityChangelog::TYPE_INVENTORY,
                    ArticleQuantityChangelog::TYPE_OUTGOING, ArticleQuantityChangelog::TYPE_SALE_TO_THIRD_PARTIES,
                    ArticleQuantityChangelog::TYPE_TRANSFER
                ])
                ->where('created_at', '<=', $date->copy()->endOfDay()->format('Y-m-d H:i:s'))
                ->latest()
        );
    }

     /**
     * Retrieves the article quantity at a specific date.
     *
     * @param Carbon $date
     * @return int|mixed
     */
    public function getQuantityAtDate(Carbon $date)
    {
        $date = $date->endOfDay();

        $fieldInSubquery = 'current_quantity';
        $article = Article::where('id', $this->id)->withQuantityAtDate($date, $fieldInSubquery)->first();


        if (!is_null($article->{$fieldInSubquery})) {
            return $article->{$fieldInSubquery};
        }

        if ($article->created_at->gt($date)) {
            return 0;
        }

        $oldestChangelogEntry = $article->quantityChangelogs()->oldest()->first();
        if ($oldestChangelogEntry) {
            return ($oldestChangelogEntry->new_quantity + (-1 * $oldestChangelogEntry->change));
        }

        return $article->quantity;
    }
}
