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
 * Usage: php server.php init <instructors_file> <graders_file> <grader_map_file> <students_file>
 *
 * <instructors_file> - json file containing instructors
 * <graders_file> - json file containing graders
 * <grader_map_file> - json file assigning graders to students
 * <students_file> - json file containing students
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
		fprintf(STDOUT, "Usage: init <instructors_file> <graders_file> <grader_map_file> <students_file> - Imports initial information into the grading system.\r\n");
		return 1;
	}

	$timer = new Timer();
	$instructors = null;
	$graders = null;
	$grader_map = null;
	$students = null;

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

	fprintf(STDOUT, "Found %d instructor records.\r\n", count($instructors));
	fprintf(STDOUT, "Found %d grader records.\r\n", count($graders));
	fprintf(STDOUT, "Found %d student records.\r\n", count($students));
	fprintf(STDOUT, "Found %d assignment records.\r\n", count($grader_map));
	fprintf(STDOUT, "Importing information into database...\r\n");

	try {
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
	} catch (DatabaseException $e) {
		fprintf(STDOUT, "An error occured inserting records.\n".
			            "The specific error returned was:\n".
			            $e->getMessage()."\n");

		if(strpos($e->getMessage(), "Duplicate entry") !== false) {
			fprintf(STDOUT, "\nTo fix duplicate key errors, you may need to run the 'clean' command before running 'init'.\n");
		}

		return 1;
	}

	fprintf(STDOUT, "Finished importing initial data in ".$timer->reap()."ms.\r\n");
});

/**
 * Cleaner
 * Un-does any changes made by the init command so that the init command can be run again.
 * Usage: php server.php clean
 */
CLIApplication::listen('clean', function($args) {
	fprintf(STDOUT, "Warning: This command will completely wipe database tables.\n"
		           ."This action cannot be reversed.\n"
		           ."Proceed? (y/n)\n");
	$confirm = null;
	fscanf(STDIN, "%s", $confirm);

	if($confirm !== "y") {
		return;
	}

	$db = App::getDatabase();
	$db->prepare("DELETE FROM `students`;")->execute();
	$db->prepare("DELETE FROM `users`;")->execute();
	$db->prepare("DELETE FROM `user_privileges`;")->execute();
	$db->prepare("DELETE FROM `graders`;")->execute();
	$db->prepare("DELETE FROM `assignments`;")->execute();
	$db->prepare("DELETE FROM `grades`;")->execute();
	$db->prepare("DELETE FROM `graders_override`;")->execute();
	fprintf(STDOUT, "The database has been reset.\n");
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
		fprintf(STDOUT, "Error: specified path is not writeable.\r\n");
		return -1;
	}

	fprintf(STDOUT, "Grade Export Starting...\r\n");

	// TODO(rixner): You will need to fill this section in once you know the format you need.
	// (TODO) Initialize internal data structures...

	// Iterate through all assignments...
	foreach(GradingSystem::getAllAssignments() as $assignment) {
		// Iterate through all of the grades for the assignment...
		foreach(GradingSystem::getGrades($assignment['id']) 
				as $studentid => $grade) {
			// Grab information about the student (in case it's needed to export).
			$student = GradingSystem::getStudent($studentid);

			// (TODO) Update state in internal data structures...
		}
	}

	// (TODO) Flush internal data structures to file...
	// $file->append(...)

	fprintf(STDOUT, "Saved: %s\r\n", $file->canonicalPath);
	fprintf(STDOUT, "Grade Export Completed.\r\n");
	return 0;
});