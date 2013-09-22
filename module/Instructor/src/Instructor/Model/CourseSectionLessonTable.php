<?php
namespace Instructor\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

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
    
    public function fetchAllx ()
    {
    	$resultSet = $this->tableGateway->select(function(Select $select) {
    	    $select->q;
    	});
    	return $resultSet;
    }
    
    public function isActive($id)
    {
        if($this->tableGateway->select(array('id'=>$id))->current()->is_active)
            return 1;
        else 
            return 0;
    }
    
    public function getLessons($course_section_id)
    {
        $resultSet = $this->tableGateway->select(array('course_section_id'=>$course_section_id));
        return $resultSet;
    }
    
    
    public function startLesson($lessonId)
    {
        if($this->tableGateway->update(array('is_active'=>'1'),array('id'=>$lessonId)))
            return 1;
        else
            return 0;
    }
    
    public function endLesson($lessonId)
    {
    	if($this->tableGateway->update(array('is_active'=>'0'),array('id'=>$lessonId)))
    		return 1;
    	else
    		return 0;
    }
    
    public function addLesson($data)
    {
    	$result=array(
    			'course_section_id'=>$data['course_section_id'],
    	        'name'=>$data['name']
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
