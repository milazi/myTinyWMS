<?php

namespace Mss\DataTables;

use Mss\Models\User;

class UserDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * Configures the DataTable for displaying user data.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->setRowId('id') // Set the row ID to the user ID.
            ->editColumn('name', function (User $user) {
                return link_to_route('user.show', $user->name, compact('user')); // Make the user name a link to the show page.
            })
            ->addColumn('source', function (User $user) {
                return ($user->getSource() == User::SOURCE_LDAP) ? 'LDAP' : __('Local'); // Display user source (LDAP or Local).
            })
            ->addColumn('roles', function (User $user) {
                return $user->getRoleNames()->implode(', '); // Display user roles as a comma-separated string.
            })
            ->addColumn('action', 'user.list_action') // Add a custom action column.
            ->rawColumns(['action']); // Declare the 'action' column as raw HTML.
    }

    /**
     * Get query source of dataTable.
     *
     * Defines the data source for the DataTable.
     *
     * @param \Mss\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        return $model->newQuery(); // Use the User model as the data source.
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
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')], // Name column.
            ['data' => 'email', 'name' => 'email', 'title' => __('E-Mail')], // Email column.
            ['data' => 'username', 'name' => 'username', 'title' => __('Username')], // Username column.
            ['data' => 'source', 'name' => 'source', 'title' => __('Source')], // Source column (e.g., LDAP, Local).
            ['data' => 'roles', 'name' => 'roles', 'title' => __('Roles')], // Roles column.
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
        return 'Users_' . date('YmdHis'); // Filename for export.
    }
}
