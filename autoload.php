<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if(!function_exists('env')) {
	function env($key, $default = null)
	{
		$value = $_ENV[$key];

		if ($value === false) {
			return $default;
		}

		return $value;
	}
}