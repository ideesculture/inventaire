<?php 
// config/application.config.php:
return array(
    'modules' => array(
    	'ZfcBase',
    	'ZfcUser',
    	'Application',
        'Inventaire',
        'Editions',
    	'DOMPDFModule',
    	'DluTwBootstrap',
    	'Depot'
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
        ),
    ),
);
?>
