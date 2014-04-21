<?php

class GradingSystemException extends Exception {}
class GradeFormatException extends GradingSystemException {}

/**
 * Interface for interacting with the grading system at a high level.
 */
class GradingSystem
{
	/**
	 * Implements the Facade; maps static function calls to singleton instance method calls.
	 */
	protected static /*GradingSystem*/ $singleton;
	public static /*mixed*/ function __callStatic(/*string*/ $name, /*array<mixed>*/ $args)
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
	protected /*DatabaseChunkIterator*/ function getAssignments(/*int*/ $userid)
	{
		$statement = "SELECT * FROM `assignments` WHERE `section` IN (
						SELECT DISTINCT `section` FROM `students` WHERE `id` IN (
							SELECT `studentid` FROM `graders_override` WHERE `userid` = :userid AND `assignmentid` = `assignments`.`id`
							UNION
							SELECT DISTINCT `studentid` FROM `graders` WHERE `userid` = :userid AND `studentid` NOT IN (
		                		SELECT DISTINCT `studentid` FROM `graders_override` WHERE `assignmentid` = `assignments`.`id`
		             		)
		          		)
					) ORDER BY `year` DESC, `month` DESC, `day` DESC;";

		return new DatabaseChunkIterator($statement, array(
			':userid' => $userid
		), 50);
	}

	/**
	 * Returns all assignments.
	 */
	protected /*DatabaseChunkIterator*/ function getAllAssignments()
	{
		return new DatabaseChunkIterator("SELECT * FROM `assignments` ORDER BY `year` DESC, `month` DESC, `day` DESC;", array(), 50);
	}

	/**
	 * Returns information about a given assignment.
	 */
	protected /*array<string,mixed>*/ function getAssignment(/*int*/ $aid)
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
			SELECT `studentid`, `netid`, `last_name`, `first_name` FROM `graders_override` JOIN `students` ON `students`.`id` = `studentid` WHERE `userid` = :userid AND `assignmentid` = :aid
			UNION
			SELECT `studentid`, `netid`, `last_name`, `first_name` FROM `graders` JOIN `students` ON `students`.`id` = `studentid` WHERE `userid` = :userid AND `studentid` NOT IN (SELECT `studentid` FROM `graders_override` WHERE `assignmentid` = :aid);
		")->execute(array(
			':aid' => $aid,
			':userid' => $userid
		));

		$students = array();

		foreach($query as $row)
			$students[$row['studentid']] = $row;

		return $students;
	}

	/**
	 * Returns all grades assigned to students for a particular assignment.
	 */
	protected /*array<int, int>*/ function getGrades(/*int*/ $aid)
	{
		$query = $this->db->prepare("
			SELECT * FROM `grades` WHERE `assignmentid` = :aid;
		")->execute(array(
			':aid' => $aid
		));

		$grades = array();

		foreach($query as $row)
			$grades[$row['studentid']] = $row['grade'];

		return $grades;
	}

	/**
	 * Writes a grade into the system for the given student and assignment.
	 */
	protected /*void*/ function setGrade(/*int*/ $aid, /*int*/ $sid, /*int*/ $grade)
	{
		if(!$this->validateGrade($grade))
			throw new GradeFormatException();

		$query = $this->db->prepare("
			REPLACE INTO `grades` (
				`assignmentid`,
				`studentid`,
				`grade`
			) VALUES (
				:aid,
				:sid,
				:grade
			);
		")->execute(array(
			':aid' => $aid,
			':sid' => $sid,
			':grade' => $grade
		));
	}

	/**
	 * Returns whether or not a grade is valid.
	 */
	protected /*bool*/ function validateGrade(/*mixed*/ $grade)
	{
		if(is_string($grade)) {
			if(!ctype_digit($grade))
				return false;
			$grade = intval($grade);
		}

		return is_integer($grade);
	}

	/**
	 * Creates a new assignment.
	 */
	protected /*int*/ function addAssignment(/*int*/ $m, /*int*/ $d, /*int*/ $y, /*string*/ $description, /*int*/ $section)
	{
		return $this->updateAssignment(null, $m, $d, $y, $description, $section);
	}

	/**
	 * Updates an existing assignment.
	 */
	protected /*int*/ function updateAssignment(/*int*/ $aid, /*int*/ $m, /*int*/ $d, /*int*/ $y, /*string*/ $description, /*int*/ $section)
	{
		$data = array(
			'month' => $m,
			'day' => $d,
			'year' => $y,
			'description' => $description,
			'section' => $section
		);

		if($aid === null) {
			$stmt = $this->db->prepare("INSERT INTO `assignments` (".sql_keys($data).") VALUES (".sql_values($data).");");
			$query = $stmt->execute(sql_parameters($data));
			return $query->insertId;
		} else {
			$data['id'] = $aid;
			$stmt = $this->db->prepare("REPLACE INTO `assignments` (".sql_keys($data).") VALUES (".sql_values($data).");");
			$query = $stmt->execute(sql_parameters($data));
			return $aid;
		}
	}

	/**
	 * Drops an existing assignment (if it exists).
	 */
	protected /*int*/ function dropAssignment(/*int*/ $aid)
	{
		$query = $this->db->prepare("DELETE FROM `assignments` WHERE `id` = ?;")->execute($aid);
		return $query->affected;
	}

	/**
	 * Ensures that a local user record exists for the provided network id.
	 * A record may not exist if the user has not yet logged into the system using CAS.
	 */
	protected /*User_Provider*/ function enforceExistence(/*string*/ $netid)
	{
		// Attempt to get the user record.
		$service = App::getUserService();
		$user = $service->loadByName($netid);

		// If we have not yet seen this user, we need to make a database record.
		if($user === null) {
			$stmt = $this->db->prepare("INSERT INTO `users` (`username`) VALUES (?);");
			$res = $stmt->execute($netid);
			$user = $service->load($res->insertId);
		}

		return $user;
	}

	/**
	 * Adds a grader to the system.
	 */
	protected /*void*/ function addGrader(/*string*/ $netid)
	{
		$user = $this->enforceExistence($netid);
		$user->addPrivilege(Privilege::TeachingAssistant);
	}

	/**
	 * Adds an instructor to the system.
	 */
	protected /*void*/ function addInstructor(/*string*/ $netid)
	{
		$user = $this->enforceExistence($netid);
		$user->addPrivilege(Privilege::TeachingAssistant);
		$user->addPrivilege(Privilege::Instructor);
	}

	/**
	 * Revokes an authorized user's access to the system (regardless of whether they are a grader or instructor).
	 * Revoking access does not unassign the grader to students.
	 */
	protected /*void*/ function revokeAccess(/*string*/ $netid)
	{
		$user = $this->enforceExistence($netid);
		$user->removePrivilege(Privilege::TeachingAssistant);
		$user->removePrivilege(Privilege::Instructor);
	}
}