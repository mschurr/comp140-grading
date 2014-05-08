<?php
/**
 * You can write your own command line utilities here.
 * Usage: php server.php <command> <...args>
 */

// Must make sure config.php is properly configured before running this script.
import('GradingSystem');

CLIApplication::listen('init', function($args){
	// List of instructor network IDs to info
	// Instructors are also considered graders.
	$instructors = [
		"mas20" => array("name" => "Matthew Schurr")
	];

	// Map of grader network IDs to info.
	$graders = [

	];

	// Map assigning grader network IDs to student network IDs.
	// Represents default assignment of graders to students (if not overridden).
	$grader_map = array(
		// "grader_netid" => "student_netid"
	);

	// List of student information.
	$students = [
		// array('netid' => '', 'email' => '', 'last_name' => '', 'first_name' => '', section => #, table => #)
	];

	// Addition of assignments and grader overrides for individual assignments
	//  should be done through the graphical user interface.

	// ----------------------------------------------
	// No need to edit below here.
	// ----------------------------------------------

	// Students
	$sid_map = array();

	foreach($students as $data) {
		// todo
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
});

CLIApplication::listen('seed', function($args){
	// Create 100 dummy assignments.

	// Create 100 dummy students.
});

CLIApplication::listen('export', function($args){
	if(count($args) < 2) {
		fprintf(STDOUT, "Usage: export <file> - Exports grades into a CSV file with the provided name.\r\n");
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