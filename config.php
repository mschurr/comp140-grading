<?php

Config::set(array(
	/**
	 * Database Driver Information
	 */
	'database.driver' => 'mysql',
	'database.user' => 'httpd',
	'database.pass' => 'httpd',
	'database.host' => 'localhost',
	'database.port' => '3306',
	'database.name' => 'comp140grading',

	'auth.driver' => 'cas',
	'users.driver' => 'cas',
	'groups.driver' => 'db',

	'auth.driver' => 'cas',
	'auth.cas.host' => 'netid.rice.edu',
	'auth.cas.port' => 443,
	'auth.cas.path' => '/cas',
	'auth.cas.cert' => FILE_ROOT.'/rice-cas.pem',

	/**
	 * Development Mode
	 * 
	 * If development mode is enabled, you will be shown rich error messages. If development mode is not, you will be 
	 *  shown production errors which reveal nothing (although the errors will still be logged).
	 */
	'app.development'	=> true,

	/**
	 * A long, secret key that will be used to verify the integrity of cookies.
	 */
	'cookies.secretKey' => 'qwertyuiop',
));

class GradingConfig {
	public static $section = array(
		1 => 'Section 1 (10:50-12:05 PM TR)',
		2 => 'Section 2 (02:30-03:45 PM TR)',
		3 => 'Section 3 (01:00-02:15 PM TR)'
	);
}

class Privilege {
	const Instructor = 1;
	const TeachingAssistant = 2;
}

class Grades {
	public static $values = array(
		1 => 'Absent',
		2 => 'Late',
		3 => 'Check',
		4 => 'CheckPlus'
	);
	const Absent = 1;
	const Late = 2;
	const Check = 3;
	const CheckPlus = 4;
}

