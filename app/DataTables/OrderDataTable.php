<?php

namespace Mss\DataTables;

use Carbon\Carbon;
use Mss\Models\Order;
use Mss\Models\OrderItem;

class OrderDataTable extends BaseDataTable
{
    protected $actionView = 'order.list_action'; // Blade view for the action column.
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
            ['data' => 'internal_order_number', 'name' => 'internal_order_number', 'title' => __('Order Number'), 'width' => '160px'],
            ['data' => 'supplier', 'name' => 'supplier', 'title' => __('Supplier'), 'visible' => false], // Supplier name, but hidden.
            ['data' => 'items', 'name' => 'items', 'title' => __('Articles')], // Articles column.
            ['data' => 'status', 'name' => 'status', 'title' => __('Order Status'), 'width' => '150px', 'class' => 'text-center'], // Order status.
            ['data' => 'confirmation_status', 'name' => 'confirmation_status', 'title' => __('Order Confirmation'), 'width' => '110px', 'class' => 'text-center', 'orderable' => false], // Order confirmation status.
            ['data' => 'invoice_status', 'name' => 'invoice_status', 'title' => __('Invoice'), 'width' => '110px', 'class' => 'text-center', 'orderable' => false], // Invoice status.
            ['data' => 'order_date', 'name' => 'order_date', 'title' => __('Order Date'), 'class' => 'text-right', 'searchable' => false, 'width' => '90px'], // Order date.
            ['data' => 'expected_delivery', 'name' => 'expected_delivery', 'title' => __('Delivery Date'), 'class' => 'text-right', 'searchable' => false, 'width' => '90px'], // Expected delivery date.
        ];
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->setRowId('id') // Set the row ID to the order ID.
            ->addColumn('supplier', function (Order $order) {
                return $order->supplier ? $order->supplier->name : '<span class="italic text-gray-600">empty</span>'; // Display supplier name or "empty" if not available.
            })
            ->orderColumn('supplier', 'supplier_name $1') // Allow ordering by supplier name.
            ->editColumn('order_date', function (Order $order) {
                if (empty($order->order_date)) {
                    return ''; // Return empty string if order date is empty.
                }

                if ($order->order_date->diffInDays(Carbon::now()) < 1) {
                    return 'today'; // Display "today" if the order date is today.
                }

                return $order->order_date->format('d.m.Y'); // Format the order date.
            })
            ->editColumn('expected_delivery', function (Order $order) {
                /* @var $expectedDelivery Carbon */
                $expectedDelivery = $order->items->max('expected_delivery'); // Get the latest expected delivery date from order items.
                $output = $expectedDelivery ? $expectedDelivery->format('d.m.Y') : ''; // Format the date

                // Check for overdue items.
                $overdueItems = $order->items->filter(function ($orderItem) {
                    /** @var OrderItem $orderItem */
                    return ($orderItem->expected_delivery < now() && $orderItem->getQuantityDelivered() < $orderItem->quantity); // Check if the delivery is overdue
                });

                // Append a label if overdue.
                if ($overdueItems->count()) {
                    $output .= '<br><span class="text-red-400 text-sm font-bold">' . __('overdue') . '</span>';
                }

                return $output;
            })
            ->editColumn('internal_order_number', function (Order $order) {
                return view('order.list_order_number', compact('order'))->render(); // Render a view to display the order number.
            })
            ->addColumn('article', function (Order $order) {
                return $order->items->count(); // Display the number of articles in the order.
            })
            ->addColumn('invoice_status', 'order.list_invoice_received') // Add a column for invoice status, rendered by a view.
            ->filterColumn('invoice_status', function ($query, $keyword) {
                // Filter by invoice status based on the provided keyword.
                switch ($keyword) {
                    case 'empty':
                        break;

                    case 'none':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND invoice_received = 1) = 0');
                        break;

                    case 'all':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND invoice_received = 1) = (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id)');
                        break;

                    case 'partial':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND invoice_received = 1) > 0 AND (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND invoice_received = 1) < (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id)');
                        break;

                    case 'check':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND invoice_received = 2) > 0');
                        break;
                }
            })
            ->addColumn('confirmation_status', 'order.list_confirmation_received') // Add a column for order confirmation status, rendered by a view.
            ->filterColumn('confirmation_status', function ($query, $keyword) {
                // Filter by order confirmation status based on the provided keyword.
                switch ($keyword) {
                    case 'empty':
                        break;

                    case 'none':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND confirmation_received = 1) = 0');
                        break;

                    case 'all':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND confirmation_received = 1) = (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id)');
                        break;

                    case 'partial':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND confirmation_received = 1) > 0 AND (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND confirmation_received = 1) < (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id)');
                        break;
                }
            })
            ->filterColumn('status', function ($query, $keyword) {
                // Filter by order status.
                if ($keyword === 'open') {
                    $query->statusOpen(); // Use a custom scope (assumed to be defined in the Order model).
                } elseif (is_numeric($keyword)) {
                    $query->where('status', $keyword); // Filter by numeric status code.
                }
            })
            ->filterColumn('supplier', function ($query, $keyword) {
                $query->where('supplier_id', $keyword); // Filter by supplier ID.
            })
            ->filter(function ($query) {
                // Apply a default status filter if no status search is provided.
                if (!isset(request('columns')[3]['search']) && !empty($this->defaultStatusFilter)) {
                    $query->whereIn('status', $this->defaultStatusFilter);
                }
            }, true)
            ->addColumn('items', function ($order) {
                return view('order.list_items', compact('order'))->render(); // Render a view to display the order items.
            })
            ->rawColumns(['action', 'supplier', 'status', 'order_date', 'expected_delivery', 'internal_order_number', 'invoice_status', 'confirmation_status', 'items']); // Declare columns as raw HTML.
    }

    /**
     * Get query source of dataTable.
     *
     * Defines the data source for the DataTable.
     *
     * @param \Mss\Models\Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model)
    {
        return $model->newQuery()->withSupplierName() // Eager load supplier name.
            ->with(['items.order.deliveries.items', 'items.article' => function ($query) {
                $query->withCurrentSupplierArticle(); // Eager load current supplier article for order items.
            }, 'supplier', 'messages']); // Eager load relationships
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
                'paging' => $this->paging, // Use the paging setting (true or false).
                'order' => [[1, 'asc']], // Default ordering is by the second column (usually 'supplier').
                'rowGroup' => ['dataSrc' => 'supplier'], // Group rows by supplier.
                'buttons' => [] // Disable buttons.
            ])
            ->addAction(['title' => __('Action'), 'width' => '100px', 'class' => 'action-col']); // Add an action column.
    }
}
