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
use Student\Model\StudentSection;
use Student\Model\StudentTableSection;
use Student\Model\Course;
use Student\Model\CourseTable;
use Student\Model\Lesson;
use Student\Model\LessonTable;
use Student\Model\CourseSection;
use Student\Model\CourseSectionTable;

class StudentController extends AbstractActionController
{
    protected $studentTable;
    
    protected $studentSectionTable;
    
    protected $courseTable;
    
    protected $course_sectionTable;
    
    protected $course_section_lessonTable;
    
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
        $studentId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $sections=$this->getStudentSectionTable()->getStudentSections($studentId);
        $courses=array();    
        foreach ($sections as $s=>$section)
        {
            $courseSection=$this->getCourseSectionTable()->getCourse($section->course_section_id);
            $course=$this->getCourseTable()->getCourse($courseSection->course_id);
            $lessons=$this->getLessonTable()->getSectionLessons($section->course_section_id);
            $isStudentActive=$this->getStudentSectionTable()->getIsStudentActive($section->id);
            
            $courses[$s]=array(
                'course'          => $course,
                'section'         => $courseSection,
                'lessons'         => $lessons,
                'isStudentActive' => $isStudentActive
            );
        }
        
        return array(
            'courses'=>$courses
        );
    }
        
    public function registerlistAction()
    {
        $studentId=$this->zfcUserAuthentication()->getIdentity()->getId();
        
        $courses=$this->getCourseTable()->getCourses();
        
        foreach ($courses as $key=>$course)
        {
            $result[$key]=array(
                'id'       => $course->id,
                'code'     => $course->code,
                'name'     => $course->name,
                'sections' => array()
            );
            $sections=$this->getCourseSectionTable()->getCourseSections($course->id);
            foreach ($sections as $k=>$section)
            {
                $result[$key]['sections'][$k]['id']=$section->id;
                $result[$key]['sections'][$k]['is_registered']=$this->getStudentSectionTable()->getIsStudentRegistered($studentId,$section->id);
            }
        }
        
        
        return array(
            'courses'=>$result
        );
    }
    
    public function registersectionAction()
    {
        $sectionId = (int) $this->params()->fromRoute('id', 0);
        $studentId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $this->getStudentSectionTable()->addStudentSection($studentId,$sectionId);
        $this->redirect()->toRoute('student/default', array('controller'=>'student','action' => 'registerlist'));
        
    }
    
    public function unregistersectionAction()
    {
    	$sectionId = (int) $this->params()->fromRoute('id', 0);
    	$studentId=$this->zfcUserAuthentication()->getIdentity()->getId();
    	$this->getStudentSectionTable()->deleteStudentSection($studentId,$sectionId);
    	$this->redirect()->toRoute('student/default', array('controller'=>'student','action' => 'registerlist'));
    
    }
    
    public function getStudentTable()
    {
    	if (!$this->studentTable) {
    		$sm = $this->getServiceLocator();
    		$this->studentTable = $sm->get('Student\Model\StudentTable');
    	}
    	return $this->courseTable;
    }
    
    public function getStudentSectionTable()
    {
    	if (!$this->studentSectionTable) {
    		$sm = $this->getServiceLocator();
    		$this->studentSectionTable = $sm->get('Student\Model\StudentSectionTable');
    	}
    	return $this->studentSectionTable;
    }
    
    public function getCourseTable()
    {
    	if (!$this->courseTable) {
    		$sm = $this->getServiceLocator();
    		$this->courseTable = $sm->get('Student\Model\CourseTable');
    	}
    	return $this->courseTable;
    }
    
    public function getCourseSectionTable()
    {
    	if (!$this->course_sectionTable) {
    		$sm = $this->getServiceLocator();
    		$this->course_sectionTable = $sm->get('Student\Model\CourseSectionTable');
    	}
    	return $this->course_sectionTable;
    }
    
    public function getLessonTable()
    {
    	if (!$this->course_section_lessonTable) {
    		$sm = $this->getServiceLocator();
    		$this->course_section_lessonTable = $sm->get('Student\Model\LessonTable');
    	}
    	return $this->course_section_lessonTable;
    }
    
}
