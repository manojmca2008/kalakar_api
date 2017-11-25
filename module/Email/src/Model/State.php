<?php

namespace User\Model;

use MCommons\Model\AbstractModel;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGatewayInterface;

class State extends AbstractModel {

    public $id;
    public $country_id;
    public $state;
    public $state_code;
    public $zone;
    public $status;
    protected $_tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        parent::__construct($tableGateway);
        $this->_tableGateway = $tableGateway;
    }

    public function getStates() {
        $select = new Select ();
        $select->from($this->_tableGateway->getTable());
        $select->columns(array(
            'id',
            'state',
            'state_code',
            'zone',
            'status'
        ));
        $select->where(array(
            'states.status' => 1,
        ));
        $stateDetail = $this->_tableGateway->selectWith($select)->toArray();
        return $stateDetail;
    }
}
