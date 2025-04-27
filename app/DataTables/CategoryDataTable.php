<?php

namespace Mss\DataTables;

use Mss\Models\Category;

class CategoryDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * Configures the DataTable for displaying category data.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->setRowId('id') // Set the row ID to the category ID.
            ->editColumn('name', function (Category $category) {
                return link_to_route('category.show', $category->name, ['category' => $category]); // Make the category name a link.
            })
            ->addColumn('action', 'category.list_action') // Add a custom action column.
            ->addColumn('checkbox', 'category.list_checkbox') // Add a checkbox column.
            ->rawColumns(['action', 'checkbox']); //  Declare the 'action' and 'checkbox' columns as raw HTML.
    }

    /**
     * Get query source of dataTable.
     *
     * Defines the data source for the DataTable.
     *
     * @param \Mss\Models\Category $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Category $model)
    {
        return $model->newQuery(); // Use the Category model as the data source.
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
                'order' => [[1, 'asc']], // Default ordering is by the second column (usually 'name').
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
            ['data' => 'checkbox', 'name' => 'checkbox', 'title' => '', 'width' => '10px', 'orderable' => false], // Checkbox column.
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')], // Name column.
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
        return 'Category_' . date('YmdHis'); // Filename for export.
    }
}
