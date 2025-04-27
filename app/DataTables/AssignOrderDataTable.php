<?php

namespace Mss\DataTables;

use Mss\Models\Order;

class AssignOrderDataTable extends OrderDataTable
{
    protected $actionView = 'order_messages.order_list_action'; //  Blade view for the action column.
    protected $pageLength = 30; // Number of records per page.
    protected $paging = true; // Enable or disable pagination.

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
            ['data' => 'internal_order_number', 'name' => 'internal_order_number', 'title' => __('Order Number')],
            ['data' => 'supplier', 'name' => 'supplier', 'title' => __('Supplier'), 'visible' => false], // Supplier name, but hidden.
            ['data' => 'items', 'name' => 'items', 'title' => __('Articles'), 'searchable' => false], //  Articles column, not searchable.
            ['data' => 'status', 'name' => 'status', 'title' => __('Status')] // Order status.
        ];
    }
}
