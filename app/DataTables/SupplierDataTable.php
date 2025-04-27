<?php

namespace Mss\DataTables;

use Mss\Models\Supplier;

class SupplierDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * Configures the DataTable for displaying supplier data.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->setRowId('id') // Set the row ID to the supplier ID.
            ->editColumn('name', function (Supplier $supplier) {
                return link_to_route('supplier.show', $supplier->name, ['supplier' => $supplier]); // Make the supplier name a link.
            })
            ->addColumn('action', 'supplier.list_action') // Add a custom action column.
            ->rawColumns(['action']); // Declare the 'action' column as raw HTML.
    }

    /**
     * Get query source of dataTable.
     *
     * Defines the data source for the DataTable.
     *
     * @param \Mss\Models\Supplier $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Supplier $model)
    {
        return $model->newQuery(); // Use the Supplier model as the data source.
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
            ->parameters([  // Set DataTables parameters.
                'paging' => false, // Disable pagination.
                'order' => [[0, 'asc']], // Default ordering is by the first column (usually 'name').
                'buttons' => [] // Disable buttons.
            ])
            ->addAction(['title' => __('Action'), 'width' => '150px']); // Add an action column with a specific title and width.
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
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')], // Name column.
            ['data' => 'accounts_payable_number', 'name' => 'accounts_payable_number', 'title' => __('Accounts Payable Number')], // Accounts payable number column.
            ['data' => 'email', 'name' => 'email', 'title' => __('E-Mail')], // Email column.
            ['data' => 'phone', 'name' => 'phone', 'title' => __('Phone')], // Phone column.
            ['data' => 'contact_person', 'name' => 'contact_person', 'title' => __('Contact Person')], // Contact person column.
            ['data' => 'website', 'name' => 'website', 'title' => __('Website')], // Website column.
            ['data' => 'notes', 'name' => 'notes', 'title' => __('Notes')], // Notes column.
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
        return 'Supplier_' . date('YmdHis'); // Filename for export.
    }
}
