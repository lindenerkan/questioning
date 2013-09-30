<?php
namespace Student\Model;
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
    
    public function getLessonQuestions($lessonId)
    {
        $resultset=$this->tableGateway->select(array('course_section_lesson_id'=>$lessonId));
        return $resultset;
    }
    
    public function isActive($id)
    {
        if($this->tableGateway->select(array('id'=>$id))->current()->is_active)
            return 1;
        else 
            return 0;
    }
    
    public function askQuestion($data,$studentId,$name)
    {
        $set=array(
            'student_id' => $studentId,
            'course_section_lesson_id' => $data['course_section_lesson_id'],
            'value' => $data['value'],
            'is_active' =>'1',
            'name'=>$name
        );
        $this->tableGateway->insert($set);
        
        return $this->tableGateway->select($set)->current()->id;
    }
}
