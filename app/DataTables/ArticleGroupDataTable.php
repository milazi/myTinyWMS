<?php

namespace Mss\DataTables;

use Mss\Models\ArticleGroup;

class ArticleGroupDataTable extends BaseDataTable
{
    /**
     * @var string
     */
    protected $actionView = 'article_group.list_action'; //blade view for the action column

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->setRowId('id')  // Set the row ID for each record.
            ->addColumn('article_number', function (ArticleGroup $articleGroup) {
                return $articleGroup->getArticleNumber(); // Add a column for the formatted article number.
            })
            ->addColumn('article', function (ArticleGroup $articleGroup) {
                return $articleGroup->items->count(); // Add a column to display the number of articles in the group.
            })
            ->addColumn('items', function (ArticleGroup $articleGroup) {
                return view('article_group.list_items', compact('articleGroup'))->render(); // Render a view to display the list of items in the group.
            })
            ->editColumn('name', function (ArticleGroup $articleGroup) {
                return link_to_route('article-group.show', $articleGroup->name, ['article_group' => $articleGroup], ['target' => '_blank']); // Make the group name a link to the show page.
            })
            ->addColumn('action', $this->actionView) // Add the action column, using the specified view.
            ->rawColumns(['action', 'items']); // Declare the 'action' and 'items' columns as raw HTML.
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Mss\Models\ArticleGroup $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ArticleGroup $model)
    {
        return $model->newQuery()
            ->with(['items.article' => function ($query) {
                $query->withCurrentSupplierArticle(); // Eager load the current supplier article for each article in the group.
            }]);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->minifiedAjax() // Use minified AJAX to reduce payload size.
            ->columns($this->getColumns()) // Define the columns to be displayed.
            ->parameters([ // Set DataTables parameters.
                'paging' => true,
                'order' => [[1, 'asc']], // Default ordering is by the second column (usually 'name').
                'buttons' => []  // Disable buttons
            ])
            ->addAction(['title' => __('Action'), 'width' => '100px', 'class' => 'action-col']); // Add an action column with a specific title, width and class.
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')],
            ['data' => 'article_number', 'name' => 'article_number', 'title' => __('Internal Article Number')],
            ['data' => 'external_article_number', 'name' => 'external_article_number', 'title' => __('External Article Number')],
            ['data' => 'items', 'name' => 'items', 'title' => __('Articles')], // Column to display the articles
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'ArticleGroups_' . date('YmdHis'); // Define the filename for CSV export.
    }
}
