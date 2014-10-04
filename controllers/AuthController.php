<?php
require_once(FRAMEWORK_ROOT."/plugins/CAS.php");

use mschurr\framework\plugins\CAS\CASAuthenticator;

/**
 * A controller for handling authentication via the CAS authentiation driver.
 */
class AuthController extends Controller {
  public function login() {
    if ($this->auth->loggedIn) {
      return Redirect::to('/');
    }

    try {
      $this->auth->attempt(null, null, true);
    } catch (AuthException $e) {
      return 400; // Bad Request
    }
  }

  public function loginAction() { return 405; }

  public function logout() {
    if (!$this->auth->loggedIn) {
      return Redirect::to('/');
    }

    $this->auth->logout();
  }
}


class zzOldAuthController extends Controller
{
	public function get_cas()
	{
		if(isset($this->request->get['url']))
			$this->session['_login_redirect'] = $this->request->get['url'];
		else
			$this->session['_login_redirect'] = '/';

		try {
			$result = $this->auth->attempt(null, null, true);
			$url = isset($this->session['_login_redirect']) ? URL::to($this->session['_login_redirect']) : URL::to('/');
			unset($this->session['_login_redirect']);
			if($url === null || !$url->isInternal()) $url = URL::to('/');
			return Redirect::to($url);
		}
		catch (Exception $e) {
			return Redirect::to('/');
		}
	}

	public function login($error=null)
	{
		if(Config::get('auth.driver') === 'cas')
			return $this->get_cas();

		// Determine destination.
		$destination = isset($this->request->get['url']) ? URL::to($this->request->get['url']) : URL::to('/');
		if($destination === null || !$destination->isInternal()) $destination = URL::to('/');

		// If the user is already logged in, redirect.
		if($this->auth->loggedIn())
			return Redirect::to($destination);

		// Otherwise, let's display the form.
		return View::make('BookExchange.Login')->with(array(
			'user' => $this->user,
			'error' => $error,
			'request' => $this->request,
			'destination' => isset($this->request->post['destination']) ? $this->request->post['destination'] : $destination
		));
	}

	public function loginAction()
	{
		// Determine the redirection URL.
		if(isset($this->request->post['destination'])) {
			$url = URL::to($this->request->post['destination']);
			if($url === null || !$url->isInternal()) $url = URL::to('/');
		}
		else {
			$url = URL::to('/');
		}

		// If the user is already logged in, just redirect.
		if($this->auth->loggedIn())
			return Redirect::to($url);

		// Protecte against CSRF attacks.
		//if(!CSRF::check('login', $this->request->post['_csrf']))
		//	return $this->get("The server detected a problem with your request; try again.");

		// Ensure that the user sent us the mandatory information.
		if(!isset($this->request->post['username'])
		|| !isset($this->request->post['password'])
		|| strlen($this->request->post['username']) == 0
		|| strlen($this->request->post['password']) == 0
		|| strlen($this->request->post['username']) > 50
		|| strlen($this->request->post['password']) > 50)
			return $this->login("You must enter a username and password.");

		try {
			// Attempt the login.
			$this->auth->attempt($this->request->post['username'], $this->request->post['password'], isset($this->request->post['persistent']));

			// Redirect on success.
			return Redirect::to($url);
		}
		catch(AuthException $e) {
			// Display form with errors on failure.
			return $this->login( (string)$e );
		}
	}

	public function logout()
	{
		// Check for a URL.
		if(isset($this->request->get['url']))
			$this->session['_logout_redirect'] = $this->request->get['url'];
		else
			$this->session['_logout_redirect'] = '/';

		// If the user is logged in, log them out.
		if($this->auth->loggedIn) {
			$this->auth->logout();
		}

		// Redirect the user to _GET[url] if set or / otherwise.
		$url = isset($this->session['_logout_redirect']) ? URL::to($this->session['_logout_redirect']) : URL::to('/');
		unset($this->session['_logout_redirect']);

		if($url === null || !$url->isInternal()) $url = URL::to('/');

		return Redirect::to($url);
	}
}
