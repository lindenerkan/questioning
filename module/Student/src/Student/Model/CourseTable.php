<?php
namespace Student\Model;
use Zend\Db\TableGateway\TableGateway;

class CourseTable
{

    protected $tableGateway;

    public function __construct (TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getCourses ()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function getCourse($courseId)
    {
        $result = $this->tableGateway->select(array('id'=>$courseId))->current();
        return $result;
    }
    
}
