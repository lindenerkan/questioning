<?php
namespace Instructor\Model;
use Zend\Db\TableGateway\TableGateway;

class StudentQuestionTable
{

    protected $tableGateway;

    public function __construct (TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll ()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function isActive($id)
    {
        if($this->tableGateway->select(array('id'=>$id))->current()->is_active)
            return 1;
        else 
            return 0;
    }
    
    public function getActiveQuestions($lesson_id)
    {
        $resultSet = $this->tableGateway->select(array('course_section_lesson_id'=>$lesson_id, 'is_active'=>'1'));
        return $resultSet;
    }
    
    public function getPassiveQuestions($lesson_id)
    {
    	$resultSet = $this->tableGateway->select(array('course_section_lesson_id'=>$lesson_id, 'is_active'=>'0'));
    	return $resultSet;
    }
    
    public function questionRespond($id)
    {
        $this->tableGateway->update(array('is_active'=>'0'),array('id'=>$id));
    }
    
}
