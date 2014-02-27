<?php

class AuthController extends Controller
{
	public function login($errors=array())
	{
		return View::make('Auth.Login')->with(array(
			'errors' => $errors
		));
	}

	public function loginAction()
	{
		try {
			
		}
		catch(AuthException $exception) {

		}
	}

	public function logout()
	{

	}
}