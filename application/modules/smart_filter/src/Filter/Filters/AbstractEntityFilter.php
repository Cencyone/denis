<?php namespace smart_filter\src\Filter\Filters;

use CI_DB_active_record;
use smart_filter\src\Filter\FilterParameters;

abstract class AbstractEntityFilter
{

    /**
     * @var FilterParameters
     */
    protected $parameters;

    /**
     * @var CI_DB_active_record
     */
    protected $db;

    /**
     * AbstractEntityFilter constructor.
     * @param FilterParameters $parameters
     * @param CI_DB_active_record $db
     */
    public function __construct(FilterParameters $parameters, $db) {
        $this->parameters = $parameters;
        $this->db = $db;
    }

    /**
     * @return array
     */
    abstract public function getValues();

}