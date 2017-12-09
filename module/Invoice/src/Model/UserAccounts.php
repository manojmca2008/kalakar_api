<?php

namespace Invoice\Model;

use MCommons\Model\AbstractModel;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGatewayInterface;

class UserAccounts extends AbstractModel {

    public $id;
    public $user_name;
    public $first_name;
    public $last_name;
    public $created_at;
    public $update_at;
    public $status;
    protected $_tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        parent::__construct($tableGateway);
        $this->_tableGateway = $tableGateway;
    }

    public function getUserAccounts($userId) {
        $select = new Select ();
        $select->from($this->_tableGateway->getTable());
        $select->columns(array(
            'id',
            'userId',
            'accountName'
        ));
        $select->where(array(
            'status' => 1,
            'userId' =>$userId,
        ));
        $usersList = $this->_tableGateway->selectWith($select)->toArray();
        return $usersList;
    }
    public function getUserAccountDetails($id) {
        $select = new Select ();
        $select->from($this->_tableGateway->getTable());
        $select->columns(array(
            '*'
        ));
        $select->where(array(
            'status' => 1,
            'id' =>$id,
        ));
        //$select->order('id DESC');
        $accountDetails = $this->_tableGateway->selectWith($select)->current();
        return $accountDetails;
    }
    public function getUserAccountByLastSeclect($id) {
        $select = new Select ();
        $select->from($this->_tableGateway->getTable());
        $select->columns(array(
            '*'
        ));
        $select->where(array(
            'status' => 1,
            'id' =>$id,
        ));
        //$select->order('id DESC');
        $accountDetails = $this->_tableGateway->selectWith($select)->current();
        return $accountDetails;
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
