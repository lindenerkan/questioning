<?php
namespace Instructor\Model;
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
    
    public function getSections($sectionId)
    {
    	$resultSet = $this->tableGateway->select(array('course_section_id'=>$sectionId));
    	return $resultSet;
    }
    
    public function activateStudent($id)
    {
        $result =array('is_active' => '1');
        $this->tableGateway->update($result, array('id' => $id));
        return $this->tableGateway->select(array('id'=>$id))->current()->course_section_id;
    }
    
    public function deactivateStudent($id)
    {
    	$result =array('is_active' => '');
    	$this->tableGateway->update($result, array('id' => $id));
    	return $this->tableGateway->select(array('id'=>$id))->current()->course_section_id;
    }
    
    public function deleteStudentsSection($sectionID)
    {
        $this->tableGateway->delete(array('course_section_id'=>$sectionID));
    }
   
}
