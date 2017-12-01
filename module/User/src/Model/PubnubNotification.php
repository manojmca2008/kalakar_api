<?php

namespace User\Model;

use MCommons\Model\AbstractModel;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGatewayInterface;

class PubnubNotification extends AbstractModel {

    public $id;
    public $user_id;
    public $notification_msg;
    public $type;
    public $read_status;
    public $channel;
    public $created_on;
    public $status;
    
    protected $_tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        parent::__construct($tableGateway);
        $this->_tableGateway = $tableGateway;
    }

    public function getNotifications($userId) {
        $select = new Select ();
        $select->from($this->_tableGateway->getTable());
        $select->columns(array(
            '*'
        ));
        $select->where(array(
            'user_id' => $userId,
        ));
        $notifications = $this->_tableGateway->selectWith($select)->toArray();
        return $notifications;
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
            return true;
        } else {
            return false;
        }
    }
}
