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
import('GradingSystemImporter');

/**
 * Imports initial information into the grading system at the start of the year.
 * Usage: import <students|graders|instructors|assignments> <file>
 *
 * All files are JSON.
 *
 * File format for graders and instructors is:
 *     { "netid" : {"name" : "Name"}, ... }
 *
 * File format for grader map is:
 *     { "student_netid" : "grader_netid", ... }
 *
 * File format for students is:
 *     [{"netid" : "", "email" : "", "last_name" : "", "first_name" : "", "section" : #, "table" : #}, ...]
 *
 * All other information should be managed through the application itself.
 */
CLIApplication::listen('import', function($args) {
	$usage = "Usage: import <students|graders|instructors|assignments> <file>\n";
	$importer = new GradingSystemImporter();

	if (count($args) < 3) {
		fprintf(STDOUT, $usage);
		return 0;
	}

	switch ($args[1]) {
		case 'students':
			$importer->importStudentsFromFile($args[2]);
			break;
		case 'graders':
			$importer->importGradersFromFile($args[2]);
			break;
		case 'instructors':
			$importer->importInstructorsFromFile($args[2]);
			break;
		case 'assignments':
			$importer->importGraderAssignmentsFromFile($args[2]);
			break;
		default:
			fprintf(STDOUT, $usage);
			break;
	}

	return 0;
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

/**
 * Revokes a user's access to the grading system.
 * Usage: php server.php revoke <netid>
 */
CLIApplication::listen('revoke', function($args) {
	if (count($args) < 2) {
		fprintf(STDOUT, "Usage: revoke <netid> - Revokes a user's access to the grading system.\n");
		return 0;
	}

	GradingSystem::revokeAccess($args[1]);
	fprintf(STDOUT, "Done.\n");
	return 0;
});

/**
 * Grants a user access to the grading system.
 * Usage: php server.php grant <grader|instructor> <netid> [<name>]
 */
CLIApplication::listen('grant', function($args) {
	$usage = "php server.php grant <grader|instructor> <netid> [<name>]\n";

	if (count($args) < 3) {
		fprintf(STDOUT, $usage);
		return 0;
	}

	// Ensure the user exists in the system.
	$user = GradingSystem::enforceExistence($args[2]);

	// Grant the user the appropriate permissions.
	if ($args[1] == 'grader') {
		GradingSystem::addGrader($args[2]);
	} else if ($args[1] == 'instructor') {
		GradingSystem::addInstructor($args[2]);
	} else {
		fprintf(STDOUT, $usage);
		return 0;
	}

	// Optional: Update the user's real name.
	if (isset($args[3])) {
		$user->setProperty('name', $args[3]);
	}

	fprintf(STDOUT, "Done.\n");
});
