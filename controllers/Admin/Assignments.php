<?php

class Assignments extends Controller
{
	/**
	 * Displays a paginated list of all assignments.
	 */
	public function get()
	{
		try {
			$paginator = GradingSystem::getAllAssignments();

			$page = 0;
			if(isset($this->request->get['page'])) {
				if(!ctype_digit($this->request->get['page']))
					return 404;
				$page = ((int) $this->request->get['page']) - 1;
			}

			return View::make('Admin.AssignmentList')->with(array(
				'assignments' => $paginator[$page],
				'pages' => count($paginator),
				'page' => $page + 1,
				'pageUrl' => function($page) {
					return URL::to(array($this, 'get'))->with(array('page' => $page));
				}
			));
		} catch (BadAccessException $e) {
			return 404;
		}
	}

	/**
	 * Displays the form to add a new assignment.
	 */
	public function add($errors = array())
	{
		return $this->edit(null, $errors);
	}

	/**
	 * Handles addition of new assignments.
	 */
	public function addAction()
	{
		return $this->editAction(null);
	}

	/**
	 * Displays the form to edit an assignment.
	 */
	public function edit($id, $errors = array())
	{
		$post = $this->request->post;
		$assignment = NullObject::sharedInstance();

		if($id !== null) {
			$assignment = GradingSystem::getAssignment($id);

			if(!$assignment)
				return 404;
		}

		return View::make('Admin.AssignmentEdit')->with(array(
			'data' => new ImmutableDefaultArrayMap(function($key) use (&$post, &$assignment){
				if($key === 'date') {
					if(isset($post[$key]))
						return $post[$key];
					if(!$assignment instanceof NullObject)
						return str_pad($assignment['month'], 2, "0", STR_PAD_LEFT).' / '.str_pad($assignment['day'], 2, "0", STR_PAD_LEFT).' / '.str_pad($assignment['year'], 4, "0", STR_PAD_LEFT);
					return NullObject::sharedInstance();
				}
				if(!$assignment instanceof NullObject && isset($assignment[$key]))
					return $assignment[$key];
				if(isset($post[$key]))
					return $post[$key];
				return NullObject::sharedInstance();
			}),
			'errors' => new ImmutableDefaultArrayMap(null, $errors),
			'target' => ($id === null) ? URL::to(array($this, 'addAction')) : URL::to(array($this, 'editAction'), $id),
			'id' => $id,
			'assignment' => $assignment
		));
	}

	/**
	 * Display confirmation prompt for deleting an assignment.
	 */
	public function delete($id, $errors = array())
	{	
		$assignment = GradingSystem::getAssignment($id);

		if(!$assignment)
			return 404;

		return View::make('Admin.AssignmentDelete')->with(array(
			'a' => $assignment,
			'errors' => new ImmutableDefaultArrayMap(null, $errors)
		));
	}

	/**
	 * Handles deletion of an assignment from the system.
	 */
	public function deleteAction($id)
	{
		if(!CSRF::check($this->request->post['_csrf']))
			return 400;

		if(!isset($this->request->post['confirm']))
			return $this->delete($id, array('confirm' => 'You must check the box in order to delete the assignment.'));

		$assignment = GradingSystem::getAssignment($id);

		if(!$assignment)
			return 404;

		// Delete the assignment.
		GradingSystem::dropAssignment($id);

		// Redirect.
		Redirect::to(array($this, 'get'));
	}

	/**
	 * Handles edits of an assignment.
	 */
	public function editAction($id = null)
	{
		if($id !== null && GradingSystem::getAssignment($id) === null)
			return 404;

		$errors = array();

		// Validate CSRF
		if(!CSRF::check($this->request->post['_csrf']))
			return 400;

		// Validate Description
		if(strlen($this->request->post['description']) < 3
		|| strlen($this->request->post['description']) > 100)
			$errors['description'] = 'You must enter a description between 3 and 100 characters in length.';

		// Validate Section
		if(!ctype_digit($this->request->post['section'])
		|| !isset(GradingConfig::$section[(int)$this->request->post['section']]))
			$errors['section'] = 'You must select a valid section.';

		// Determine M, D, Y and validate.
		$matches = array();
		$reg = "/^([0-9]{2}) \/ ([0-9]{2}) \/ ([0-9]{4})$/s";
		$status = preg_match($reg, $this->request->post['date'], $matches);
		$month = 0;
		$day = 0;
		$year = 0;

		if($status !== 1)
			$errors['date'] = 'You must enter a date in the required format.';
		else {
			$month = (int) $matches[1];
			$day = (int) $matches[2];
			$year = (int) $matches[3];

			if(!checkdate($month, $day, $year))
				$errors['date'] = 'You must enter a valid date.';
		}

		// Handle Errors
		if(count($errors) > 0)
			return $this->add($errors);

		// Insert new record.
		if($id === null) {
			$id = GradingSystem::addAssignment(
				$month, 
				$day, 
				$year, 
				$this->request->post['description'], 
				$this->request->post['section']
			);
		// Update existing record.
		} else {
			GradingSystem::updateAssignment(
				$id,
				$month, 
				$day, 
				$year, 
				$this->request->post['description'], 
				$this->request->post['section']
			);
		}
		
		// Redirect to result.
		return Redirect::to([$this, 'view'], $id);
	}

	/**
	 * Displays assignment information and overrides.
	 */
	public function view($id, $errors = array())
	{
		$assignment = GradingSystem::getAssignment($id);

		if(!$assignment)
			return 404;

		$students = GradingSystem::getAllStudentsInSection($assignment['section']);
		$students->count();
		$grades = GradingSystem::getGrades($id);
		$graders = GradingSystem::getGradersForAssignment($id);
		$overrides = GradingSystem::getOverridesForAssignment($id);

		return View::make('Admin.AssignmentView')->with(array(
			'assignment' => $assignment,
			'overrides' => $overrides,
			'grades' => new ImmutableDefaultArrayMap("-", $grades),
			'students' => $students,
			'graders' => new ImmutableDefaultArrayMap(0, $graders),
			'errors' => new ImmutableDefaultArrayMap(null, $errors)
		));
	}

	/**
	 *
	 */
	public function addOverride($id, $errors = array())
	{
		$assignment = GradingSystem::getAssignment($id);

		if(!$assignment)
			return 404;

		return View::make('Admin.AssignmentOverride')->with(array(
			'a' => $assignment,
			'errors' => $errors
		));
	}

	/**
	 *
	 */
	public function addOverrideAction($id)
	{
		// Get information about the assignment.
		$assignment = GradingSystem::getAssignment($id);

		// Validate the assignment.
		if(!$assignment)
			return 404;

		// Validate the CSRF token.
		if(!CSRF::check($this->request->post['_csrf']))
			return 400;

		// Process
		$errors = array();
		// TODO

		// If there are errors, reshow the form.
		if(count($errors) > 0)
			return $this->addOverride($id, $errors);

		// Otherwise, redirect the user back to the assignment view.
		return Redirect::to([$this, 'view'], $id);
	}

	/**
	 * Handles deleting a grader override from an assignment.
	 */
	public function deleteOverride($id)
	{
		$assignment = GradingSystem::getAssignment($id);

		if(!$assignment)
			return 404;

		if(!CSRF::check($this->request->post['_csrf']))
			return 400;

		if(!ctype_digit($this->request->post['sid']))
			return 400;

		// Delete the override.
		GradingSystem::dropOverride($id, $this->request->post['sid']);

		// Redirect to assignment view.
		return Redirect::to([$this, 'view'], $id);
	}
}