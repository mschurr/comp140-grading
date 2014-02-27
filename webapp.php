<?php
/**
 * Routing for standard teaching-assistant areas.
 */
Route::filter(function(Request $request){
	if(!$request->session->auth->loggedIn)
		return Redirect::to('AuthController@login');
	if(!$request->session->auth->user->hasPrivilege(Privilege::TeachingAssistant))
		return 403;
	return true;
}, function(){
	Route::get('/grade', 'TeachingController'); // show days
	Route::any('/grade/{year}/{month}/{day}', 'GradingController')
		->where(function(Request $request, $year, $month, $day){
			if(!is_integer($year) || $year < 2000 || $year > date('Y'))
				return false;
			if(!is_integer($month) || $month < 1 || $month > 12)
				return false;
			if(!is_integer($day) || $day < 1 || $day > 31)
				return false;
			return true;
		});
});

/**
 * Routing for instructor administrative areas.
 */
Route::filter(function(Request $request){
	if(!$request->session->auth->loggedIn)
		return Redirect::to('AuthController@login');
	if(!$request->session->auth->user->hasPrivilege(Privilege::Instructor))
		return 403;
	return true;
}, function(){
	Route::get('/admin', 'AdminController');
	Route::any('/admin/teaching-assistants', 'AdminController');
	Route::any('/admin/assignment', 'AdminController');
	Route::any('/admin/grades', 'AdminController');
	Route::any('/admin/students', 'AdminController');
});

/**
 * Unrestricted routing.
 */
Route::get('/login', 'AuthController@login');
Route::post('/login', 'AuthController@loginAction');
Route::get('/logout', 'AuthController@logout');

/**
 * Error routing.
 */
Route::error(404, function(Request $request, Response $response){
	return View::make('errors.404');
});

Route::error(500, function(Request $request, Response $response){
	return View::make('errors.500');
});