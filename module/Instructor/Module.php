<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Instructor;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Instructor\Model\Course;
use Instructor\Model\CourseTable;
use Instructor\Model\CourseSection;
use Instructor\Model\CourseSectionTable;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Zend\Db\ResultSet\ResultSet;

class Module
{
    public function init (ModuleManager $moduleManager)
    {
    	$moduleManager->getEventManager()->attach('loadModules.pre', function  (\Zend\ModuleManager\ModuleEvent $e)
    	{
    		if (! $this->zfcUserAuthentication()
    		->hasIdentity()) {
    			return $this->redirect()
    			->toRoute('zfcuser/login');
    		}
    	});
    }
    
    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    public function getServiceConfig ()
    {
    	return array(
    			'factories' => array(
    					'Instructor\Model\CourseTable' => function  ($sm)
    					{
    						$tableGateway = $sm->get('CourseTableGateway');
    						$table = new CourseTable($tableGateway);
    						return $table;
    					},
    					'CourseTableGateway' => function  ($sm)
    					{
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$resultSetPrototype = new ResultSet();
    						$resultSetPrototype->setArrayObjectPrototype(new Course());
    						return new TableGateway('Course', $dbAdapter, null, $resultSetPrototype);
    					},
    					'Instructor\Model\CourseSectionTable' => function  ($sm)
    					{
    						$tableGateway = $sm->get('CourseSectionTableGateway');
    						$table = new CourseSectionTable($tableGateway);
    						return $table;
    					},
    					'CourseSectionTableGateway' => function  ($sm)
    					{
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$resultSetPrototype = new ResultSet();
    						$resultSetPrototype->setArrayObjectPrototype(new CourseSection());
    						return new TableGateway('Course_section', $dbAdapter, null, $resultSetPrototype);
    					},
    			)
    	);
    }
}
