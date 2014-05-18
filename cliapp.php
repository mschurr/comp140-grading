<?php
/**
 * You can write your own command line utilities here.
 * Usage: php server.php <command> <...args>
 *
 * You should make sure config.php is properly set before running these scripts.
 */

/**
 * Required Libraries
 */
import('GradingSystem');

/**
 * Initialization
 * Imports initial information into the grading system at the start of the year.
 * Usage: php server.php init <instructors> <graders> <grader_map> <students>
 *
 * <instructors> - json file containing instructors
 * <graders> - json file containing graders
 * <grader_map> - json file assigning graders to students
 * <students> - json file containing students
 *
 * File format for graders and instructors is: 
 *     { "netid" : {"name" : "Name"}, ... }
 *
 * File format for grader map is:
 *     { "grader_netid" : "student_netid", ... }
 *
 * File format for students is:
 *     [{"netid" : "", "email" : "", "last_name" : "", "first_name" : "", "section" : #, "table" : #}, ...]
 *
 * All other information should be managed through the application itself.
 */
CLIApplication::listen('init', function($args) {
	if(count($args) < 5) {
		fprintf(STDOUT, "Usage: init <instructors> <graders> <grader_map> <students> - Imports initial information into the grading system.\r\n");
		return 1;
	}

	$timer = new Timer();

	try {
		// Load instructors and graders from file.
		$instructors = from_json(File::open($args[1])->content);
		$graders = from_json(File::open($args[2])->content);

		// Load grader assignments from file.
		// File is of JSON format: 
		$grader_map = from_json(File::open($args[3])->content);

		// Load students from file.
		$students = from_json(File::open($args[4])->content);

		if($graders === null
		|| $instructors === null
		|| $grader_map === null
		|| $students === null) {
			fprintf(STDOUT, "An error occured parsing one or more data files (make sure the files contain valid JSON).\r\n");
			return 1;
		}
	}
	catch(FileException $e) {
		fprintf(STDOUT, "An error occured reading one or more data files (make sure the files exist).\r\n");
		return 1;
	}

	// Students
	$sid_map = array();

	foreach($students as $data) {
		$sid_map[$data['netid']] = GradingSystem::addStudent(
			$data['netid'],
			$data['email'],
			$data['last_name'],
			$data['first_name'],
			$data['section'],
			$data['table']
		);
	}

	// Instructors and Graders
	$uid_map = array();

	foreach($instructors as $netid => $data) {
		$uid_map[$netid] = GradingSystem::addInstructor($netid);
	}

	foreach($graders as $netid => $data) {
		$uid_map[$netid] = GradingSystem::addGrader($netid);
	}

	// Grader Assignments
	foreach($grader_map as $grader => $student) {
		GradingSystem::assignGrader($uid_map[$grader], $sid_map[$student]);
	}

	fprintf(STDOUT, "Finished importing initial data in ".$timer->reap()."ms.\r\n");
});

/**
 * Grade Exporting
 * Exports grades into the destination file.
 * Usage: php server.php export <file>
 *
 * <file> - location on disk to save results
 * 
 * This command will need to be finished by the instructor... not sure yet about required format.
 */
CLIApplication::listen('export', function($args){
	if(count($args) < 2) {
		fprintf(STDOUT, "Usage: export <file> - Exports grades into a file with the provided name.\r\n");
		return -1;
	}

	$file = File::open($args[1]);

	if($file->exists) {
		fprintf(STDOUT, "Error: specified file already exists.\r\n");
		return -1;
	}

	if(!$file->isWriteable) {
		fprintf(STDOUT, "Error: specified file is not writeable.\r\n");
		return -1;
	}

	fprintf(STDOUT, "Grade Export Starting...\r\n");

	foreach(GradingSystem::getAllAssignments() as $assignment) {
		fprintf(STDOUT, "Exporting Assignment %s\r\n", json_encode($assignment));
		$grades = GradingSystem::getGrades($assignment['id']);

		foreach($grades as $studentid => $grade) {
			$student = GradingSystem::getStudent($studentid);

			// ...code to export grade here.
			// Professor can fill this in, not sure exact format needed.
			// $file->append($string);
		}
	}

	fprintf(STDOUT, "Saved: %s\r\n", $file->canonicalPath);
	fprintf(STDOUT, "Grade Export Completed.\r\n");
	return 0;
});