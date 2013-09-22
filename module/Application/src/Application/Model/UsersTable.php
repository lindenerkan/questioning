<?php
namespace Application\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\Crypt\Password\Bcrypt;

class UsersTable
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
    
    public function makeAdmin($id)
    {
        $this->tableGateway->update(array('admin'=>"1"), array('id' => $id));
    }
    
    public function makeStudent($id)
    {
    	$this->tableGateway->update(array('admin'=>"0"), array('id' => $id));
    }
    
    public function getRow($id)
    {
        $resultSet = $this->tableGateway->select(array('id' => $id))->current();
        return $resultSet;
    }
    
    public function saveDisplayName($data,$id)
    {
        $result =array('display_name' => $data->display_name);
        $this->tableGateway->update($result, array('id' => $id));
    }
    
    public function savePassword($data,$id)
    {
        $bcrypt = new Bcrypt();
        $data = $bcrypt->create($data->new);
    	$result =array('password' => $data);
        $this->tableGateway->update($result, array('id' => $id));
    }
}
