<?php

namespace User\Model;

use MCommons\Model\AbstractModel;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGatewayInterface;

class User extends AbstractModel {

    public $id;
    public $user_name;
    public $first_name;
    public $last_name;
    public $email;
    public $password;
    public $mobile;
    public $phone;
    public $display_pic_url;
    public $user_source;
    public $created_at;
    public $update_at;
    public $status;
    protected $_tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        parent::__construct($tableGateway);
        $this->_tableGateway = $tableGateway;
    }

    public function getUsers() {
        $select = new Select ();
        $select->from($this->_tableGateway->getTable());
        $select->columns(array(
            '*'
        ));
        $select->where(array(
            'status' => 1,
        ));
        $userDetail = $this->_tableGateway->selectWith($select)->toArray();
        return $userDetail;
    }

    public function checkUser($email) {
        $select = new Select ();
        $select->from($this->_tableGateway->getTable());
        $select->columns(array(
            'status',
            'id'
        ));
        $select->where(array(
            'email' => $email,
        ));
        $userDetail = $this->_tableGateway->selectWith($select)->current();
        return $userDetail;
    }
    
    public function getUserDetails($id) {
        $select = new Select ();
        $select->from($this->_tableGateway->getTable());
        $select->columns(array(
            '*'
        ));
        $select->where(array(
            'status' => 1,
            'id' =>$id,
        ));
        $usersList = $this->_tableGateway->selectWith($select)->toArray();
        return $usersList;
    }

    public function update($data) {
        $writeGateway = $this->_tableGateway;
        $rowsAffected = $writeGateway->update($data, array('id' => $this->id));
        if ($rowsAffected) {
            return true;
        } else {
            return false;
        }
    }
    public function insert($data) {
        $writeGateway = $this->_tableGateway;
        $rowsAffected = $writeGateway->insert($data);
        if ($rowsAffected) {
            $this->id = $this->_tableGateway->lastInsertValue;
            return true;
        } else {
            return false;
        }
    }
}
