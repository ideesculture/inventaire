<?php 
// module/Inventaire/Module.php
namespace Inventaire;

// Add this import statement:
use Inventaire\Model\InventaireTable;
use Inventaire\Model\PhotoTable;


class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    // Add this method:
    public function getServiceConfig()
    {
    	return array(
    			'factories' => array(
    					'Inventaire\Model\InventaireTable' =>  function($sm) {
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$table     = new InventaireTable($dbAdapter);
    						return $table;
    					},
    					'Inventaire\Model\PhotoTable' =>  function($sm) {
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$table     = new PhotoTable($dbAdapter);
    						return $table;
    					},
    			),
    	);
    }
    
    public function onBootstrap($e)
    {
    	$translator = $e->getApplication()->getServiceManager()->get('translator');
    	$translator
    	->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))
    	->setFallbackLocale('fr_FR');
    }
    
}
?>