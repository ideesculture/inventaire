<?php 

namespace Recolement;

// module/Inventaire/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Recolement\Controller\Recolement' => 'Recolement\Controller\RecolementController'
        ),
    ),

    'router' => array(
        'routes' => array(
            'recolement' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/recolement[/:action[/:id]]',
                    'constraints'  => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]+',
                     ),
                    'defaults' => array(
                        'controller' => 'Recolement\Controller\Recolement',
                        'action'     => 'index'
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
            'recolement' => __DIR__ . '/../view',
        ),
    ),
);
?>