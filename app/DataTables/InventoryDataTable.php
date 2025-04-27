<?php

namespace Mss\DataTables;

use Mss\Models\Inventory;
use Mss\Services\InventoryService;

class InventoryDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * Configures the DataTable for displaying inventory data.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->setRowId('id') // Set the row ID to the inventory ID.
            ->addColumn('started', function (Inventory $inventory) {
                return $inventory->created_at->format('d.m.Y H:i'); // Format the start date and time.
            })
            ->addColumn('open_categories', function (Inventory $inventory) {
                return InventoryService::getOpenCategories($inventory)->count(); // Get the count of open categories.
            })
            ->addColumn('open_articles', function (Inventory $inventory) {
                $inventory->load(['items' => function ($query) {
                    $query->unprocessed()->with('article.category'); // Load unprocessed items with article and category.
                }]);

                return $inventory->items->count(); // Get the count of open articles.
            })
            ->addColumn('action', 'inventory.list_action') // Add a custom action column.
            ->rawColumns(['action']); // Declare the 'action' column as raw HTML.
    }

    /**
     * Get query source of dataTable.
     *
     * Defines the data source for the DataTable.
     *
     * @param \Mss\Models\Inventory $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Inventory $model)
    {
        return $model->newQuery()->unfinished(); // Use the Inventory model and filter for unfinished inventories.
    }

    /**
     * Optional method if you want to use html builder.
     *
     * Builds the HTML structure of the DataTable.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->minifiedAjax() // Use minified AJAX to reduce payload size.
            ->columns($this->getColumns()) // Define the columns to be displayed.
            ->parameters([ // Set DataTables parameters.
                'paging' => false, // Disable pagination.
                'order' => [[0, 'asc']], // Default ordering is by the first column (usually 'started').
                'buttons' => [] // Disable buttons.
            ])
            ->addAction(['title' => __('Action'), 'width' => '170px']); // Add an action column with a specific title and width.
    }

    /**
     * Get columns.
     *
     * Defines the columns to be displayed in the DataTable.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            ['data' => 'started', 'name' => 'started', 'title' => __('Started')], // Column for inventory start date/time.
            ['data' => 'open_categories', 'name' => 'open_categories', 'title' => __('Open Categories')], // Column for open categories count.
            ['data' => 'open_articles', 'name' => 'open_articles', 'title' => __('Open Articles')],  // Column for open articles count.
        ];
    }

    /**
     * Get filename for export.
     *
     * Defines the filename for exported data.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Inventory_' . date('YmdHis'); // Define the filename for CSV export. 
    }
}
