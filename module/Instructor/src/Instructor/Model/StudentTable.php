<?php
namespace Instructor\Model;
use Zend\Db\TableGateway\TableGateway;

class StudentTable
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
    
    public function getStudent($id)
    {
        $result = $this->tableGateway->select(array('id'=>$id))->current();
        return $result;
    }
}
