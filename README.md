laravel-tblist
==============

laravel simple admin table listing

----------
## Installation
Add the following to your `composer.json` file:

```json
"nerweb/laravel-tblist": "dev-master"
```

Then, run `composer update nerweb/laravel-tblist` or `composer install` if you have not already installed packages.

## Usage

I assume that you have already created a class for your intended model.
First we need to create our own class and named it UserTblist.

```php
class UserTblist extends \Nerweb\Tblist\BaseTblist {

    // set no result message
    public $noResults = "No User found.";

    // sometimes we want to start at page 2 (default page 1, of course)
    // public $page = 1;

    // set per page, the number of item we need to show in each page (default 25)
    public $perPage = 15;

    // set per page drop down selection (default '1,5,10,25,50,100,250')
    // accepts string separated by comma or an array of item
    // you can insert all string to select all item.
    // public $perPageSelection = '1,5,10,25,50,100,250';

    // if option column_checkable is set to true, then then checkbox
    // it will generate will be use the value of this property as
    // input name.
    // Note! it will automatically be suffixed with open and close bracket
    //       to specific multiple selection or array
    // (default check_item)
    // public $cbName = "check_item";

    // if option advance_shortcut is set to true, then this property will
    // be used. The pagination jump descripancy from current page.
    // public $pageJump = 10;

    function __construct()
    {
        // set the database main table name we want to display (i.e users, posts)
        $this->table = 'users';

        // set the database main table id (i.e post_id, default is id).
        // $this->tableId = 'user_id';

        // override default support
        // read more about support here.
        parent::__construct(array(

            // will append a checkable column for each row.
            'column_checkable'  => true,

            // will prepend a action column for each row.
            'action'            => false,

            // use quick jump to pagination page
            'advance_shortcut'  => false,
        ));

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
        // tblist use eloquent to process database query

        // all users
        // $this->query = new User();

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

        // If we want to order items by default to specific column and
        // not being displayed in the header or does not exists in column properties
        // then we use the default order method by laravel.
        // $this->query->orderBy('users.registration_date'([,'asc|desc']));

        // If we want to order items and show
        // it to the header that is ordered by specific column
        // then we need to assign an array to columnOrders
        $this->columnOrders = array(
            // ([table_without_prefix.])column_name => 'asc|desc'
            'posts.post_title' => 'asc',
            'posts.post_mime_type' => 'asc',
        );

        // Sometimes we need to specifically select a column
        // this property accepts array of columns. this is the same
        // with the value passed to $user->select(array()).
        // (by default it uses array('*') to select all
        // $this->columnsToSelect = array('*');

    }


    protected function setColumns()
    {
        // the last but not the least, is to choose what columns should be displayed in the table.
        // For table display, we need to specify the column name for the result query.
        $this->columns = array(
            // '([table_without_prefix.])column_name' => array(
            //      'label' => 'Column Label Here', (string)
            //      'sortable' => 'Does this column sortable? true or false', (bool),
            //      'classes' => 'someclass someclass2' (string|optional)
            // ),
            'users.id'   => array(
                'label'     => 'Title',
                'sortable'  => true
            ),
            'users.username'   => array(
                'label'     => 'Description',
                'sortable'  => false
            ),
            'users.email'   => array(
                'label'     => 'Description',
                'sortable'  => false
            ),
            'users.created_at'   => array(
                'label'     => 'Description',
                'sortable'  => false
            ),
        );
    }
}
```

That's it for now. Let just see the result in action. Create a route and controller, inside the controller method and insert below.

```php
$list = new UserTblist();

if (Request::ajax())
{
    return $list->toJson();
}
else
{
    echo Form:open(array('action'=>'#','method'=>'get','id'=>'user_tblist','class'=>'tblist'));
    // some input fields for filter and hidden
    echo $list->getTableData(),
    echo $list->getPagination(),
    echo $list->getPaginationInfo(),
    echo Form::close();

}
```
Also, load jQuery and laravel-tblist jQuery helper.
```html
// Load jQuery
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
// to use ajax, load jquery-tblist js
<script src="/assets/js/jquery-tblist.js"></script>
```
Initialize laravel-tblist
```js
// Initialize tblist
'use strict';
$j = jQuery.noConflict();

$j(function() {

    // Global Options
    var options = {
        start:  function(parameter,$list) { alert('table list request started'); },
        end:    function(parameter,$list) { alert('table list request ended'); },
        onSelect:    function() { return false; },

        table:              ".table-list",
        perPage:            ".per-page",
        pagination:         ".pagination",
        paginationInfo:     ".pagination-info",
        ajaxSubmitEnabled:  true
    };

    $j('.tblist').tblist(options);

    // listen to check and un check box.
    // get currently all check table id
    // Note! Assume that class .tblist has been initiated with tblist plugin.

    // get all selected item from the current page.
    $j('#user_tblist').tblist('count');

    // Get all the checked item.
    $j('#user_tblist').tblist('getCb');

    //Programmatically select an item.
    $j('#user_tblist').tblist('selectCb',[the_tableId]);

    // Programmatically select all item.
    $j('#user_tblist').tblist('selectAllCb');

    // Programmatically remove an item.
    $j('#user_tblist').tblist('removeCb',[the_tableId]);

    // Programmatically Remove all item.
    $j('#user_tblist').tblist('removeAllCb');

});
```

### Modify column data
on the UserTblist you can add method ```php colSetColumnNameToCamel (i.e colSetUsername, colSetCreatedAt) ```
that accepts only 1 parameter, an object of result row.

in your UserTblist, add this next to setQuery method

```php
public function colSetUsername($row)
{
    // display default
    // echo $row->username;

    // custom display
    // do what ever you want and echo the display

    // Note! that it is important that you echo the display
    // to capture with ob_start() and ob_get_clean();

    echo "this username \"{$row->username}\" has an email of {$row->email}";
}

public function colSetCreatedAt($row)
{
    echo date('d/m/Y',$row->created_at);
}
```


### Add custom column

Add the column info to columns property and create a method with colSetYourColumnName($row) and make sure that the sortable key is false is false.
  
Note! It requires to create ```php colSetTheColumnName ``` method to be created.

insert this to columns property value after 'users.created_at' key

```php
'user_full_info'   => array(
    'label'     => 'User Summary',
    'sortable'  => false
),
```
and Add the method below

```php
public function colSetUserFullInfo($row)
{
    echo "firstname: {$row->firstname}";
    echo '<br />';
    echo "lastname: {$row->lastname}";
    echo '<br />';
    echo "age: {$row->age}";
    echo '<br />';
}
```



### Use multiple tblist for the same page

#### Method 1: 
submit the form to the same url, and add a hidden field to the form
that specifies the form, you can then use that to check which tblist
it is intended to.

#### Method 2:
- set the url property by ```php $list->setBaseURL('url','optional_parameters') ```
- set the form action attribute to the url just being set.
- create the url route and controller, initiate the child class (i.e UserTblist) and return the ajax data similar below.

```php
if (Request::ajax())
{
    return $list->toJson();
}
```

## License
This project is open-sourced software licensed under the [MIT license][mit-url].

[mit-url]: http://opensource.org/licenses/MIT