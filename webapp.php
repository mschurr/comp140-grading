<?php
/*############################
# Helper Modules
############################*/

import('GradingSystem');

/*############################
# Public Pages
############################*/

Route::get('/', function(Request $request, Response $response){
	if(!$request->session->user)
		return Redirect::to('AuthController@login');
	if(!$request->session->user->hasPrivilege(Privilege::TeachingAssistant))
		return 403;
	return Redirect::to('TeachingAssistantController@showAssignments');
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
	// Assignments
	Route::get('/admin/assignments', 'Admin.Assignments@get');
	Route::get('/admin/assignments/add', 'Admin.Assignments@add');
	Route::get('/admin/assignments/edit/{id}', 'Admin.Assignments@edit')->where('id', '[0-9]+');
	Route::post('/admin/assignments/add', 'Admin.Assignments@addAction');
	Route::post('/admin/assignments/edit/{id}', 'Admin.Assignments@editAction')->where('id', '[0-9]+');
	Route::get('/admin/assignments/delete/{id}', 'Admin.Assignments@delete')->where('id', '[0-9]+');
	Route::post('/admin/assignments/delete/{id}', 'Admin.Assignments@deleteAction')->where('id', '[0-9]+');

	// Instructors

	// Graders

	// Grader Assignments

	// Grader Overrides

	// Grades
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