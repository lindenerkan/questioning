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
    protected $api;
    
    public function __construct (TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->api="c890d0436c8066c2a57e3d47904e1e20";
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
        $this->jotFormAPI = new JotForm($this->api);
        $name=$quiz->name;
        $quiz = array(
        		'questions' => array(),
        		'properties' => array(
        				'title' => $name,
        				'height' => '800',
        		        'sendpostdata' => 'Yes',
        		        'activeRedirect' => 'thankurl',
        		        'thankurl' => "http://82.196.1.215/public/student/student/getsubmission",
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
        $this->jotFormAPI = new JotForm($this->api);
         
        //$formID="32621905483353";
        $submissions = $this->jotFormAPI->getFormSubmissions($formID);
        
        $questions = $this->jotFormAPI->getFormQuestions($formID);
        
        
        $result=array();
        foreach ($questions as $question)
        {
            if($question['type']!='control_button' && $question['type']!='control_pagebreak' && $question['type']!='control_fileupload' && $question['type']!='control_collapse' && $question['type']!='control_text' && $question['type']!='control_hidden' && $question['type']!='control_autoincrement' && $question['type']!='control_captcha' && $question['type']!='control_image' && $question['type']!='control_head')
            {
                //print_r($question);
                $result[$question['qid']]['type']=$question['type'];
                $result[$question['qid']]['text']=$question['text'];
                
                if($question['type']=='control_radio' ||$question['type']=='control_dropdown')
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
                else
                {
                	$result[$question['qid']]['values']=array();
                
                	foreach ($submissions as $submission)
                	{
                	    if(isset($submission['answers'][$question['qid']]['prettyFormat']))
						$result[$question['qid']]['values'][]=$submission['answers'][$question['qid']]['prettyFormat'];
                	    else 
                	        $result[$question['qid']]['values'][]=$submission['answers'][$question['qid']]['answer'];
                	}
                }
               /* else if($question['type']=="control_fullname" || $question['type']=="control_phone" || $question['type']=="control_datetime" || $question['type']=="control_time")
                {
                    $result[$question['qid']]['values']=array();
                    foreach ($submissions as $submission)
                    {
            			$result[$question['qid']]['values'][]=$submission['answers'][$question['qid']]['answer'];
                    }   
                }*/
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
