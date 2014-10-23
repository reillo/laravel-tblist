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

    public $perPageSelection    = 'all,1,5,10,25,50,100,250';
    public $perPage             = 25;
    public $pageJump            = 10;

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
}