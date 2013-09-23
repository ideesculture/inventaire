<?php 

namespace Inventaire;

// module/Inventaire/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Inventaire\Controller\Inventaire' => 'Inventaire\Controller\InventaireController',
            'Inventaire\Controller\Photo' => 'Inventaire\Controller\PhotoController',
        	'Inventaire\Controller\Search'=> 'Inventaire\Controller\SearchController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'inventaire' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/inventaire[/:action[/:id][/brouillon/:brouillon][/annee/:annee][/page/:page]]',
                    'constraints'  => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]+',
                    	'page'     => '[0-9]+',
                    	'annee'    => '[0-9]+',
                    	'brouillon' => '[0-1]'
                    ),
                    'defaults' => array(
                        'controller' => 'Inventaire\Controller\Inventaire',
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
                        'controller' => 'Inventaire\Controller\Search',
                        'action'     => 'index',
                        'page' 		 => 1
                    ),
                ),
             ),
             'photo' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/photo[/:action][/:inventaire_id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'inventaire_id'     => '[0-9]+',
                     ),
                    'defaults' => array(
                        'controller' => 'Inventaire\Controller\Photo',
                        'action'     => 'add',
                    ),
                ),
            ),
             'inventaire_photo' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/inventaire_photo[/:action][/:inventaire_id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'inventaire_id'     => '[0-9]+',
                     ),
                    'defaults' => array(
                        'controller' => 'Inventaire\Controller\Photo',
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
            'inventaire' => __DIR__ . '/../view',
        ),
    ),
);
?>