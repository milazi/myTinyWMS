<?php

namespace Mss\DataTables;

use Spatie\Permission\Models\Role;

class RoleDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * Configures the DataTable for displaying role data.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->setRowId('id') // Set the row ID to the role ID.
            ->editColumn('name', function (Role $role) {
                return link_to_route('role.show', $role->name, compact('role')); // Make the role name a link.
            })
            ->addColumn('permissions', function (Role $role) {
                return $role->permissions->count(); // Add a column to display the number of permissions for each role.
            })
            ->addColumn('action', 'role.list_action') // Add a custom action column.
            ->rawColumns(['action']); // Declare the 'action' column as raw HTML.
    }

    /**
     * Get query source of dataTable.
     *
     * Defines the data source for the DataTable.
     *
     * @param \Spatie\Permission\Models\Role $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Role $model)
    {
        return $model->newQuery(); // Use the Role model as the data source.
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
                'buttons' => []  // Disable buttons.
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
            ['data' => 'permissions', 'name' => 'permissions', 'title' => __('Permissions')], // Permissions column.
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
        return 'Roles_' . date('YmdHis'); // Filename for export.
    }
}
