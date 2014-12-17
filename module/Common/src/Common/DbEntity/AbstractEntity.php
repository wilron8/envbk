<?php

/**
 * Base entity class to allow policy in filling up entity values and in getting specified field values
 *
 * @author kimsreng
 */

namespace Common\DbEntity;

abstract class AbstractEntity implements EntityInterface {

    /**
     * This is to be implemented by child class as key=>[]
     * for fields to be populated by different scenario
     * 
     * @return array
     */
    protected function inputScenario() {
        return [];
    }

    /**
     * This is to be implemented by child class as key=>[]
     * for fields to be output by different scenario
     * 
     * @return array
     */
    protected function outputScenario() {
        return [];
    }

    /**
     * 
     * @param array $fields
     * @param type $scenario
     */
    public function input(Array $fields, $scenario = '') {
        if (isset($this->inputScenario()[$scenario])) {
            $allowedFields = $this->inputScenario()[$scenario];
            $input = [];
            foreach ($allowedFields as $key) {
                if (isset($fields[$key])) {
                    $input[$key] = $fields[$key];
                }
            }
            $this->fillUp($input);
        } else {
            $this->exchangeArray($fields);
        }
    }

    /**
     * 
     * @param mixed $scenario string as scenario name or just list of fields as array
     * @return array
     */
    public function output($scenario = '') {
        $all_fields = $this->getArrayCopy();
        if (isset($this->outputScenario()[$scenario])) {
            $out = [];
            $fields = $this->outputScenario()[$scenario];
            foreach ($fields as $key) {
                if (isset($all_fields[$key])) {
                    $out[$key] = $all_fields[$key];
                }
            }
            return $out;
        } else {
            return $all_fields;
        }
    }

    /**
     * Fill the class instance with field values
     * 
     * @param array $fields
     */
    protected function fillUp(Array $fields) {
        foreach ($fields as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    
    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
