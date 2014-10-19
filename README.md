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

For the complete example, see src/example folder.

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
    // you can insert 'all' string without quotes to select all item.
    // public $perPageSelection = 'all,1,5,10,25,50,100,250';

    // if option column_checkable is set to true, then this will be
    // use as the name of the checkbox input
    // Note! it will automatically be suffixed with open and close bracket
    //       as multiple checkbox selection
    // (default check_item)
    // public $cbName = "check_item";

    // if option advance_shortcut is set to true, then this property will
    // be used. The pagination will jump into from current page.
    // public $pageJump = 10;

    function __construct()
    {
        // set the database main table name we want to display without
        // prefix (i.e users, posts)
        $this->table = 'users';

        // set the database main table id (i.e post_id, default is id).
        // will be use to set the checkbox in each result rows.
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
            // ([table_without_prefix.])column_name => 'asc|desc'
            'users.username' => 'asc'
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
        $this->columns = array(
            // '([table_without_prefix.])column_name' => array(
            //      'label' => 'Column Label Here', (string)
            //      'sortable' => 'Does this column sortable? true or false', (bool),
            //      'classes' => 'someclass someclass2' (string|optional)
            // ),
            'users.id'   => array(
                'label'     => 'ID',
                'sortable'  => true
            ),
            'users.username'   => array(
                'label'     => 'Username',
                'sortable'  => true
            ),
            'users.email'   => array(
                'label'     => 'Email',
                'sortable'  => true
            ),
            'users.created_at'   => array(
                'label'     => 'Created At',
                'sortable'  => true
            ),
        );
    }
}
```

That's it for now. Let just see the result in action.
Create a route and controller. inside the controller method, and insert below.

```php
$list = new UserTblist();

if (Request::ajax())
{
    return $list->toJson();
}

echo Form:open(array(
    'action'=>'#',
    'method'=>'get',
    'id'=>'user_tblist',
    'class'=>'tblist-form'
));

// some input fields for filter and hidden
echo $list->getTableData();
echo $list->getPagination();
echo $list->getPaginationInfo();

echo Form::close();
```
Also, load jQuery and laravel-tblist jQuery helper.
```html
// Load jQuery
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
// to use ajax, load jquery-tblist js
<script src="/assets/js//tblist.jquery.js"></script>
```
Initialize laravel-tblist
```js
// Initialize tblist
$(function() {
    // Global Options
    $('.tblist-form').tblist({
         start:  function(parameter,$list) {
            // alert('table list request started');
        },
         end:    function(parameter,$list) {
            // alert('table list request ended');
        },
         onSelect:    function() { return false; },

         table:              ".table-list",
         perPage:            ".per-page",
         pagination:         ".pagination",
         paginationInfo:     ".pagination-info",
         ajaxSubmitEnabled:  true
     });
});
```

### Modify column data
on the UserTblist you can add method ```php colSetColumnNameToCamel (i.e colSetUsername, colSetCreatedAt) ```
that accepts only 1 parameter, an object of result row.

in your UserTblist, add this next to setQuery method

```php
protected function colSetId($row)
{
    echo HTML::link(URL::to("/users/$row->id/view"), $row->id);
}

protected function colSetCreatedAt($row)
{
    echo date('Y-m-d', strtotime($row->created_at));
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
    echo "first_name: {$row->first_name}";
    echo '<br />';
    echo "last_name: {$row->last_name}";
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
- set the url property by ```$list->setBaseURL('url','array_parameters')```
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