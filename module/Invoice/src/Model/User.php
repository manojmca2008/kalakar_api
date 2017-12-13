<?php

namespace Invoice\Model;

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
        $usersList = $this->_tableGateway->selectWith($select)->toArray();
        return $usersList;
    }
    public function getOtp($otp) {
        $select = new Select ();
        $select->from($this->_tableGateway->getTable());
        $select->columns(array(
            'phone',
            'email',
            'firstName'
        ));
        $select->where(array(
            'otp' => $otp,
        ));
        $usersList = $this->_tableGateway->selectWith($select)->current();
        return $usersList;
    }
    public function getUserDetails($email) {
        $select = new Select ();
        $select->from($this->_tableGateway->getTable());
        $select->columns(array(
            '*'
        ));
        $select->where(array(
            'status' => 1,
            'email'=>$email
        ));
        $userDetails = $this->_tableGateway->selectWith($select)->current();
        return $userDetails;
    }

    public function checkUser($email) {
        $select = new Select ();
        $select->from($this->_tableGateway->getTable());
        $select->columns(array(
            'status',
            'id',
            'firstName'
        ));
        $select->where(array(
            'email' => $email,
        ));
        $userDetail = $this->_tableGateway->selectWith($select)->current();
        return $userDetail;
    }
    
    public function checkUserIdentity($identity) {
        $select = new Select ();
        $select->from($this->_tableGateway->getTable());
        $select->columns(array(
            'checkUserIdentity',
        ));
        $select->where(array(
            'forgetPassIdentity' => $identity,
        ));
        $userDetail = $this->_tableGateway->selectWith($select)->current();
        return $userDetail;
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
