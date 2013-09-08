<?php 
// module/Depot/Module.php
namespace Depot;

// Add this import statement:
use Depot\Model\DepotTable;
use Depot\Model\PhotoTable;


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
    					'Depot\Model\DepotTable' =>  function($sm) {
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$table     = new DepotTable($dbAdapter);
    						return $table;
    					},
    					'Depot\Model\PhotoTable' =>  function($sm) {
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