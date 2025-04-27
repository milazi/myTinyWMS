<?php

namespace Mss\DataTables;

use App;
use Yajra\DataTables\Services\DataTable;

abstract class BaseDataTable extends DataTable
{
    protected $pageLength = 50; // Default number of records per page.

    // Custom DOM structure for the DataTable.
    protected $dom = '<"table-toolbar"<"flex mb-4"<"#table-filter"><"table-search"f>B><"table-toolbar-middle"r><"table-toolbar-right">><"table-wrapper"<"fix-head-bg"><"table-content"t><"table-footer"<"table-footer-actions">ip>>';

    /**
     * Build the DataTable's query.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function builder()
    {
        $builder = parent::builder();

        // Set language file based on the application locale.
        switch (App::getLocale()) {
            case 'de':
                $langFile = asset('js/datatables/German.1.10.13.json');
                break;

            case 'en':
            default:
                $langFile = asset('js/datatables/English.1.10.13.json');
                break;
        }

        // Set DataTable parameters.
        $builder->parameters([
            'dom' => $this->dom, // Use the custom DOM structure.
            'order' => [[0, 'asc']], // Default ordering: ascending by the first column.
            'language' => ['url' => $langFile, 'searchPlaceholder' => __('Search')], // Set language file and search placeholder.
            'pageLength' => $this->pageLength, // Set the number of records per page.
            //'stateSave' => true, // Enable state saving (optional, commented out).
            'bAutoWidth' => false, // Disable auto-width calculation.
            'lengthMenu' => $this->getLengthMenu() // Set the available page length options.
        ]);
        $builder->setTableAttribute('class', 'table'); // Set the table's HTML class attribute.

        return $builder;
    }

    /**
     * Get the page length menu options.
     *
     * @return array
     */
    protected function getLengthMenu(): array
    {
        $values = [50, 100, -1]; // Available page lengths.
        $captions = [50, 100, __('All')]; // Corresponding display captions.

        // If the current page length is not in the default options, add it.
        if (!in_array($this->pageLength, $values)) {
            $values = array_prepend($values, $this->pageLength);
            $captions = array_prepend($captions, $this->pageLength);
        }

        return [$values, $captions]; // Return the combined array of values and captions.
    }
}
