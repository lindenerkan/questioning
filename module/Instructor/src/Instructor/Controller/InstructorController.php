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
use Instructor\Model\StudentQuestion;
use Instructor\Model\StudentQuestionTable;
use Instructor\Model\StudentSection;
use Instructor\Model\StudentSectionTable;
use Instructor\Model\Course;
use Instructor\Model\CourseTable;
use Instructor\Model\Quiz;
use Instructor\Model\QuizTable;
use Instructor\Model\CourseSection;
use Instructor\Model\CourseSectionTable;
use Instructor\Form;
use Instructor\Model\CourseSectionLesson;
use Instructor\Model\JotForm;

class InstructorController extends AbstractActionController
{
    protected $courseTable;
    
    protected $course_sectionTable;
    
    protected $course_section_lessonTable;
    
    protected $studentTable;
    
    protected $quizTable;
    
    protected $studentSectionTable;
    
    protected $studentquestionTable;
    
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
    	    $result[$key]['id']=$course->id;
    	    $result[$key]['code']=$course->code;
    	    $result[$key]['name']=$course->name;
    	    $result[$key]['sections']=array();
    	    $sections=$this->getCourseSectionTable()->getSections($course->id,$instructorId);
    	    foreach ($sections as $s=>$section)
    	    {
    	        if($section->instructor_id==$instructorId)
    	        {	 
    	        	$result[$key]['sections'][$s]['section_id']=$section->id;
    	        	$result[$key]['sections'][$s]['section_name']=$section->name;
    	        	$result[$key]['sections'][$s]['lessons']=array();
    	        	
    	        	$lessons=$this->getCourseSectionLessonTable()->getLessons($section->id);
    	        	
    	        	foreach ($lessons as $l=>$lesson)
    	        	{
    	        	    $result[$key]['sections'][$s]['lessons'][$l]['id']=$lesson->id;
    	        	    $result[$key]['sections'][$s]['lessons'][$l]['lesson_name']=$lesson->name;
    	        	    $result[$key]['sections'][$s]['lessons'][$l]['is_active']=$lesson->is_active;
    	        	}
    	        	
    	        }
    	    }
    	}
    	
    	
        return array(
            'courses' =>$result
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
    	            $result[$key]['sections'][$k]['lessons'][$i]['name']=$lesson->name;
    	        }
    	        
    	    }   
        }
        
        $formSection = new Form\CreateSectionForm('createsection-form');
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
        		if(isset($data['course_id']))
        		{
        		    $instructorId=$this->zfcUserAuthentication()->getIdentity()->getId();
        		    $this->getCourseSectionTable()->addCourseSection($data,$instructorId);
        		    $this->redirect()->toRoute('instructor/panel');
        		}
        	}
        }
        
        
        $formLesson = new Form\CreateLessonForm('createlesson-form');
        if ($this->getRequest()->isPost()) {
        	$lessonName = new CourseSectionLesson();
        	// Postback
        	$data = array_merge_recursive(
        			$this->getRequest()->getPost()->toArray(),
        			$this->getRequest()->getFiles()->toArray()
        	);
        
        	$formLesson->setData($data);
        	if ($formLesson->isValid()) {
        		$data = $formLesson->getData();
        		$lessonName->exchangeArray($data);
        		if(isset($data['course_section_id']))
        		{
        		    $this->getCourseSectionLessonTable()->addLesson($data);
        		    $this->redirect()->toRoute('instructor/panel');
        		}
        	}
        }
        
        
        
        return array(
            'courses'=>$result,
            'formSection' =>$formSection,
            'formLesson' =>$formLesson
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
    
    public function startlessonAction()
    {
        $courseSectionLessonId = (int) $this->params()->fromRoute('id', 0);
        if($this->getCourseSectionLessonTable()->startLesson($courseSectionLessonId))
        {
            $this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'lesson','id'=>$courseSectionLessonId));
        }
        else 
        {
            $this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'index'));
        }
        
    } 
    
    public function lessonAction()
    {
        $courseSectionLessonId = (int) $this->params()->fromRoute('id', 0);
        if($this->getCourseSectionLessonTable()->isActive($courseSectionLessonId))
        {
            
            $active_questions=$this->getStudentQuestionTable()->getActiveQuestions($courseSectionLessonId);
            $passive_questions=$this->getStudentQuestionTable()->getPassiveQuestions($courseSectionLessonId);
            
            
            $myFile = "log.html";
            if(is_file($myFile))
            unlink($myFile);
            
            foreach ($active_questions as $question)
            {
                $text="<div class=\"row span6\">
                <div class=\"alert alert-success\">
                    <a href=\""."http://localhost/OnlineQuestioning/public/index.php/instructor/instructor/questionRespond/".$courseSectionLessonId."/".$question->id."\" class=\"close\" data-dismiss=\"alert\">&times;</a>
                        <h4>".$question->name."</h4>
                            <span>".$question->value."</span>
                                </div></div>";
                $fp = fopen("log.html", 'a');
                fwrite($fp, $text);
                fclose($fp);
            }
            
            
            //<a href=\"\" class=\"close\" data-dismiss=\"alert\">&times;</a>
            foreach ($passive_questions as $question)
            {
            	$text="<div class=\"row span6\">
                <div class=\"alert alert-warning\">
                        <h4>".$question->name."</h4>
                            <span>".$question->value."</span>
                                </div></div>";
            	$fp = fopen("log.html", 'a');
            	fwrite($fp, $text);
            	fclose($fp);
            }
            //$this->url('instructor/default',array('controller'=>'instructor','action' => 'questionRespond','id'=>$courseSectionLessonId,'key'=>$question->id))
            
            
            return array(
                'active_questions' => $active_questions,
                'passive_questions' =>$passive_questions,
                'lessonId' =>$courseSectionLessonId
            );
        }
        else 
        {
            $this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'index'));
        }
    }
    
    public function questionRespondAction()
    {
        $lessonId = (int) $this->params()->fromRoute('id', 0);
        $questionId = (int) $this->params()->fromRoute('key', 0);
        
        $this->getStudentQuestionTable()->questionRespond($questionId);
        
        $this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'lesson','id'=>$lessonId));
    }
    
    public function quizpageAction()
    {
        $lessonId = (int) $this->params()->fromRoute('id', 0);
        $quiz = (int) $this->params()->fromRoute('key', 0);
        $form = (int) $this->params()->fromRoute('form', 0);
        
        
        if($quiz)
        {
            $quiz=$this->getQuizTable()->getQuiz($quiz);
        }
        
        if($form)
        {
            $form_id=$this->getQuizTable()->getQuiz($form)->form_id;
            $report=$this->getQuizTable()->report($form_id);
        }
        else $report=false;
        
        $quizes=$this->getQuizTable()->getLessonQuizes($lessonId);
        
        return array(
            'quizes'     => $quizes,
            'settedQuiz' => $quiz,
            'report'     => $report
        );
    }
    
    public function startQuizAction()
    {
        $quizId = (int) $this->params()->fromRoute('key', 0);
        $lessonId = (int) $this->params()->fromRoute('id', 0);
        
        $quiz=$this->getQuizTable()->getQuiz($quizId)->form_id;
        //$text='<script type="text/javascript" src="http://form.jotformeu.com/jsform/'.$quiz->form_id.'"></script>';
        $text='<iframe id="JotFormIFrame" onload="window.parent.scrollTo(0,0)" allowtransparency="true" src="http://form.jotformeu.com/form/'.$quiz.'" frameborder="0" style="width:100%; height:465px; border:none;" scrolling="no"></iframe>';
        $fp = fopen("quiz.html", 'a');
        fwrite($fp, $text);
        fclose($fp);
        
        
        $this->getQuizTable()->startQuiz($quizId);
        $this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'quizpage','id'=>$lessonId,'key'=>$quizId));
    }

    public function endQuizAction()
    {
    	$quizId = (int) $this->params()->fromRoute('key', 0);
    	$lessonId = (int) $this->params()->fromRoute('id', 0);
    	
    	$myFile = "quiz.html";
    	if(is_file($myFile))
    		unlink($myFile);
    
    	$this->getQuizTable()->endQuiz($quizId);
    	$this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'quizpage','id'=>$lessonId,'key'=>0,'form'=>$quizId));
    }
    
    public function endlessonAction()
    {
        $courseSectionLessonId = (int) $this->params()->fromRoute('id', 0);
        if($this->getCourseSectionLessonTable()->endLesson($courseSectionLessonId))
        {
            $this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'index'));
        }
        else 
        {
            $this->redirect()->toRoute('instructor/default', array('controller'=>'instructor','action' => 'lesson','id'=>$courseSectionLessonId));
        }
        
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
    
    public function getStudentQuestionTable()
    {
    	if (!$this->studentquestionTable) {
    		$sm = $this->getServiceLocator();
    		$this->studentquestionTable = $sm->get('Instructor\Model\StudentQuestionTable');
    	}
    	return $this->studentquestionTable;
    }
}
