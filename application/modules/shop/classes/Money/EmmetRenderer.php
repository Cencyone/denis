<?php
namespace shop\classes\Money;

use artem_c\emmet\Emmet;

class EmmetRenderer
{

    /**
     * @var EmmetRenderer
     */
    protected static $instance;

    protected function __construct() {

    }

    public static function getInstance() {
        return self::$instance ?: self::$instance = new self();
    }

    /**
     * @param $string
     * @param array $variables
     * @return string
     */
    public function render($string, array $variables = []) {
        return (new Emmet($string))->create($variables);
    }

}