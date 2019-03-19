<?php

/**
 * SPaymentSystems - class to work with payment systems
 *
 * @package
 * @version $id$
 * @copyright
 * @author <dev@imagecms.net>
 * @license
 */
class SPaymentSystems
{

    public $pathToSystems = null; // Path to payment system classes.

    protected $classes = [];

    protected $order = null;

    public function __construct() {
        $this->pathToSystems = SHOP_DIR . 'classes/PaymentSystems/';
    }

    /**
     * Base init function
     *
     * @param  $order
     * @return void
     */
    public function init($order) {
        $this->setOrder($order);
    }

    /**
     * Load payment system class by name
     *
     * @param string $name
     * @param null|SPaymentMethods $paymentMethod
     * @return string
     */
    public function loadPaymentSystem($name, $paymentMethod = null) {

        if (array_key_exists($name, $this->systems)) {
            // Load class file
            if (!class_exists($name)) {
                include $this->pathToSystems . $this->systems[$name]['filePath'];
            }

            // Create new class
            $class = new $name;

            if ($paymentMethod instanceof SPaymentMethods) {
                $class->setPaymentMethod($paymentMethod);
            }

            return $class;
        } else {
            return 'System not found.';
        }
    }

    /**
     * getList
     *
     * @access public
     * @return array with system names
     */
    public function getList() {
        $ci = &get_instance();
        $methods = $ci->db->like('name', 'payment_method_')
            ->get('components')
            ->result();

        $system = [];
        foreach ($methods as $v) {
            $system[$v->name] = [
                                 'filePath' => $v->name,
                                 'listName' => lang(end(explode('_', $v->name)), $v->name), //перевод в каждом модуле свой.
                                 'class'    => null,
                                ];
        }

        return $system;
    }

    /**
     * Set order class. Will be used to work with payment system classes.
     *
     * @param SOrders $order
     * @return void
     */
    public function setOrder(SOrders $order) {
        $this->order = $order;
    }

    /**
     * Get current order
     *
     * @return SOrders class
     */
    public function getOrder() {
        return $this->order;
    }

}