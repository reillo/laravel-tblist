<?php namespace App\Modules\User\Libraries;

use Nerweb\Tblist\BaseTblist;
use Input;
use User;
use Utils;

class UserTblist extends BaseTblist {

    public $table = "users";
    public $tableId = "id";

    public $cbName = "users_id";

    function __construct()
    {
        parent::__construct(array(
            'column_checkable'  => true,
            'action'            => true,
            'advance_shortcut'  => true,
        ));

        $this->noResults = lang('texts.users.no_result_found');
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
        $this->query = \User::where('id', '<>', 0);

        // We can use a filter by using eloquent where.
        // check if email url param is set
        if (\Input::has('email'))
        {
            $this->query->where('users.email','like','%'.\Input::get('email').'%');
        }

        if (\Input::has('name'))
        {
            $this->query->where(\DB::raw('concat(users.first_name," ",users.last_name)'),'like','%'.\Input::get('name').'%');
        }

        if (\Input::has('user_type'))
        {
            $this->query->where('user_type',\Input::get('user_type'));
        }

        if (\Input::has('status'))
        {
            $this->query->where('status',\Input::get('status'));
        }

        // Debug query
        // $this->query->toSql();
        $this->columnOrders = array();

        $this->columnsToSelect = array('*');
    }


    protected function setColumns()
    {
        $this->columns = array(
            'users.id'   => array(
                'label'     => lang('texts.id'),
                'sortable'  => true,
                'classes'   => 'hidden-xs hidden-sm'
            ),
            'users.first_name'   => array(
                'label'     => lang('texts.first_name'),
                'sortable'  => true
            ),
            'users.last_name'   => array(
                'label'     => lang('texts.last_name'),
                'sortable'  => true,
                'classes'   => 'hidden-xs'
            ),
            'users.email'   => array(
                'label'     => lang('texts.email'),
                'sortable'  => true
            ),
            'users.user_type'   => array(
                'label'     => lang('texts.user_type'),
                'sortable'  => true,
                'classes'   => 'hidden-xs'
            ),
            'users.organization_name'   => array(
                'label'     => lang('texts.organization_name'),
                'sortable'  => true,
                'classes'   => 'hidden-xs hidden-sm'
            ),
            'users.status'   => array(
                'label'     => lang('texts.status'),
                'sortable'  => true,
                'classes'   => 'hidden-xs'
            ),
            'users.last_login'   => array(
                'label'     => lang('texts.last_login'),
                'sortable'  => true,
                'classes'   => 'hidden-xs hidden-sm'
            ),
            'users.registration_date'   => array(
                'label'     => lang('texts.registration_date'),
                'sortable'  => true,
                'classes'   => 'hidden-xs hidden-sm'
            )
        );
    }

    protected function colSetAction($row)
    {
        ?>
        <div class="btn-group" id="">
            <a href="<?php echo admin_url("/users/{$row->id}/view") ?>" class="btn btn-primary"><?php echo lang('texts.users.view_user') ?></a>
            <button data-toggle="dropdown" type="button" class="btn btn-info dropdown-toggle">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right text-left">
                <li><a href="<?php echo admin_url("/users/export/?users_id[]={$row->id}&_token=".urlencode(csrf_token())) ?>"><?php echo lang('texts.users.export_user') ?></a></li>
                <li><a href="<?php echo admin_url("/users/{$row->id}/edit") ?>"><?php echo lang('texts.users.edit_user') ?></a></li>
                <li>
                    <a class="confirm_action" data-message="<?php echo lang('texts.users.delete_confirmation') ?>" href="<?php echo admin_url("/users/delete/?users_id[]={$row->id}&_token=".urlencode(csrf_token())) ?>">
                        <?php echo lang('texts.users.delete_user') ?>
                    </a>
                </li>
            </ul>
        </div>
        <?php
    }

    protected function colSetLastLogin($row)
    {
        echo Utils::timestampToDateTimeString(strtotime($row->last_login));
    }

    protected function colSetRegistrationDate($row)
    {
        echo Utils::timestampToDateString(strtotime($row->registration_date));
    }

    protected function colSetOrganizationName($row)
    {
        if ($row->user_type == \User::USER_TYPE_PRIVATE)
        {
            echo lang('texts.not_applicable');
        }
        else
        {
            echo $row->organization_name;
        }

    }

}
