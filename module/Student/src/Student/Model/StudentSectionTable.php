<?php
namespace Student\Model;
use Zend\Db\TableGateway\TableGateway;

class StudentSectionTable
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
    
    public function getStudentSections($student_id)
    {
        $resultSet = $this->tableGateway->select(array('student_id'=>$student_id));
        return $resultSet;
    }
    
    public function getStudentActiveSections($student_id)
    {
    	$resultSet = $this->tableGateway->select(array('student_id'=>$student_id,'is_active'=>'1'));
    	return $resultSet;
    }
    
    public function getIsStudentActive($id)
    {
        $result = $this->tableGateway->select(array('id'=>$id))->current()->is_active;
        return $result;
    }
    
    public function getIsStudentRegistered($studentId,$sectionId)
    {
    	$result = $this->tableGateway->select(array('student_id'=>$studentId,'course_section_id'=>$sectionId))->current();
    	if($result)
    	    return $result;
    	else 
    	    return 0;
    	//return $result;
    }
    
    public function addStudentSection($studentId,$sectionId)
    {
    	$result=array(
    			'student_id'=>$studentId,
    			'course_section_id'=>$sectionId
    	);
    	$this->tableGateway->insert($result);
    }
    
    public function deleteStudentSection($studentId,$sectionId)
    {
    	$result=array(
    			'student_id'=>$studentId,
    			'course_section_id'=>$sectionId
    	);
    	$this->tableGateway->delete($result);
    }
   
}
