<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laravel Tblist Example Simple</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Font awesome -->
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- bootstrap 3.0.2 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
    <!-- Tblist Styles  -->
    <link rel="stylesheet" href="<?php echo URL::to('/packages/nerweb/laravel-tblist/css/tblist-form.css') ?>">

    <!-- jQuery 2.0.2 -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

</head>

<body class="skin-black">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
            @yield('content')
        </div>
      </div>
    </div>

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <!-- Tblist JS jQuery Plugin  -->
    <script src="<?php echo URL::to('/packages/nerweb/laravel-tblist/js/tblist.jquery.js') ?>"></script>

    <script>
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
    </script>
</body>
</html>