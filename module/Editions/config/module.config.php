<?php 
// module/Editions/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Editions\Controller\Editions' => 'Editions\Controller\EditionsController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'editions' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/editions[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Editions\Controller\Editions',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'editions' => __DIR__ . '/../view',
        ),
    ),
);
?>