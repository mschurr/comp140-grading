<?php

class TeachingAssistantController extends Controller
{
	/**
	 * Returns a list of assignments for which the teaching assistant is eligible to issue grades.
	 */
	public function showAssignments()
	{
		return View::make('TeachingAssistant.AssignmentList')->with(array(
			'assignments' => GradingSystem::getAssignments($this->user->id)
		));
	}

	/**
	 * Displays an individual assignment and students for grading.
	 */
	public function showAssignment($id)
	{
		$assignment = GradingSystem::getAssignment($id);
		$students = GradingSystem::getStudents($id, $this->user->id);

		if(!$assignment)
			return 404;

		if(len($students) === 0)
			return 403;

		return View::make('TeachingAssistant.Assignment')->with(array(
			'a' => $assignment,
			'students' => $students
		));
	}

	/**
	 * Handles grading of an individual assignment.
	 */
	public function gradeAssignment($id)
	{
		return $this->showAssignment($id);
	}
}