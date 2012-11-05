<?php 

namespace Depot;

// module/Inventaire/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Depot\Controller\Depot' => 'Depot\Controller\DepotController'
        ),
    ),

    'router' => array(
        'routes' => array(
            'depot' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/depot[/:action[/:id][/annee/:annee][/page/:page]]',
                    'constraints'  => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]+',
                    	'page'     => '[0-9]+',
                    	'annee'    => '[0-9]+'
                     ),
                    'defaults' => array(
                        'controller' => 'Depot\Controller\Depot',
                        'action'     => 'index',
                        'page' 		 => 1
                    ),
                ),
             ),
        ),
    ),
    
    'translator' => array(
    		'translation_file_patterns' => array(
    				array(
    						'type'     => 'gettext',
    						'base_dir' => __DIR__ . '/../language',
    						'pattern'  => '%s.mo',
    						'text_domain' => __NAMESPACE__,
    				),
    		),
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'depot' => __DIR__ . '/../view',
        ),
    ),
);
?>