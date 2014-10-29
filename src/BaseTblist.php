<?php namespace Nerweb\Tblist;

use URL;

/**
 * Laravel Tblist
 * http://github.com/nerweb93/laravel-tblist
 *
 * Class BaseTblist
 * @package Tblist
 */
abstract class BaseTblist extends Tblist {

    /**
     * Per page selection
     *
     * @var string $perPageSelection
     */
    public $perPageSelection = 'all,1,5,10,25,50,100,250';

    /**
     * The checkbox or row selection checkbox name
     *
     * @var string $cbName
     */
    public $cbName = "check_item";

    // Set Columns for sortable and action

    /**
     * Add sortable to column
     *
     * @return void
     */
    protected function addCheckableColumn()
    {
        $this->columns['checkable'] = array(
            'thead_attr'    => 'style="width:40px"',
        );
    }

    /**
     * Add action to column
     *
     * @return void
     */
    protected function addActionColumn()
    {
        $this->columns['action'] = array(
            'thead_attr'    => 'style="width:100px"',
        );
    }

    // Header

    /**
     * Create header column for checkable column
     *
     * @param $column_key
     */
    protected function colSetHeaderCheckable($column_key)
    {
        ?>
        <label>
            <input autocomplete="off" type="checkbox" id="<?php echo $this->table; ?>-check-all" class="cb-select-all input-beauty" name="check" value="">
            <span class="lbl"></span>
        </label>
        <?php
    }

    /**
     * Create header column for action column
     *
     * @param $column_key
     */
    protected function colSetHeaderAction($column_key)
    {
        echo "Actions";
    }


    // Body Content

    protected function colSetCheckable($row) {
        ?>
        <label>
            <input  autocomplete="off"
                    class="cb-select input-beauty cb-select-id-<?php echo $row->{$this->tableId}; ?>"
                    type="checkbox"
                    name="<?php echo $this->cbName ?>"
                    value="<?php echo $row->{$this->tableId}; ?>">

            <span class="lbl"></span>
        </label>
        <?php
    }

    protected function colSetAction($row)
    {
        ?>
        <div class="btn-group" id="">
            <a href="<?php echo URL::to("/users/{$row->id}/view") ?>" class="btn btn-primary">View</a>
            <button data-toggle="dropdown" type="button" class="btn btn-info dropdown-toggle">
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu pull-right text-left">
                <li>
                    <a href="<?php echo URL::to("/users/export/?users_id[]={$row->{$this->tableId}}") ?>">
                        Export
                    </a>
                </li>
                <li>
                    <a href="<?php echo URL::to("/users/{$row->{$this->tableId}}/edit") ?>">
                        Edit
                    </a>
                </li>
                <li>
                    <a href="<?php echo URL::to("/users/delete/?users_id[]={$row->{$this->tableId}}") ?>"  class="confirm-delete">
                        Delete
                    </a>
                </li>
            </ul>
        </div>
        <?php
    }

    /**
     * generate a per page select block
     *
     * @return string
     */
    public function getPerPageLimit()
    {
        ob_start();
        $selection = explode(',',$this->perPageSelection);

        ?>
        <div class="field filter-option-lg form-group">
            <label class="field-label" for="#attention-of-search">Per page</label>
            <select name="per_page" class="per-page form-control" data-width="80px" >
                <?php foreach($selection as $val):
                    $selected_attr = ($val == $this->perPage) ? "selected=\"selected\"" : null; ?>
                    <option <?php echo $selected_attr; ?> class="<?php echo $val; ?>"><?php echo $val; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
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
     * Get Table Content
     *
     * @return string
     */
    public function getTableData()
    {
        ob_start();

        ?>
        <table class="table table-bordered table-list" border="0" cellspacing="0" cellpadding="0" >
            <?php echo $this->getTableHeader(); ?>
            <?php echo $this->getTableFooter(); ?>
            <?php echo $this->getTableBody(); ?>
        </table>
        <?php

        return ob_get_clean();
    }
}