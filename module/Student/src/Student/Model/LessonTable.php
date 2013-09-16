<?php
namespace Student\Model;
use Zend\Db\TableGateway\TableGateway;

class LessonTable
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
    
    public function getSectionLessons($section_id)
    {
        $resultSet = $this->tableGateway->select(array('course_section_id'=>$section_id));
        return $resultSet;
    }
    
}
