<?php

use Propel\Runtime\ActiveQuery\Criteria;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Class SOrdersModel
 * @deprecated since 4.9
 */
class SOrdersModel
{

    public function __construct() {
        $this->ci = &get_instance();
        $this->db = &$this->ci->db;
        $this->locale = MY_Controller::getCurrentLocale();
    }

    /**
     * @deprecated since 4.9
     */
    public function getOrdersByID($userID, $criteria = Criteria::DESC) {
        return SOrdersQuery::create()
                        ->orderByDateCreated($criteria)
                        ->joinSOrderStatuses()
                        ->filterByUserId($userID)
                        ->find();
    }

    /**
     * @depreceted since 4.9 its a simple query use SOrdersQuery::create()->setComment(__METHOD__)->findOneByKey($orderSecretKey);
     */
    public function getOrdersByKey($orderSecretKey) {
        return SOrdersQuery::create()
                        ->filterByKey($orderSecretKey)
                        ->findOne();
    }

}