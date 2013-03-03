<?php 
// module/Recolement/Module.php
namespace Recolement;

// Add this import statement:
use Recolement\Model\RecolementTable;

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
    					'Recolement\Model\RecolementTable' =>  function($sm) {
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$table     = new RecolementTable($dbAdapter);
    						return $table;
    					}
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