<?php

class NerwebUserController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function getIndex()
	{
        $list = new NerwebUserTblist();
        $list->prepareList();

        if (Request::ajax())
        {
            return $list->toJson();
        }

		return View::make('user.index')->with(array(
            'list' => $list
        ));
	}

}
