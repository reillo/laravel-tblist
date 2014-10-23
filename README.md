laravel-tblist v1.1.0
==============

Simple admin table listing for bootstrap 3.\* and laravel 4.\*|5.*

----------

## Installation

Add the following to your `composer.json` file:

```json
"nerweb/laravel-tblist": "1.1.x"
```

Then, run `composer update nerweb/laravel-tblist` or `composer install` if you have not already installed packages.

## Simple Example

For complete example, see `src/example` folder.

First, create a class name `UserTblist`.


```php
use Nerweb\Tblist\BaseTblist;

class UserTblist extends BaseTblist {

    // set no result message
    public $noResults = "No User found.";

    function __construct()
    {
        $this->table = 'users';
        
        // Supported options
        parent::__construct(array(
            'column_checkable'  => true,
            'advance_shortcut'  => true,
        ));

        $this->setQuery();
        $this->setColumns();
        $this->prepareList();
    }

    protected function setQuery()
    {
        $this->query = User::where('active',1);
        $this->columnsToSelect = array('*');
    }

    protected function setColumns()
    {
        $this->addCheckableColumn();

        $this->columns['username'] = array(
            'label'     => 'Username',
            'sortable'  => true,
            'table_column'  => 'users.username',
        );
        
        $this->columns['email'] = array(
            'label'         => 'Email',
            'sortable'      => true,
            'classes'       => 'someclass someclass2',
            'table_column'  => 'users.email',
            'thead_attr'    => 'style="width:200px" data-someattr="example"',
        );

        $this->addActionColumn();
    }
}
```
Create a route and and its controller. Inside the controller method, insert below.

```php
$list = new UserTblist();

return View::make('users.index', array('list', $list));
```
Create the blade `user/index.blade.php` and insert below.

```php
{{ Form:open(array(
    'action' => $list->getBaseURL(),
    'method' =>'get',
    'id' =>'user_tblist',
    'class' =>'tblist-form'
)) }}
{{ $list->getTableData() }}
{{ $list->getPagination() }}
{{ $list->getPaginationInfo() }}
{{ Form::close() }}
```

-------------


## API Reference


### Column

The "column" property stores the data for column name and column options.

#### Column key

The `column key` may or may not have exists in the result row. Column key also assumes
as the name to be sorted.

Note! if `column key` does not exists in the result row, you should create a protected method
`colSetTheColumnName` as custom column

#### Column options

|Options | Required | type     |Description
|------|----------|------------|-------
|`label`        | `required` | `string` |Column header name display
|`sortable`     | `required` | `bool`   |Whether sortable or not
|`table_column` | `optional` | `string` |if set, then use its value instead of the `column key` as the column name to sort (i.e `roles.admin`, `users.admin`)
|`classes`      | `optional` | `string` |table column classes. Note! Both applied to header, footer and body column
|`thead_attr`   | `optional` | `string` |Table header attribute string


### Custom Column Display

Add protected method `colSetColumnNameToCamel` (i.e `colSetUsername`, `colSetCreatedAt`)
that accepts only 1 parameter, an object of result row. Then echo or display the string.

#### Example

in your class, `UserTblist` for this example. Add this next to `setColumns` method

```php

protected function colSetUsername($row)
{
    echo Html::link("/users/{$row->id}/view", $row->username);
}
```

### Custom Column
Sometimes we want to show column that is not in the result object row.

To do this, add a `column key` to `columns` property and make sure that the `sortable` options is set to `false`.

and

create a method called method called `colSetYourColumnName($row)` that accepts result row.


### Multiple Tblist

Sometimes we may want to display multiple table listing on the same page.

To do this,

 - on class initialization, in this case `$list = new UserTblist()`. Override base URL via `$list->setBaseURL($url, $parameters)` method.
 - set the tblist form action with `$list->getBaseURL()`.
 - create the route and controller and return the json data of the tblist.


## License
This project is open-sourced software licensed under the [MIT license][mit-url].

[mit-url]: http://opensource.org/licenses/MIT
