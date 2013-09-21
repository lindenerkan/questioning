<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
defined('BASE_PATH') || define('BASE_PATH', realpath(dirname(__FILE__)));

chdir(dirname(__DIR__));


switch($_SERVER['HTTP_HOST']) {
	case 'localhost':
	case 'localhost:10088':
		$_SERVER['APPLICATION_ENV'] = 'local';
		ini_set('display_errors', true);
		break;
	case '82.196.1.215':
		$_SERVER['APPLICATION_ENV'] = 'production';
		break;
}


date_default_timezone_set('Europe/Istanbul');
// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
