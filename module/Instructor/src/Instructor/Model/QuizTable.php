<?php
namespace Instructor\Model;
use Zend\Db\TableGateway\TableGateway;
use Instructor\Model\JotForm;

class Exception
{
	public function __construct($e){
		echo $e;
	}
}

class QuizTable
{

    protected $tableGateway;
    protected $jotFormAPI;
    
    public function __construct (TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function getLessonQuizes($lessonId)
    {
        $resultSet = $this->tableGateway->select(array('course_section_lesson_id'=>$lessonId));
        return $resultSet; 
    }
    
    public function createQuiz($lessonId,$quiz)
    {
        $this->jotFormAPI = new JotForm("f5387ebc3c885e25c7a115bc949533e9");
        $name=$quiz->name;
        $quiz = array(
        		'questions' => array(),
        		'properties' => array(
        				'title' => $name,
        				'height' => '600',
        		),
        );
        $response = $this->jotFormAPI->createForm($quiz);
        if($response)
        {
            $forms = $this->jotFormAPI->getForms(0, 1, null, null);
            $latestForm = $forms[0];
            $latestFormID = $latestForm["id"];
            if($this->addQuiz($lessonId, $latestFormID,$name))
                return $latestFormID;
        }
    }
    
    public function addQuiz($lessonId,$formId,$name)
    {
        $result=array(
        		'course_section_lesson_id'=>$lessonId,
        		'form_id'=>$formId,
                'name'=>$name
        );
        $this->tableGateway->insert($result);
    }
    
    public function deleteQuiz($quizId)
    {
        $this->tableGateway->delete(array('id'=>$quizId));
    }
    
    public function deleteQuizes($lesson_id)
    {
        $this->tableGateway->delete(array('course_section_lesson_id'=>$lesson_id));
    }
    
}
