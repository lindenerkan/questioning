<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Instructor\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Instructor\Model\Course;
use Instructor\Model\CourseTable;
use Instructor\Model\CourseSection;
use Instructor\Model\CourseSectionTable;
use Instructor\Form;

class InstructorController extends AbstractActionController
{
    protected $courseTable;
    
    protected $course_sectionTable;
    
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
                //'locale' => \Locale::getDefault($locale)
            ));
        }
    }

    public function indexAction ()
    {
        if (! $this->zfcUserAuthentication()->hasIdentity()) {
        	return $this->redirect()->toRoute('zfcuser/login', array(
        	));
        }
    	if (! $this->zfcUserAuthentication()->getIdentity()->getAdmin()) {
    	    return $this->redirect()->toRoute('home', array());
    	}
        return array(
        );
    }
    
    public function panelAction()
    {
        return array(
        );
    }
    
    public function createcourseAction()
    {
        if (! $this->zfcUserAuthentication()->hasIdentity()) {
        	return $this->redirect()->toRoute('zfcuser/login', array(
        	));
        }
        if (! $this->zfcUserAuthentication()->getIdentity()->getAdmin()) {
        	return $this->redirect()->toRoute('home', array());
        }
        
        $form = new Form\CreateCourseForm('createcourse-form');
        if ($this->getRequest()->isPost()) {
        	$course = new Course();
        	// Postback
        	$data = array_merge_recursive(
        			$this->getRequest()->getPost()->toArray(),
        			$this->getRequest()->getFiles()->toArray()
        	);
        
        	$form->setData($data);
        	if ($form->isValid()) {
        		$data = $form->getData();
        		$course->exchangeArray($data);
        		
        		$result=$this->getCourseTable()->addCourse($course);
        		$instructorId=$this->zfcUserAuthentication()->getIdentity()->getId();
        		if($result)
        		    $this->getCourseSectionTable()->addCourseSection($result,$instructorId);
        		
        		$this->redirect()->toRoute('instructor/panel');
        	}
        }
        return array('form' => $form);
    }
    
    public function getCourseTable()
    {
    	if (!$this->courseTable) {
    		$sm = $this->getServiceLocator();
    		$this->courseTable = $sm->get('Instructor\Model\CourseTable');
    	}
    	return $this->courseTable;
    }
    
    public function getCourseSectionTable()
    {
    	if (!$this->course_sectionTable) {
    		$sm = $this->getServiceLocator();
    		$this->course_sectionTable = $sm->get('Instructor\Model\CourseSectionTable');
    	}
    	return $this->course_sectionTable;
    }
}
