<?php

namespace Mss\DataTables;

use Carbon\Carbon;
use Mss\Models\Article;

class ArticleDataTable extends BaseDataTable
{
    const STATUS_COL_ID = 17;
    const CATEGORY_COL_ID = 16;
    const TAGS_COL_ID = 15;
    const SUPPLIER_COL_ID = 13;

    /**
     * @var array
     */
    protected $rawColumns = ['action', 'price', 'checkbox', 'order_number', 'supplier_name', 'average_usage']; // Columns that should not be escaped

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->setRowId('article_{{$id}}') //Sets the row id for each article
            ->editColumn('internal_article_number', function (Article $article) {
                return (empty($article->internal_article_number)) ? '(' . $article->id . ')' : $article->internal_article_number; // Display internal article number or ID if empty
            })
            ->editColumn('quantity', function (Article $article) {
                return $article->quantity; //Returns article quantity
            })
            ->editColumn('min_quantity', function (Article $article) {
                return $article->min_quantity; //Returns minimum article quantity
            })
            ->editColumn('name', function (Article $article) {
                return link_to_route('article.show', $article->name, ['article' => $article], ['target' => '_blank']); // Make article name a link to show page
            })
            ->addColumn('price', function (Article $article) {
                return formatPrice(optional($article->currentSupplierArticle)->price / 100); // Display formatted price from current supplier article
            })
            ->addColumn('order_number', function (Article $article) {
                $orderNumber = optional($article->currentSupplierArticle)->order_number; //gets the order number of the article

                if ($article->openOrders()->count()) {
                    $orderNumber .= '<i class="fa fa-shopping-cart float-right" title="' . __('Open Order') . '"></i>'; // Add shopping cart icon if there are open orders
                }

                return $orderNumber;
            })
            ->addColumn('average_usage', function (Article $article) {
                return intval($article->average_usage); // Returns average usage of the article
            })
            ->addColumn('last_receipt', function (Article $article) {
                $latestReceipt = $article->last_receipt;  //gets the last receipt

                return ($latestReceipt) ? Carbon::parse($latestReceipt)->format('d.m.Y') : ''; //formats the date
            })
            ->addColumn('delivery_time', function (Article $article) {
                return optional($article->currentSupplierArticle)->delivery_time; //gets the delivery time from current supplier
            })
            ->addColumn('order_quantity', function (Article $article) {
                return optional($article->currentSupplierArticle)->order_quantity; //gets the order quantity from current supplier
            })
            ->editColumn('category', function (Article $article) {
                return optional($article->category)->name; //gets category name
            })
            ->editColumn('supplier_name', function (Article $article) {
                return '<div class="flex">
                            <div>' . $article->supplier_name . '</div>
                            <div class="flex-1 text-right pr-4">
                                <a href="' . route('article.index', ['supplier' => $article->current_supplier_id]) . '"><i class="fa fa-filter"></i></a>  // Link to filter articles by supplier
                            </div>
                        </div>';
            })
            ->addColumn('unit', function (Article $article) {
                return optional($article->unit)->name; //gets the unit name
            })
            ->addColumn('tags', function (Article $article) {
                return $article->tags->pluck('name')->implode(', '); //gets the name of the tags
            })
            ->addColumn('average_usage', function (Article $article) {
                if ($article->average_usage_12 == 0) return $article->average_usage_12;

                $diff = $article->average_usage_12 - $article->average_usage_3;
                $diffPercent = (100 * $diff) / $article->average_usage_12;

                if ($diffPercent < -30) {
                    return $article->average_usage_12 . ' <i class="fa fa-angle-double-up text-danger bold" style="font-size: 18px; margin-left: 5px" title="' . __('Consumption in the last 3 months at least 30% higher than in the last 12') . '"></i>';
                } elseif ($diffPercent < -15) {
                    return $article->average_usage_12 . ' <i class="fa fa-angle-up text-danger bold" style="font-size: 18px; margin-left: 5px" title="' . __('Consumption in the last 3 months at least 15% higher than in the last 12') . '"></i>';
                } elseif ($diffPercent > 30) {
                    return $article->average_usage_12 . ' <i class="fa fa-angle-double-down text-success bold" style="font-size: 18px; margin-left: 5px" title="' . __('Consumption in the last 3 months at least 30% lower than in the last 12') . '"></i>';
                } elseif ($diffPercent > 15) {
                    return $article->average_usage_12 . ' <i class="fa fa-angle-down text-success bold" style="font-size: 18px; margin-left: 5px" title="' . __('Consumption in the last 3 months at least 30% lower than in the last 12') . '"></i>';
                }

                return $article->average_usage_12;
            })
            ->filterColumn('id', function ($query, $keyword) {
                $query->whereIn('id', explode(',', $keyword));
            })
            ->filterColumn('category', function ($query, $keyword) {
                $query->where('category_id', $keyword);
            })
            ->filterColumn('delivery_time', function ($query, $keyword) {
                $query->whereHas('suppliers', function ($query) use ($keyword) {
                    $query->where('delivery_time', $keyword);
                });
            })
            ->filterColumn('supplier_name', function ($query, $keyword) {
                /*
                 * @todo optimize me!
                 */
                $query->whereRaw('(SELECT supplier_id FROM article_supplier WHERE article_supplier.article_id = articles.id order by created_at desc limit 1) = ?', $keyword);
            })
            ->filterColumn('tags', function ($query, $keyword) {
                $query->whereHas('tags', function ($query) use ($keyword) {
                    $query->where('tags.id', $keyword);
                });
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword == 'all') {
                    $query->whereIn('status', [Article::STATUS_ACTIVE, Article::STATUS_INACTIVE, Article::STATUS_NO_ORDERS]);
                } else {
                    $query->where('status', $keyword);
                }
            })
            ->filter(function ($query) {
                if (!isset(request('columns')[15]['search']) && !isset(request('columns')[17]['search'])) {
                    $query->where('status', Article::STATUS_ACTIVE);
                }
            }, true)
            ->orderColumn('supplier', 'supplier_name $1')
            ->orderColumn('last_receipt', 'last_receipt $1')
            ->addColumn('action', function ($article) {
                return '<a href="' . route('article.show', $article) . '" class="table-action" target="_blank">' . __('Details') . '</a>';
            })
            ->addColumn('checkbox', function ($article) {
                return '<div class="i-checks"><label><input type="checkbox" value="' . $article->id . '" name="article[]" /></label></div>';
            })
            ->rawColumns($this->rawColumns);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Mss\Models\Article $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Article $model)
    {
        return $model->newQuery()
            ->withCurrentSupplierArticle()->withCurrentSupplier()->withCurrentSupplierName()->withAverageUsage(12)->withAverageUsage(3)->withLastReceipt()
            ->with(['category', 'suppliers', 'unit', 'tags', 'openOrderItems']);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->minifiedAjax()
            ->columns($this->getColumns())
            ->parameters($this->getHtmlParameters())
            ->addAction(['title' => __('Action'), 'width' => '80px', 'class' => 'text-right']);
    }

    protected function getHtmlParameters(): array
    {
        $parameters = [
            'order' => [[2, 'asc']], // Default ordering
            'buttons' => [
                ['extend' => 'csv', 'className' => 'btn-secondary', 'text' => '<i class="fa fa-download"></i>', 'titleAttr' => __('Export CSV')] // Export button
            ],
        ];

        return $parameters;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            ['data' => 'checkbox', 'name' => 'checkbox', 'title' => '<div class="i-checks"><label><input type="checkbox" value="" id="select_all" /></label></div>', 'width' => '10px', 'orderable' => false, 'class' => 'text-center', 'searchable' => false],
            ['data' => 'sort_id', 'name' => 'sort_id', 'title' => 'Sort.', 'width' => '40px', 'visible' => false, 'searchable' => false],
            ['data' => 'internal_article_number', 'name' => 'internal_article_number', 'title' => '#'],
            ['data' => 'name', 'name' => 'name', 'title' => __('Article Name')],
            ['data' => 'order_number', 'name' => 'order_number', 'title' => __('Order Number')],
            ['data' => 'quantity', 'name' => 'quantity', 'title' => __('Stock'), 'class' => 'text-center', 'width' => '40px', 'searchable' => false],
            ['data' => 'min_quantity', 'name' => 'min_quantity', 'title' => __('Min. Stock'), 'class' => 'text-center', 'width' => '55px', 'searchable' => false],
            ['data' => 'order_quantity', 'name' => 'order_quantity', 'title' => __('Order Quantity'), 'class' => 'text-center', 'width' => '55px', 'searchable' => false],
            ['data' => 'average_usage', 'name' => 'average_usage', 'title' => __('Avg. Cons.'), 'class' => 'text-center', 'width' => '60px', 'searchable' => false],
            ['data' => 'unit', 'name' => 'unit', 'title' => __('Unit'), 'searchable' => false],
            ['data' => 'price', 'name' => 'price', 'title' => __('Price'), 'class' => 'text-right whitespace-no-wrap', 'searchable' => false],
            ['data' => 'notes', 'name' => 'notes', 'title' => __('Notes'), 'visible' => false],
            ['data' => 'delivery_time', 'name' => 'delivery_time', 'title' => __('Delivery Time'), 'class' => 'text-center', 'searchable' => false],
            ['data' => 'supplier_name', 'name' => 'supplier_name', 'title' => __('Supplier')],
            ['data' => 'last_receipt', 'name' => 'last_receipt', 'title' => __('Last Receipt'), 'width' => '70px', 'class' => 'whitespace-no-wrap', 'searchable' => false],
            ['data' => 'tags', 'name' => 'tags', 'title' => __('Tags'), 'visible' => false],
            ['data' => 'category', 'name' => 'category', 'title' => __('Category'), 'visible' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'visible' => false],
            ['data' => 'id', 'name' => 'id', 'title' => __('ID'), 'visible' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Article_' . date('YmdHis');
    }
}
