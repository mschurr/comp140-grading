<?php


class TeachingAssistantController extends Controller
{
	/**
	 * Returns a paginated list of assignments for which the teaching assistant is eligible to issue grades.
	 */
	public function showAssignments()
	{
		try {
			$paginator = GradingSystem::getAssignments($this->user->id);

			$page = 0;
			if(isset($this->request->get['page'])) {
				if(!ctype_digit($this->request->get['page']))
					return 404;
				$page = ((int) $this->request->get['page']) - 1;
			}

			return View::make('TeachingAssistant.AssignmentList')->with(array(
				'assignments' => $paginator[$page],
				'pages' => count($paginator),
				'page' => $page + 1,
				'pageUrl' => function($page) {
					return URL::to(array($this, 'showAssignments'))->with(array('page' => $page));
				}
			));
		} catch (BadAccessException $e) {
			return 404;
		}
	}

	/**
	 * Displays an individual assignment and all students.
	 */
	public function showAssignmentAll($id, &$errors = null, $saved = false) {
		return $this->showAssignment($id, $errors, $saved, true);
	}

	/**
	 * Displays an individual assignment and students for grading.
	 */
	public function showAssignment($id, &$errors = null, $saved = false, $all = false)
	{
		$assignment = GradingSystem::getAssignment($id);

		if(!$assignment)
			return 404;

		$students = $all ?
				GradingSystem::getAllStudentsInSection($assignment['section']) :
				GradingSystem::getStudents($id, $this->user->id);
		$grades = GradingSystem::getGrades($id);
		$post = $this->request->post;

		if(len($students) === 0)
			return 403;

		return View::make('TeachingAssistant.Assignment')->with(array(
			'a' => $assignment,
			'students' => $students,
			'saved' => $saved,
			'grade' => new ImmutableDefaultArrayMap(function($key) use (&$grades, &$post){
					if(isset($post['g'.$key]))
						return $post['g'.$key];
					if(isset($grades[$key]))
						return $grades[$key];
					return -1;
				}),
			'errors' => new ImmutableDefaultArrayMap(null, $errors),
			'all' => $all,
			'action' => URL::to(array($this, ($all ? 'gradeAssignmentAll' : 'gradeAssignment')), $assignment['id'])
		));
	}


	/**
	 * Handles grading of an individual assignment for all students.
	 */
	public function gradeAssignmentAll($id) {
		return $this->gradeAssignment($id, true);
	}

	/**
	 * Handles grading of an individual assignment.
	 */
	public function gradeAssignment($id, $all = false)
	{
		$assignment = GradingSystem::getAssignment($id);

		if(!$assignment)
			return 404;

		$students = $all ?
				GradingSystem::getAllStudentsInSection($assignment['section']) :
				GradingSystem::getStudents($id, $this->user->id);
		$grades = GradingSystem::getGrades($id);
		$errors = array();

		if(len($students) === 0)
			return 403;

		if(!CSRF::check('grade', $this->request->post['_csrf']))
			return 400;

		foreach($students as $student) {
			$sid = $student['id'];
			if(strlen($this->request->post['g'.$sid]) > 0
			&& !GradingSystem::validateGrade($this->request->post['g'.$sid]))
				$errors['g'.$sid] = "You must enter a valid grade.";
			if(strlen($this->request->post['g'.$sid]) === 0
			&& isset($grades[$sid]))
				$errors['g'.$sid] = "You must enter a valid grade.";
		}

		if(count($errors) == 0) {
			foreach($students as $student) {
				$sid = $student['id'];
				if(strlen($this->request->post['g'.$sid]) > 0)
					GradingSystem::setGrade($id, $sid, $this->request->post['g'.$sid]);
			}
		}

		return $this->showAssignment($id, $errors, (count($errors) == 0), $all);
	}
}
