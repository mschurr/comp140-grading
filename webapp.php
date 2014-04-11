<?php
/*############################
# Helper Modules
############################*/

import('GradingSystem');

/*############################
# Public Pages
############################*/

Route::get('/', function(Request $request, Response $response){
	$user = $request->session->user;

	return View::make('Home')->with(array(
		'user' => $user,
		'authorized' => $user && $user->hasPrivilege(Privilege::TeachingAssistant)
	));
});

/*############################
# Teaching Assistant Pages
############################*/

$teachingAssistantFilter = function(Request $request){
	if(!$request->session->auth->loggedIn)
		return Redirect::to('AuthController@login');
	if(!$request->session->auth->user->hasPrivilege(Privilege::TeachingAssistant))
		return 403;
	return true;
};

Route::filter($teachingAssistantFilter, function() {
	Route::get('/assignments', 'TeachingAssistantController@showAssignments');
	Route::get('/assignment/{id}', 'TeachingAssistantController@showAssignment')
		->where('id', '[0-9]+');
	Route::post('/assignment/{id}', 'TeachingAssistantController@gradeAssignment')
		->where('id', '[0-9]+');
});

/*############################
# Administrator Pages
############################*/

$adminFilter = function(Request $request) {
	if(!$request->session->auth->loggedIn)
		return Redirect::to('AuthController@login');
	if(!$request->session->auth->user->hasPrivilege(Privilege::Instructor))
		return 403;
	return true;
};

Route::filter($adminFilter, function(){
	
});

/*############################
# Authentication Service
############################*/

Route::get ('/login',  'AuthController@login'      );
Route::post('/login',  'AuthController@loginAction');
Route::get ('/logout', 'AuthController@logout'     );

/*############################
# Error Pages
############################*/

Route::error(404, function(Request $request, Response $response){
	return View::make('errors.404');
});

Route::error(500, function(Request $request, Response $response){
	return View::make('errors.500');
});