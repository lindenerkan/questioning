<?php
namespace Student\Model;
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
    
    public function getCourseSections($course_id)
    {
        $resultSet=$this->tableGateway->select(array('course_id'=>$course_id));
        return $resultSet;
    }
    
    public function getCourse($id)
    {
        $result=$this->tableGateway->select(array('id'=>$id))->current();
        return $result;
    }

    
}
