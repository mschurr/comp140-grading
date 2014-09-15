<?php
/*############################
# Helper Modules
############################*/

import('GradingSystem');

// A simple hack to force SSL on all requests.
if (Config::get('ssl.forced', false)) {
	if (!App::getRequest()->https) {
		header('HTTP/1.1 302 Found');
		$url = str_replace("http://", "https://", URL::to('/'));
		header('Location: '.$url);
		die();
	}
}

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

	Route::get('/assignment/{id}/all', 'TeachingAssistantController@showAssignmentAll')
		->where('id', '[0-9]+');
	Route::post('/assignment/{id}/all', 'TeachingAssistantController@gradeAssignmentAll')
		->where('id', '[0-9]+');

	Route::get('/admin/gradebook', 'Admin.Gradebook');
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
	Route::post('/admin/assignments/add', 'Admin.Assignments@addAction');

	Route::get('/admin/assignments/{id}', 'Admin.Assignments@view')
		->where('id', '[0-9]+');

	Route::get('/admin/assignments/{id}/edit', 'Admin.Assignments@edit')
		->where('id', '[0-9]+');
	Route::post('/admin/assignments/{id}/edit', 'Admin.Assignments@editAction')
		->where('id', '[0-9]+');

	Route::get('/admin/assignments/{id}/delete', 'Admin.Assignments@delete')
		->where('id', '[0-9]+');
	Route::post('/admin/assignments/{id}/delete', 'Admin.Assignments@deleteAction')
		->where('id', '[0-9]+');

	Route::get('/admin/assignments/{id}/add-override', 'Admin.Assignments@addOverride')
		->where('id', '[0-9]+');
	Route::post('/admin/assignments/{id}/add-override', 'Admin.Assignments@addOverrideAction')
		->where('id', '[0-9]+');
	Route::post('/admin/assignments/{id}/delete-override', 'Admin.Assignments@deleteOverride')
		->where('id', '[0-9]+');

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
