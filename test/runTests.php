#!/usr/bin/env php
<?php
chdir(__DIR__);

require '../vendor/autoload.php';

$testClasses = [
	'SeanMorris\Form\Test\FormTest'
];

$return = 0;

foreach($testClasses as $testClass)
{
	$test = new $testClass;

	if(!$test->run(new \TextReporter()))
	{
		$return = 1;
	}
}

exit($return);
