<?php

namespace exchange\classes;

/**
 * Class-helper for Products
 * (to simplify product processing)
 *
 * @author kolia
 */
class DataCollector
{

    /**
     *
     * @var array
     */
    protected $currentPassData = [];

    /**
     *
     * @var string|integer
     */
    protected $keys = [];

    /**
     *
     * @var array
     */
    protected $tablesData = [];

    /**
     *
     * @param string $table
     * @param array $data
     * @param string|integer $key array key (optioanl)
     * @return bool
     */
    public function addData($table, array $data, $key = NULL) {

        if (count($data) == 0) {
            return FALSE;
        }
        if (isset($this->currentPassData[$table])) {
            $this->currentPassData[$table] = array_merge($this->currentPassData[$table], $data);
        } else {
            $this->currentPassData[$table] = $data;
        }
        if ($key != NULL) {
            $this->keys[$table] = $key;
        }
        return FALSE;
    }

    /**
     *
     * @param string $tableBName
     * @return array
     */
    public function getData($tableBName = NULL) {

        if ($tableBName == NULL) {
            return $this->tablesData;
        }
        if (array_key_exists($tableBName, $this->tablesData)) {
            return $this->tablesData[$tableBName];
        }
        return [];
    }

    /**
     * Collects data of current pass, getting ready for new pass
     */
    public function newPass() {

        foreach ($this->currentPassData as $tableName => $tableData) {
            if (isset($this->keys[$tableName])) {
                $currentKey = $this->keys[$tableName];
                if (array_key_exists($tableName, $this->tablesData)) {
                    if (array_key_exists($currentKey, $this->tablesData[$tableName])) {
                        $oldData = $this->tablesData[$tableName][$currentKey];
                        $this->tablesData[$tableName][$currentKey] = array_merge($oldData, $tableData);
                    } else {
                        $this->tablesData[$tableName][$currentKey] = $tableData;
                    }
                } else {
                    $this->tablesData[$tableName][$currentKey] = $tableData;
                }
            } else {
                $this->tablesData[$tableName][] = $tableData;
            }
        }
        $this->currentPassData = [];
        $this->keys = [];
    }

    /**
     *
     * @param string $tableBName
     * @return boolean
     */
    public function unsetData($tableBName = NULL) {

        if ($tableBName == NULL) {
            $this->tablesData = [];
            return TRUE;
        }
        if (array_key_exists($tableBName, $this->tablesData)) {
            unset($this->tablesData[$tableBName]);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Similar to addData, but adds rows to existing "product"
     * @param string $table
     * @param array $data
     * @param string $key
     * @return bool
     */
    public function updateData($table, array $data, $key = NULL) {

        if (count($data) == 0) {
            return FALSE;
        }
        $this->currentPassData[$table][] = $data;
        if ($key != FALSE) {
            $this->keys[$table] = $key;
        }
        return FALSE;
    }

}