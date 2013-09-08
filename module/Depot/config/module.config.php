<?php 

namespace Depot;

// module/Depot/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Depot\Controller\Depot' => 'Depot\Controller\DepotController',
            'Depot\Controller\Photo' => 'Depot\Controller\PhotoController',
        	'Depot\Controller\Search'=> 'Depot\Controller\SearchController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'depot' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/depot[/:action[/:id][/brouillon/:brouillon][/annee/:annee][/page/:page]]',
                    'constraints'  => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]+',
                    	'page'     => '[0-9]+',
                    	'annee'    => '[0-9]+',
                    	'brouillon' => '[0-1]'
                    ),
                    'defaults' => array(
                        'controller' => 'Depot\Controller\Depot',
                        'action'     => 'index',
                        'page' 		 => 1,
                        'brouillon'	 => 1
                    ),
                ),
             ),
            'search' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/search[/:action[/:id][/annee/:annee][/page/:page]]',
                    'constraints'  => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]+',
                    	'page'     => '[0-9]+',
                    	'annee'    => '[0-9]+'
                     ),
                    'defaults' => array(
                        'controller' => 'Depot\Controller\Search',
                        'action'     => 'index',
                        'page' 		 => 1
                    ),
                ),
             ),
             'photo' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/photo[/:action][/:depot_id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'depot_id'     => '[0-9]+',
                     ),
                    'defaults' => array(
                        'controller' => 'Depot\Controller\Photo',
                        'action'     => 'add',
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