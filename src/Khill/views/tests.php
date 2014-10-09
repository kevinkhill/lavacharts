<?php

// Set these to what you require

// Path to PHPUnit_Html or null if include path
$config['phpunit_html'] = base_path().'/vendor/';

// Path to PHPUnit or null if include path (ie. /usr/local/php/PEAR/PHPUnit/)
$config['phpunit'] = base_path().'/vendor/phpunit/phpunit/';

// Name of template to use
$config['template'] = 'default';

// Test to run (or null to run all tests in current directory)
$config['test'] = null;

// File test class is in (or null)
$config['testFile'] = null;

// Name of bootstrap PHP file to run before the tests
$config['bootstrap'] = null;

// Read configuration from this XML file
$config['configuration'] = null;

// Don't use any default 'phpunit.xml' configuration file if exists
$config['noConfiguration'] = false;

// Write code coverage report in Clover XML to this file
$config['coverageClover'] = null;

// Write code coverage report in HTML format to this directory
$config['coverageHtml'] = (is_dir('./reports') ? './reports' : null);

// Filter which tests to run
$config['filter'] = null;

// Only run tests from the specified group(s)
$config['groups'] = null;

// Exclude tests from the specified group(s)
$config['excludeGroups'] = null;

// Run each test in a separate PHP process
$config['processInsolation'] = null;

// Try to check source files for syntax errors
$config['syntaxCheck'] = false;

// Stop execution upon first error
$config['stopOnError'] = false;

// Stop execution upon first error or failure
$config['stopOnFailure'] = false;

// Stop execution upon first incomplete test
$config['stopOnIncomplete'] = false;

// Stop execution upon first skipped test
$config['stopOnSkipped'] = false;

// Backup and restore $GLOBALS for each test
$config['noGlobalsBackup'] = true;

// Backup and restore static attributes for each test
$config['staticBackup'] = true;

// Mark a test as incomplete if no assertions are made
$config['strict'] = false;


//////////////////////////////////////////////////////////////////////////////

require($config['phpunit_html'].'PHPUnit_Html/src/main.php');

/* vim: set expandtab tabstop=4 shiftwidth=4: */
