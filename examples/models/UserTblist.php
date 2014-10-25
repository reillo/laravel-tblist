<?php

class UserTblist extends BaseTblist {

    // set no result message
    public $noResults = "No User found.";

    // 'checkbox' column chebkox name.
    // automatically being suffixed with []
    public $cbName = "users_id";

    function __construct()
    {
        // set the database main table name we want to display without
        // prefix (i.e users, posts)
        $this->table = 'users';

        // set the database main table id (i.e post_id, default is id).
        // will be use to set the checkbox in each result rows.
        // $this->tableId = 'user_id';

        // Build query
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
        // tblist use eloquent to process database query

        // Only active users
        $this->query = User::where('active',1);

        // We can use a filter by using eloquent where.
        // check if email url param is set
        if (Input::has('email'))
        {
            $this->query->where('user.email','like','%'.Input::get('email').'%');
        }

        // Debug query
        // $this->query->toSql();

        // If we want to order by default to specific column and
        // then we need use the default order method
        // $this->query->orderBy('users.registration_date'([,'asc|desc']));

        // If we want to order items and show
        // it to the header that is ordered by specific column
        // then we need to assign an array to columnOrders
        $this->columnOrders = array(
            // column_name => 'asc|desc'
            'username' => 'asc'
        );

        // Sometimes we need to specifically select a column. this is
        // the same with the array value passed to model select method.
        // (by default it uses array('*') to select all
        // $this->columnsToSelect = array('*');

    }


    /**
     * Set columns to display
     *
     * return void
     */
    protected function setColumns()
    {
        // choose what columns should be displayed in the table.
        // For table display, we need to specify the column name for the result query.

        $this->addCheckableColumn();

        $this->columns['id'] = array(
            'label'         => 'ID',
            'sortable'      => true,
            'table_column'  => 'users.id',
        );

        $this->columns['username'] = array(
            'label'     => 'Username',
            'sortable'  => true,
            'table_column'  => 'users.username',
        );

        $this->columns['email'] = array(
            'label'         => 'Email',
            'sortable'      => true,
            'classes'       => 'some_class some_class2',
            'table_column'  => 'users.email',
            'thead_attr'    => 'style="width:200px" data-some-attr="example"',
        );

        $this->columns['created_at'] = array(
            'label'         => 'Created At',
            'sortable'      => true,
            'table_column'  => 'users.created_at',
        );

        $this->addActionColumn();

    }

    protected function colSetCreatedAt($row)
    {
        echo date('Y-m-d', strtotime($row->created_at));
    }
}