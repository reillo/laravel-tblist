<?php namespace Nerweb\Tblist;

use \Illuminate\Support\Facades\Request;
use \Illuminate\Support\Str;

/**
 * Laravel Tblist
 * http://github.com/nerweb93/laravel-tblist
 *
 * Class Tblist
 * @package Tblist
 */
abstract class Tblist {

    const COLUMN_OPTION_LABEL         = 'label';
    const COLUMN_OPTION_SORTABLE      = 'sortable';
    const COLUMN_OPTION_TABLE_COLUMN  = 'table_column';
    const COLUMN_OPTION_CLASSES       = 'classes';
    const COLUMN_OPTION_THEAD_ATTR    = 'thead_attr';

    /**
     * The table name
     *
     * @var string $table (required)
     */
    public $table;

    /**
     * The target table id.
     *
     * @var string $tableId (required)
     */
    public $tableId = "id";

    /**
     * Query instance
     *
     * @var Illuminate\Support\Facades\DB $query
     */
    public $query;

    /**
     * The assoc array with the key of returned column of the specified query
     *
     * @var array
     */
    public $columns = array();

    /**
     * Add Columns to select
     *
     * @var array $columnsToSelect
     */
    public $columnsToSelect = array('*');

    /**
     * The display message when there is no results
     *
     * @var string $noResults
     */
    public $noResults = 'No results found.';

    /**
     * the column name that will be ordered by default.
     *
     * @var array $columnOrders
     */
    public $columnOrders = array();

    /**
     * If greater than 0 then add an advance shortcut to pagination
     * this will identify the range that the pagination
     * will jump into.
     *
     * @var int $goto_shortcut_page
     */
    public $pageJump = 0;

    /**
     * Default per page
     *
     * @var int|string $perPage (all|n)
     */
    public $perPage = 25;

    /**
     * The default page, on what lists should be display
     * will be overridden via child class and request parameter
     *
     * @var int $page
     */
    public $page = 1;

    protected $totalCount;
    protected $nextPage;
    protected $prevPage;
    protected $lastPage;
    protected $firstPage;
    protected $jumpNext;
    protected $jumpPrev;
    protected $offset;
    protected $limit;

    /**
     * The items of results row
     *
     * @var int $page
     */
    protected $resultRows = array();

    /**
     * The base url
     *
     * @var bool|string
     */
    private $baseURL = false;

    /**
     * Custom/additional parameters
     *
     * @var array
     */
    private $parameters = array();

    /**
     * The default request query [$_REQUEST]
     *
     * @var array
     */
    private $requestParameters;

    /**
     * merge request_parameters and url_parameters
     * note! tblist overrides some of the parameters name
     *
     * @var array
     */
    private $finalParameters;

    /**
     * The fragment to be appended to all URLs.
     *
     * @var string
     */
    protected $fragment;

    /**
     * Intended for caching the calculation of head,
     *
     * @var bool hf = head and footer content
     */
    private $HFContent = false;

    /**
     * This will call all the method by sequence depends on the requirements
     * of each other
     *
     * @return void
     */
    public function prepareList()
    {
        $this->setRequestParameters();

        $this->setPerPage();

        $this->setCurrentPage();

        $this->setTotalCount();

        $this->setPagination();

        $this->setQueryOrder();

        $this->setQueryOffset();

        $this->setResultRows();

        $this->setFinalParameters();
    }

    private function setRequestParameters()
    {
        $this->requestParameters = Request::all();
    }

    /**
     * This will just set the per_page property
     * override default per page when there are per_page
     * in the request parameter
     *
     * @return void
     */
    private function setPerPage()
    {
        if ($this->hasInput('per_page'))
        {
            $per_page_input = $this->getInput('per_page');

            if ($this->isValidPageNumber($per_page_input)) {
                $this->perPage = $per_page_input;
            }
            else if ($per_page_input == 'all')
            {
                $this->perPage = 'all';
            }
        }
    }

    /**
     * This will just set the page property
     *
     * @return void
     */
    private function setCurrentPage()
    {
        if ($this->perPage == 'all') return;

        if ($this->hasInput('page'))
        {
            $page_input = $this->getInput('page');

            if ($this->isValidPageNumber($page_input))
            {
                $this->page = $page_input;
            }
        }
    }

    /**
     * Count the result
     *
     * @return void
     */
    private function setTotalCount()
    {
        $this->totalCount = $this->query->count();
    }

    /**
     * Will just calculate all the calculation needed to generate a pagination page
     *
     * @return void
     */
    private function setPagination()
    {
        // ALl Pages
        if ($this->perPage == 'all') return;

        $this->firstPage    = 1;
        $this->lastPage     = ceil($this->totalCount / $this->perPage);

        // if current page is greater than the total page
        // then set the current page equal to total page
        $this->page = min($this->page, $this->lastPage);

        // Calculate the offset of where the retrieval index start at
        $this->offset   = ($this->page - 1) * $this->perPage;
        $this->limit    = $this->perPage;

        // Next Page And Previous Page
        $this->nextPage = min($this->page + 1, $this->lastPage);
        $this->prevPage = max($this->page - 1, $this->firstPage);
    }

    /**
     * This will set the order by string and add to query property
     * (Multiple order)
     *
     * @return void
     */
    private function setQueryOrder()
    {
        if ($this->hasInput('column_orders'))
        {
            // before we process the order, we need to check if
            // single order is set.
            // and remove other column_orders keys and method except for the single order
            // specified.
            if ($this->hasInput('single_order') && isset($this->columns[$this->getInput('single_order')]))
            {
                // single_order might empty
                $single_order = $this->getInput('single_order');

                if ( ! empty($single_order))
                {
                    foreach ($this->requestParameters['column_orders'] as $key => $value)
                    {
                        if ($key != $single_order)
                        {
                            unset($this->requestParameters['column_orders'][$key]);
                        }
                    }
                }
            }

            // overide default column_orders keys and methods
            $column_orders_input = $this->getInput('column_orders');
            if (!empty($column_orders_input) && is_array($column_orders_input))
            {
                // $input_orders method
                foreach ($column_orders_input as $order_by => $order_method)
                {
                    if (!array_key_exists($order_by, $this->columns))
                    {
                        unset($column_orders_input[$order_by]);
                    }
                    else
                    {
                        if (!is_string($order_method) || !in_array($order_method, array('asc', 'desc')))
                        {
                            $column_orders_input[$order_by] = 'asc';
                        }
                    }
                }

                // override default order
                $this->columnOrders = $column_orders_input;
            }
        }

        // load default column_orders
        // no need to perform validation since
        // tblist assumes that it is a system generated data
        // not user generated data.
        foreach ($this->columnOrders as $column_key => $order_method)
        {
            // just set the default order query string
            // just like: order by column_name desc
            $table_column = $this->getColumnOption($column_key, self::COLUMN_OPTION_TABLE_COLUMN, $column_key);
            $this->query->orderBy($table_column, $order_method);
        }
    }

    /**
     * This will just set the offset and limit of the query
     *
     * @return void
     */
    private function setQueryOffset()
    {
        if ($this->perPage == 'all') return;

        $this->query->skip($this->offset)->take($this->perPage);
    }

    /**
     * This will get the data rows from the database
     * with the all where queries
     *
     * @return void
     */
    private function setResultRows()
    {
        $this->query->select($this->columnsToSelect);
        $this->resultRows = $this->query->get();
    }

    /**
     * Set Final Parameters
     *
     * @return void
     */
    private function setFinalParameters()
    {
        // Sometimes we may use tblist  for the same page for different url
        // (i.e ajax) so on initiation we need to check the url if set
        // manually. and URL must be the same with the actual url.
        if (!$this->baseURL || Request::url() == $this->baseURL)
        {
            $this->finalParameters = array_merge($this->requestParameters, $this->parameters);
        }
        else
        {
            $this->finalParameters = $this->parameters;
        }
    }

    /**
     * This will just get all the total count
     *
     * @return int the total number of row result
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }


    // TABLE HEADER STRUCTURE

    /**
     * This will just return the header of the table list
     *
     * @return string
     */
    public function getTableHeader()
    {
        ob_start();
        ?>
        <thead>
        <?php echo $this->getTableHf(); ?>
        </thead>
        <?php

        return ob_get_clean();
    }

    /**
     * This will just return the header of the table list
     *
     * @return string
     */
    public function getTableFooter()
    {
        ob_start();
        ?>
        <tfoot>
        <?php echo $this->getTableHf(); ?>
        </tfoot>
        <?php

        return ob_get_clean();
    }

    /**
     * get the data for both header and footer.
     *
     * @return string
     */
    private function getTableHf()
    {
        if ($this->HFContent) return $this->HFContent;

        ob_start();
        ?>
        <tr>
            <?php // Start constructing the table header columns ?>
            <?php foreach ($this->columns as $column_key => $column_options) : ?>

                <?php $sortable_class = $this->getSortableClass($column_key); ?>

                <th data-column-name="<?php echo $column_key; ?>"
                    class="<?php echo $sortable_class; ?> <?php echo $this->getColumnOption($column_key, self::COLUMN_OPTION_CLASSES); ?>"
                    <?php echo $this->getColumnOption($column_key, self::COLUMN_OPTION_THEAD_ATTR) ?>>

                    <?php echo $this->getColumnHeaderStructure($column_key, $column_options); ?>
                </th>
            <?php endforeach; ?>
        </tr>

        <?php

        $this->HFContent = ob_get_clean();

        return $this->HFContent;
    }

    /**
     * Create the column data structure
     *
     * @param string    $column_key - the column name
     * @return string
     */
    protected function getColumnHeaderStructure($column_key)
    {
        $this->getSortableLinkStart($column_key);

        // Column header content
        $key_col_method = Str::camel('col_set_header_' . $column_key);
        if (method_exists($this, $key_col_method))
        {
            ob_start();
            $this->{$key_col_method}($column_key);
            echo ob_get_clean();
        }
        else
        {
            echo $this->getColumnOption($column_key, self::COLUMN_OPTION_LABEL, NULL);
        }

        $this->getSortableLinkEnd($column_key);
    }


    /**
     * Get sortable element link start
     *
     * @param string    $column_key
     * @return void
     */
    protected function getSortableLinkStart($column_key)
    {
        if ($this->getColumnOption($column_key, self::COLUMN_OPTION_SORTABLE, false))
        {
            $sortable_url = $this->createUrl(array(
                "column_orders[{$column_key}]" => (isset($this->columnOrders[$column_key]) && $this->columnOrders[$column_key] == 'asc') ? 'desc' : 'asc',
                'single_order' => $column_key
            ));

            echo '<a href="' . $sortable_url . '">';
        }
    }

    /**
     * Get sortable element link end
     *
     * @param string    $column_key
     * @return void
     */
    protected function getSortableLinkEnd($column_key)
    {
        if ($this->getColumnOption($column_key, self::COLUMN_OPTION_SORTABLE, false))
        {
            // Identify Caret
            echo '<i class="fa fa-caret-down"></i>';
            echo '<i class="fa fa-caret-up"></i>';

            echo '</a>';
        }
    }

    /**
     * Get column sortable class
     *
     * @param   string  $column_key
     * @return null|string
     */
    protected function getSortableClass($column_key)
    {
        $classes = NULL;

        if ($this->getColumnOption($column_key, self::COLUMN_OPTION_SORTABLE))
        {
            $classes .= 'sorting ';
            if (array_key_exists($column_key, $this->columnOrders))
            {
                $order_method = $this->columnOrders[$column_key];
                switch ($order_method)
                {
                    case 'desc':
                        $classes .= 'sorting_asc ';
                        break;
                    default:
                        $classes .= 'sorting_desc ';
                }
            }
        }

        return $classes;
    }


    // TABLE BODY STRUCTURE

    /**
     * Create the table body
     *
     * @return string
     */
    function getTableBody()
    {
        ob_start();

        ?>
        <tbody>
        <?php if ($this->totalCount >= 1) : ?>

            <?php foreach ($this->resultRows as $row) : ?>
                <tr data-id="<?php echo $row->{$this->tableId} ?>">
                    <?php foreach ($this->columns as $column_key => $column_options) : ?>
                        <td class="<?php echo $this->getColumnOption($column_key, self::COLUMN_OPTION_CLASSES) ?>">
                            <?php echo $this->getColumnStructure($row, $column_key); ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>

        <?php else : ?>
            <tr class="no-items">
                <td colspan="<?php echo $this->getTotalColumns(); ?>"><?php echo $this->noResults; ?></td>
            </tr>
        <?php endif; ?>
        </tbody>

        <?php
        return ob_get_clean();
    }

    /**
     * Create the column data structure
     *
     * @param object    $row - data row
     * @param string    $column_key - the column name
     * @return string
     */
    protected function getColumnStructure($row, $column_key)
    {
        // Check if method exists i.e colSetID
        $key_col_method = Str::camel('col_set_' . $column_key);

        if (method_exists($this, $key_col_method))
        {
            ob_start();
            $this->{$key_col_method}($row);
            return ob_get_clean();
        }

        return e($row->{$column_key});
    }

    /**
     * Get total columns of the table
     *
     * @return int
     */
    private function getTotalColumns()
    {
        return count($this->columns);
    }

    // GET PAGINATION STRUCTURE

    /**
     * Create the pagination list
     *
     * @return string
     */
    public function getPagination()
    {
        if ($this->perPage == 'all' || ! $this->totalCount)
        {
            return '<span class="pagination">&nbsp;</span>';
        }

        ob_start();
        // calculate advance shortcut
        // Advance shortcut
        if ($this->pageJump)
        {
            $this->jumpNext = min($this->page  + $this->pageJump, $this->lastPage);
            $this->jumpPrev = max($this->page  - $this->pageJump, $this->firstPage);
        }

        $pagi_from = max($this->page - ceil($this->pageJump / 2), $this->firstPage);
        $pagi_to = min($pagi_from + $this->pageJump, $this->lastPage);

        ?>
        <ul class="pagination pagination--offset pull-right">
            <?php
            $this->createPagiList(1, '<i class="icon-double-angle-left"></i> first', $this->page == $this->firstPage);

            if ($this->pageJump)
            {
                $this->createPagiList($this->jumpPrev, "<i class=\"icon-angle-left\"></i> - {$this->pageJump}", $this->jumpPrev == $this->firstPage);
            }

            $this->createPagiList($this->prevPage,'<i class="icon-angle-left"></i> prev',$this->page == $this->firstPage);

            for($i = $pagi_from;$i <= $pagi_to; $i++)
            {
                $this->createPagiList($i,$i,false,($i == $this->page));
            }

            $this->createPagiList($this->nextPage, 'next <i class="icon-angle-right"></i>', $this->page == $this->lastPage);

            if ($this->pageJump)
            {
                $this->createPagiList($this->jumpNext, "{$this->pageJump} + <i class=\"icon-angle-right\"></i>", $this->jumpNext == $this->lastPage);
            }

            $this->createPagiList($this->lastPage, 'last <i class="icon-double-angle-right"></i>',$this->lastPage == $this->page);
            ?>
        </ul>

        <?php
        return ob_get_clean();
    }

    /**
     * Get pagination info
     *
     * @return string
     */
    public function getPaginationInfo()
    {
        $markup  = "<div class=\"pagination-info\">";
        $markup .= "Showing {$this->page} to {$this->lastPage} of {$this->totalCount} entries";
        $markup .= "</div>";

        return $markup;
    }

    /**
     * Create the pagination ul list
     *
     * @param int   $page
     * @param int   $anchor_content
     * @param bool  $disabled
     * @param bool  $active
     */
    protected function createPagiList($page = 1, $anchor_content = 1, $disabled = false, $active =  false)
    {
        // generate classes
        $disabled_class = $disabled ? 'disabled' : NULL;
        $active_class   = $active ? 'active' : NULL;

        ?>
        <li class="<?php echo $disabled_class; ?> <?php echo $active_class ?>" >
            <a href="<?php echo $this->createUrl(array('page'=>$page)) ?>" data-page="<?php echo $page; ?>" >
                <?php echo $anchor_content ?>
            </a>
        </li>
    <?php
    }


    // URL AND PARAMETERS GETTERS AND SETTERS

    /**
     * Create URL with parameters
     *
     * @param   array $parameters - merge parameters from this parameters
     * @return  string
     */
    public function createUrl(array $parameters = array())
    {
        $final_parameters = $this->getFinalParameters();
        $path = $this->getBaseURL();

        // Override default parameters
        $final_parameters = array_merge($final_parameters, $parameters);

        return $path . "?" . http_build_query($final_parameters, null, '&') . $this->buildFragment();
    }


    /**
     * Get / set the URL fragment to be appended to URLs.
     *
     * @param  string|null  $fragment
     * @return $this|string
     */
    public function fragment($fragment = null)
    {
        if (is_null($fragment)) return $this->fragment;

        $this->fragment = $fragment;

        return $this;
    }

    /**
     * Build the full fragment portion of a URL.
     *
     * @return string
     */
    protected function buildFragment()
    {
        return $this->fragment ? '#'.$this->fragment : '';
    }

    /**
     * Get the url, either from set url or from request url
     *
     * @return string
     */
    public function getBaseURL()
    {
        return ($this->baseURL) ? $this->baseURL : Request::url();
    }

    /**
     * Get final parameters
     *
     * @return array
     */
    protected function getFinalParameters()
    {
        return $this->finalParameters;
    }

    /**
     * Set URL and Customer parameters for this tblist
     * Note! This should have no query string, should only be the url
     *
     * @param   string  $url          - The target url that the list will request or submitted to
     * @param   array   $parameters   - custom parameters
     * @return $this
     */
    public function setBaseURL($url, $parameters = array())
    {
        $this->baseURL = $url;
        $this->parameters = $parameters;

        return $this;
    }


    // QUERY RETRIEVAL HELPER

    /**
     * Determine if the given value is a valid page number.
     *
     * @param  int  $page
     * @return bool
     */
    protected function isValidPageNumber($page)
    {
        return $page >= 1 && filter_var($page, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Return column option value
     *
     * @param $column_key           - the column key in columns property
     * @param string $option_name   - the column option key in columns options
     * @param mixed $default
     * @return null
     */
    protected function getColumnOption($column_key, $option_name, $default = NULL)
    {
        return isset($this->columns[$column_key][$option_name]) ? $this->columns[$column_key][$option_name] : value($default);
    }

    // INPUT/PARAMETERS GETTERS AND SETTERS

    /**
     * will just retrieve the input by field name
     *
     * @param   string  $input_name - The input name
     * @param   mixed   $default    - default value, if input is not exists
     * @return  mixed
     */
    protected function getInput($input_name, $default = NULL)
    {
        return $this->hasInput($input_name) ? $this->requestParameters[$input_name] : $default;
    }

    /**
     * will just check the input by field name if exists
     *
     * @param string $field_name
     * @return bool
     */
    protected function hasInput($field_name)
    {
        return isset($this->requestParameters[$field_name]);
    }


    // DATA TABLE

    /**
     * Get Table Content
     *
     * @return string
     */
    public function getTableData()
    {
        ob_start();

        ?>
        <table class="table-list" border="0" cellspacing="0" cellpadding="0" >
            <?php echo $this->getTableHeader(); ?>
            <?php echo $this->getTableFooter(); ?>
            <?php echo $this->getTableBody(); ?>
        </table>
        <?php

        return ob_get_clean();
    }

    public function toArray()
    {
        return array(
            'pagination'    => $this->getPagination(),
            'tableData'     => $this->getTableData(),
            'paginationInfo'=> $this->getPaginationInfo(),
        );
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
