<?php

class GradingSystemException extends Exception {}

class GradingSystem
{
	protected static /*GradingSystem*/ $singleton;

	public static /*Object*/ function __callStatic(/*string*/ $name, /*array<Object>*/ $args)
	{
		if(static::$singleton === null)
			static::$singleton = new GradingSystem();
		return call_user_func_array(array(static::$singleton, $name), $args);
	}

	// ------------------------------------------

	protected /*Database*/ $db;

	protected /*void*/ function __construct()
	{
		$this->db = App::getDatabase();
	}

	/**
	 * Returns an array of assignments that the given $userid has some role in grading sorted by date.
	 */
	protected /*array<array<string,Object>>*/ function getAssignments(/*int*/ $userid)
	{
		$query = $this->db->prepare("
			SELECT * FROM `assignments` WHERE `section` IN (
				SELECT DISTINCT `section` FROM `students` WHERE `id` IN (
					SELECT `studentid` FROM `graders_override` WHERE `userid` = :userid AND `assignmentid` = `assignments`.`id`
					UNION
					SELECT DISTINCT `studentid` FROM `graders` WHERE `userid` = :userid AND `studentid` NOT IN (
                		SELECT DISTINCT `studentid` FROM `graders_override` WHERE `assignmentid` = `assignments`.`id`
             		)
          		)
			) ORDER BY `year` DESC, `month` DESC, `day` DESC;
		")->execute(array(
			':userid' => $userid
		));

		return $query->rows;
	}

	/**
	 * Returns information about a given assignment.
	 */
	protected /*array<string,Object>*/ function getAssignment(/*int*/ $aid)
	{
		$query = $this->db->prepare("
			SELECT * FROM `assignments` WHERE `id` = ?;
		")->execute($aid);

		return len($query) ? $query->row : null;
	}

	/**
	 * Returns the students assigned to the given $userid for grading for assignment $aid.
	 */
	protected /*array<int>*/ function getStudents(/*int*/ $aid, /*int*/ $userid)
	{
		$query = $this->db->prepare("
			SELECT `studentid` FROM `graders_override` WHERE `userid` = :userid AND `assignmentid` = :aid
			UNION
			SELECT `studentid` FROM `graders` WHERE `userid` = :userid AND `studentid` NOT IN (SELECT `studentid` FROM `graders_override` WHERE `assignmentid` = :aid);
		")->execute(array(
			':aid' => $aid,
			':userid' => $userid
		));

		$students = array();

		foreach($query as $row)
			$students[] = $row['studentid'];

		return $students;
	}

	// API Methods
	/*
	setGrade($studentid, $aid, $grade)
	getGrade($studentid, $aid)
	*/
}