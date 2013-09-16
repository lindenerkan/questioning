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
use Instructor\Model\Student;
use Instructor\Model\StudentTable;
use Instructor\Model\StudentSection;
use Instructor\Model\StudentSectionTable;
use Instructor\Model\Course;
use Instructor\Model\CourseTable;
use Instructor\Model\Quiz;
use Instructor\Model\QuizTable;
use Instructor\Model\CourseSection;
use Instructor\Model\CourseSectionTable;
use Instructor\Form;

class InstructorController extends AbstractActionController
{
    protected $courseTable;
    
    protected $course_sectionTable;
    
    protected $course_section_lessonTable;
    
    protected $studentTable;
    
    protected $quizTable;
    
    protected $studentSectionTable;
    
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
    	
    	$courses=$this->getCourseTable()->getCourses();
    	$instructorId=$this->zfcUserAuthentication()->getIdentity()->getId();
    	
    	$result=array();
    	
    	foreach ($courses as $key=>$course)
    	{
    	    //course -> instructor id yok!!!
    	    if($course->instructor_id===$instructorId)
    	    {
    	        $result[$key]['id']=$course->id;
    	        $result[$key]['code']=$course->code;
    	        $result[$key]['name']=$course->name;
    	        $result[$key]['sections']=array();
    	        $sections=$this->getCourseSectionTable()->getSections($course->id,$instructorId);
    	        foreach ($sections as $s=>$section)
    	        {
    	            $result[$key]['sections'][$s]['section_id']=$section->id;
    	            $result[$key]['sections'][$s]['section_name']=$section->name;
    	        }
    	        
    	    }
    	}
    	
        return array(
        );
    }
    
    public function panelAction()
    {
        $instructorId=$this->zfcUserAuthentication()->getIdentity()->getId();
        
        $result=array(
            
        );
        
        $courses=$this->getCourseTable()->getCourses();
        foreach ($courses as $key=>$course)
        {
            $result[$key]['id']=$course->id;
            $result[$key]['code']=$course->code;
            $result[$key]['name']=$course->name;
            $result[$key]['sections']=array();
            
            $sections=$this->getCourseSectionTable()->getSections($course->id,$instructorId);
    	    foreach ($sections as $k=>$section)
    	    {
    	        $result[$key]['sections'][$k]['id']=$section->id;
    	        $result[$key]['sections'][$k]['name']=$section->name;
    	        $result[$key]['sections'][$k]['lessons']=array();
    	        $lessons=$this->getCourseSectionLessonTable()->getLessons($section->id);
    	        
    	        foreach ($lessons as $i=>$lesson)
    	        {
    	            $result[$key]['sections'][$k]['lessons'][$i]['id']=$lesson->id;
    	        }
    	        
    	    }   
        }
        
        $formSection = new Form\CreatesectionForm('createsection-form');
        if ($this->getRequest()->isPost()) {
        	$sectionName = new CourseSection();
        	// Postback
        	$data = array_merge_recursive(
        			$this->getRequest()->getPost()->toArray(),
        			$this->getRequest()->getFiles()->toArray()
        	);
        
        	$formSection->setData($data);
        	if ($formSection->isValid()) {
        		$data = $formSection->getData();
        		$sectionName->exchangeArray($data);
                print_r($data);
        		$instructorId=$this->zfcUserAuthentication()->getIdentity()->getId();
        		$this->getCourseSectionTable()->addCourseSection($data,$instructorId);
        		$this->redirect()->toRoute('instructor/panel');
        	}
        }
        
        
        
        return array(
            'courses'=>$result,
            'formSection' =>$formSection
        );
    }
    
    public function studentsAction()
    {
        $sectionId = (int) $this->params()->fromRoute('id', 0);
        $sections=$this->getStudentSectionTable()->getSections($sectionId);
        $students=array();
        foreach ($sections as $s=>$section)
        {
            $students[$s]['student']=$this->getStudentTable()->getStudent($section->student_id);
            $students[$s]['section']=$section;
        }
        
        return array(
            'students' => $students
        );
        
    }
    
    public function activatestudentAction()
    {
        $Id = (int) $this->params()->fromRoute('id', 0);
        $sectionId= $this->getStudentSectionTable()->activateStudent($Id);
        $this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'students','id'=>$sectionId));
    }
    
    public function deactivatestudentAction()
    {
    	$Id = (int) $this->params()->fromRoute('id', 0);
    	$sectionId =$this->getStudentSectionTable()->deactivateStudent($Id);
    	$this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'students','id'=>$sectionId));
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
        		//if($result)
        		    //$this->getCourseSectionTable()->addCourseSection($result,$instructorId);
        		
        		$this->redirect()->toRoute('instructor/panel');
        	}
        }
        return array('form' => $form);
    }
    
    public function editcourseAction()
    {
    	if (! $this->zfcUserAuthentication()->hasIdentity()) {
    		return $this->redirect()->toRoute('zfcuser/login', array(
    		));
    	}
    	if (! $this->zfcUserAuthentication()->getIdentity()->getAdmin()) {
    		return $this->redirect()->toRoute('home', array());
    	}
    
    	$courseId = (int) $this->params()->fromRoute('id', 0);
    	$course = $this->getCourseTable()->getCourse($courseId);
    	$e=array(
    	    'id'=>$course->id,
    	    'code'=>$course->code,
    	    'name'=>$course->name,
    	);
    	$form = new Form\EditCourseForm('editcourse-form');
    	$form->setData($e);
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
    			
    			$this->getCourseTable()->updateCourse($course);
    			
    			$this->redirect()->toRoute('instructor/panel');
    		}
    	}
    	return array('form' => $form);
    }
    
    public function quizAction()
    {
        $lessonId = (int) $this->params()->fromRoute('id', 0);
        $quizes=$this->getQuizTable()->getLessonQuizes($lessonId);
        
        
        $form = new Form\CreateQuizForm('createquiz-form');
        if ($this->getRequest()->isPost()) {
        	$quiz = new Quiz();
        	// Postback
        	$data = array_merge_recursive(
        			$this->getRequest()->getPost()->toArray(),
        			$this->getRequest()->getFiles()->toArray()
        	);
        
        	$form->setData($data);
        	if ($form->isValid()) {
        		$data = $form->getData();
        		$quiz->exchangeArray($data);
        
    			$this->getQuizTable()->createQuiz($lessonId,$quiz);
                $this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'quiz','id'=>$lessonId));
        	}
        }
        
        
        
        return array(
            'quizes' => $quizes,
            'lessonId' => $lessonId,
            'form' =>$form
        );
    }
    
    public function createquizAction()
    {
        $lessonId = (int) $this->params()->fromRoute('id', 0);
        $this->getQuizTable()->createQuiz($lessonId);
        $this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'quiz','id'=>$lessonId));
    }
    
    public function deletequizAction()
    {
        $lessonId = (int) $this->params()->fromRoute('key', 0);
    	$quizId = (int) $this->params()->fromRoute('id', 0);
    	$this->getQuizTable()->deleteQuiz($quizId);
    	$this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'quiz','id'=>$lessonId));
    }
    
    public function addsectionAction()
    {
        $courseId = (int) $this->params()->fromRoute('id', 0);
        $instructorId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $this->getCourseSectionTable()->addCourseSection($courseId,$instructorId);
        $this->redirect()->toRoute('instructor/panel');
    }
    
    public function addlessonAction()
    {
    	$coursesectionId = (int) $this->params()->fromRoute('id', 0);
    	$this->getCourseSectionLessonTable()->addLesson($coursesectionId);
    	$this->redirect()->toRoute('instructor/panel');
    }
    
    public function deletelessonAction()
    {
    	$lessonId = (int) $this->params()->fromRoute('id', 0);
    	$this->getQuizTable()->deleteQuizes($lessonId);
    	$this->getCourseSectionLessonTable()->deleteLesson($lessonId);
    	$this->redirect()->toRoute('instructor/panel');
    }
    
    public function deletesectionAction()
    {
    	$sectionId = (int) $this->params()->fromRoute('id', 0);
    	
    	//delete quizes
    	$lessons= $this->getCourseSectionLessonTable()->getLessons($sectionId);
    	foreach ($lessons as $lesson)
    	{
    		$this->getQuizTable()->deleteQuizes($lesson->id);
    	}
    	
    	$this->getCourseSectionLessonTable()->deleteLessonsWithSectionId($sectionId);
    	$this->getStudentSectionTable()->deleteStudentsSection($sectionId);
    	$this->getCourseSectionTable()->deleteSection($sectionId);
    	$this->redirect()->toRoute('instructor/panel');
    }
    
    public function deletecourseAction()
    {
    	$courseId = (int) $this->params()->fromRoute('id', 0);
    	
    	$sections =$this->getCourseSectionTable()->getSectionsWithCourseId($courseId);
    	
    	foreach ($sections as $section)
    	{
    	    //delete quizes
    	    $lessons= $this->getCourseSectionLessonTable()->getLessons($section->id);
    	    foreach ($lessons as $lesson)
    	    {
    	        $this->getQuizTable()->deleteQuizes($lesson->id);
    	    }
    	    
    	    //delete lessons
    	    $this->getCourseSectionLessonTable()->deleteLessonsWithSectionId($section->id);
            
    	    //delete students sections
    	    $this->getStudentSectionTable()->deleteStudentsSection($section->id);
    	    
    	    //delete sections
    	    $this->getCourseSectionTable()->deleteSection($section->id);
    	}

    	//delete course
    	$this->getCourseTable()->deleteCourse($courseId);
    	
    	$this->redirect()->toRoute('instructor/panel');
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
    
    public function getCourseSectionLessonTable()
    {
    	if (!$this->course_section_lessonTable) {
    		$sm = $this->getServiceLocator();
    		$this->course_section_lessonTable = $sm->get('Instructor\Model\CourseSectionLessonTable');
    	}
    	return $this->course_section_lessonTable;
    }
    
    public function getStudentTable()
    {
    	if (!$this->studentTable) {
    		$sm = $this->getServiceLocator();
    		$this->studentTable = $sm->get('Instructor\Model\StudentTable');
    	}
    	return $this->studentTable;
    }
    
    public function getStudentSectionTable()
    {
    	if (!$this->studentSectionTable) {
    		$sm = $this->getServiceLocator();
    		$this->studentSectionTable = $sm->get('Instructor\Model\StudentSectionTable');
    	}
    	return $this->studentSectionTable;
    }
    
    public function getQuizTable()
    {
    	if (!$this->quizTable) {
    		$sm = $this->getServiceLocator();
    		$this->quizTable = $sm->get('Instructor\Model\QuizTable');
    	}
    	return $this->quizTable;
    }
}
