<?php
namespace Instructor\Model;
use Zend\Db\TableGateway\TableGateway;


class CourseSectionTable
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
    
    public function getSections($courseId,$instructor_id)
    {
        $resultSet = $this->tableGateway->select(array('course_id'=>$courseId,'instructor_id'=>$instructor_id));
        return $resultSet;
    }
    
    public function getSectionsWithInstructorId($instructor_id)
    {
        $resultSet = $this->tableGateway->select(array('instructor_id'=>$instructor_id));
        return $resultSet;
    }
        
    public function getID()
    {
        
    }
    
    public function addCourseSection($courseId,$instructorId)
    {
        $result=array(
            'course_id'=>$courseId,
            'instructor_id'=>$instructorId
        );
        $this->tableGateway->insert($result);
    }
}
