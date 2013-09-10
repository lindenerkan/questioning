<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Student\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Student\Model\Student;
use Student\Model\StudentTable;

class StudentController extends AbstractActionController
{
protected function identity ()
    {
        $locale = $this->getEvent()
            ->getRouteMatch()
            ->getParam('locale', 'tr-TR');
        \Locale::setDefault($locale);
        $loc = $this->getServiceLocator();
        $translator = $loc->get('translator');
        $translator->addTranslationFile("phparray", './module/Instructor/language/lang.array.' . str_replace('-', '_', $locale) . '.php');
        
        $loc->get('ViewHelperManager')
            ->get('translate')
            ->setTranslator($translator);
        //$this->zfcUserAuthentication()->getIdentity()->getEmail();
        if (! $this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login', array(
                'locale' => \Locale::getDefault($locale)
            ));
        }
    }

    public function indexAction ()
    {
        $this->identity();
    	echo $this->zfcUserAuthentication()->getIdentity()->getID();
        return array(
        );
    }
    
    public function courseAction()
    {
        
    }
}
