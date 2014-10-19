<?php namespace Nerweb\Tblist;

/**
 * Laravel Tblist v1.0.0
 * http://github.com/nerweb93/laravel-tblist
 *
 * Class BaseTblist
 * @package Tblist
 */
abstract class BaseTblist extends Tblist {

    function __construct($support = null)
    {
        $this->perPageSelection    = '1,5,10,25,50,100,250';
        $this->perPage             = 25;
        $this->pageJump            = 10;

        // Set Base Support
        if ( is_null($support))
        {
            $support = array(
                'column_checkable'  => true,
                'action'            => false,
                'advance_shortcut'  => false,
            );
        }

        parent::__construct($support);
    }

    protected function colSetHeaderCheckable()
    {
        ?>
        <label>
            <input autocomplete="off" type="checkbox" id="<?php echo $this->table; ?>-check-all" class="cb-select-all input-beauty" name="check" value="">
            <span class="lbl"></span>
        </label>
        <?php
    }

    protected function colSetCheckable($db_row) {
        $row_id = $db_row->{$this->tableId};
        ?>
        <label>
            <input  autocomplete="off" class="cb-select input-beauty cb-select-id-<?php echo $row_id; ?>" type="checkbox" name="<?php echo $this->cbName ?>" value="<?php echo $row_id; ?>">
            <span class="lbl"></span>
        </label>
    <?php
    }

}