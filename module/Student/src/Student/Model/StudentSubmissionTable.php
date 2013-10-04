<?php
namespace Student\Model;

use Zend\Db\TableGateway\TableGateway;

class StudentSubmissionTable
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
    
    public function addSub($subID,$formID,$studentID,$ip)
    {
        $set=array(
        		'subID' => $subID,
        		'formID' => $formID,
        		'student_id'=>$studentID,
                'ip'=>$ip
        );
        $this->tableGateway->insert($set);
    }

    
}
