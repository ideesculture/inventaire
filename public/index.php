<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

$chronometre = microtime();
$chronometre = explode(' ', $chronometre);
$chronometre = $chronometre[1] + $chronometre[0];
$start = $chronometre;

chdir(dirname(__DIR__));

// Setup autoloading
include 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(include 'config/application.config.php')->run();

$chronometre = microtime();
$chronometre = explode(' ', $chronometre);
$chronometre = $chronometre[1] + $chronometre[0];
$finish = $chronometre;
$total_time = round(($finish - $start), 4);
echo '<!-- Page generated in '.$total_time.' seconds. -->';