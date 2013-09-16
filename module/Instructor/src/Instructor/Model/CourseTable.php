<?php
namespace Instructor\Model;
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
    
    public function getCourseID($code,$name)
    {
        $result= $this->tableGateway->select(array('code'=>$code,'name'=>$name))->current()->id;
        return $result;
    }
    
    public function getCourse($id)
    {
    	$result= $this->tableGateway->select(array('id'=>$id))->current();
    	return $result;
    }
    
    public function verifyCourseCode($code)
    {
        $result=$this->tableGateway->select(array('code'=>$code))->current();
    	if(!$result)
            return true;
    }
    
    public function addCourse($data)
    {
        $result=array(
            'code'=>$data->code,
            'name'=>$data->name
        );
        if($this->verifyCourseCode($result['code']))
        {
            if($this->tableGateway->insert($result))
            {
                return $this->getCourseID($result['code'], $result['name']);
            }   	
        }
    }
    
    public function updateCourse($data)
    {
    	$result=array(
    			'code'=>$data->code,
    			'name'=>$data->name
    	);
		$this->tableGateway->update($result,array('id'=>$data->id));
    }
    
    public function deleteCourse($courseId)
    {
    	$this->tableGateway->delete(array('id' => $courseId));
    }
}
