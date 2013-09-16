<?php
namespace Instructor\Model;
use Zend\Db\TableGateway\TableGateway;

class CourseSectionLessonTable
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
    
    public function getLessons($course_section_id)
    {
        $resultSet = $this->tableGateway->select(array('course_section_id'=>$course_section_id));
        return $resultSet;
    }
    
    public function addLesson($sectionId)
    {
    	$result=array(
    			'course_section_id'=>$sectionId
    	);
    	$this->tableGateway->insert($result);
    }
    
    public function deleteLesson($lessonId)
    {
        $this->tableGateway->delete(array('id' => $lessonId));
    }
    
    public function deleteLessonsWithSectionId($sectionId)
    {
    	$this->tableGateway->delete(array('course_section_id' => $sectionId));
    }
}
