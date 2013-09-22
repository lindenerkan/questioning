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
    
    public function getQuiz($id)
    {
        $result = $this->tableGateway->select(array('id'=>$id))->current();
        return  $result;
    }
    
    public function startQuiz($id)
    {
        $this->tableGateway->update(array('is_active'=>'1'),array('id'=>$id));
    }
    
    public function endQuiz($id)
    {
    	$this->tableGateway->update(array('is_active'=>NULL),array('id'=>$id));
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
    
    public function report($formID)
    {
        $this->jotFormAPI = new JotForm("f5387ebc3c885e25c7a115bc949533e9");
         
        //$formID="32621905483353";
        $submissions = $this->jotFormAPI->getFormSubmissions($formID);
        
        $questions = $this->jotFormAPI->getFormQuestions($formID);
        
        
        $result=array();
        foreach ($questions as $question)
        {
            if($question['type']!='control_button')
            {
                //print_r($question);
                $result[$question['qid']]['type']=$question['type'];
                $result[$question['qid']]['text']=$question['text'];
                
                if($question['type']=='control_radio')
                {
                    $result[$question['qid']]['options']=(explode('|', $question['options']));
                    $result[$question['qid']]['values']=array();
                    
                    foreach ($result[$question['qid']]['options'] as $optionkey=>$option)
                    {
                        $result[$question['qid']]['values'][$option]=0;
                    }
                    
                    foreach ($submissions as $submission)
                    {
                        foreach ($result[$question['qid']]['options'] as $optionkey=>$option)
                        {
                            if($option==$submission['answers'][$question['qid']]['answer'])
                            {
                                $result[$question['qid']]['values'][$option]++;
                            }
                        }
                    }
                }
                else if($question['type']=="control_checkbox")
                {
                    $result[$question['qid']]['options']=(explode('|', $question['options']));
                    $result[$question['qid']]['values']=array();
                    
                    foreach ($result[$question['qid']]['options'] as $optionkey=>$option)
                    {
                    	$result[$question['qid']]['values'][$option]=0;
                    }
                    
                    foreach ($submissions as $submission)
                    {
                    	foreach ($result[$question['qid']]['options'] as $optionkey=>$option)
                    	{
                    	    if(isset($submission['answers'][$question['qid']]['answer']))
                    	    {
                    	        foreach ($submission['answers'][$question['qid']]['answer'] as $answer)
                    	        {
                    	        	if($option==$answer)
                    	        	{
                    	        		$result[$question['qid']]['values'][$option]++;
                    	        	}
                    	        }
                    	    }
                    	}
                    }
                }

            }
        }
        /*
        foreach ($result as $r)
        {
            print_r($r);
            echo "<br><br>";
        }
        */
        return $result;
        
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
