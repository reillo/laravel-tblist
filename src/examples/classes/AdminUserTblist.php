<?php namespace App\Modules\AdminUser\Libraries;

use Nerweb\Tblist\BaseTblist;
use Input;
use AdminUser;

class AdminUserTblist extends BaseTblist {

    public $table = "admin_users";
    public $tableId = "id";

    public $cbName = "admin_users_id";

    function __construct()
    {
        parent::__construct(array(
            'column_checkable'  => true,
            'action'            => true,
            'advance_shortcut'  => true,
        ));

        $this->noResults = lang('texts.admin_user.no_result_found');
        // we need to create a custom method setQuery,
        $this->setQuery();

        // set table columns
        $this->setColumns();

        // we need to prepare the tblist to run all necessary actions
        $this->prepareList();

        // Debug All Query
        // dd(DB::getQueryLog());
    }

    protected function setQuery()
    {
        // all users
        $this->query = AdminUser::where('id','<>',0);

        // We can use a filter by using eloquent where.
        // check if email url param is set
        if (Input::has('email'))
        {
            $this->query->where('admin_users.email', 'LIKE', Input::get('email').'%');
        }

        if (Input::has('name'))
        {
            $this->query->where(function($query) {
                $query->where('first_name','LIKE', Input::get('name').'%');
                $query->orWhere('admin_users.last_name','LIKE', Input::get('name').'%');
            });
        }

        // Debug query
        // $this->query->toSql();
        $this->columnOrders = array();

        $this->columnsToSelect = array('*');
    }

    protected function setColumns()
    {
        $this->columns = array(
            'id'   => array(
                'label'     => 'ID',
                'sortable'  => true
            ),
            'first_name'   => array(
                'label'     => 'First Name',
                'sortable'  => true
            ),
            'last_name'   => array(
                'label'     => 'Last Name',
                'sortable'  => true
            ),
            'username'   => array(
                'label'     => 'Username',
                'sortable'  => false
            ),
            'email'   => array(
                'label'     => 'Email',
                'sortable'  => false
            ),
            'last_login'   => array(
                'label'     => 'Last Login',
                'sortable'  => false
            )
        );
    }

    protected function colSetAction($row)
    {
        ?>
        <div class="btn-group" id="">
            <a href="<?php echo admin_url("/admin-users/{$row->id}/edit") ?>" class="btn btn-primary"><?php echo lang('texts.admin_user.edit_user') ?></a>
            <button data-toggle="dropdown" type="button" class="btn btn-info dropdown-toggle">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
                <li><a href="<?php echo admin_url("/admin-users/{$row->id}/edit") ?>"><?php echo lang('texts.admin_user.edit_user') ?></a></li>
                <li><a href="<?php echo admin_url("/admin-users/delete/?admin_users_id[]={$row->id}&_token=".urlencode(csrf_token())) ?>"><?php echo lang('texts.admin_user.delete_user') ?></a></li>
            </ul>
        </div>
        <?php
    }

    protected function colSetLastLogin($row)
    {
        echo \Utils::timestampToDateTimeString(strtotime($row->last_login));
    }


}
