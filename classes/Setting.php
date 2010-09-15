<?php

class Setting {
    private $_key;
    private $_description;
    private $_listEntry;
    private $_values = array();

    /**
     * @param string $key
     * @param string $description
     * @param string $listEntry
     * @return void
     */
    public function __construct($key, $description, $listEntry){
        $this->_key = $key;
        $this->_listEntry = $listEntry;
        $this->_description = $description;
    }

    /**
     * @param Value $value
     * @return void
     */
    public function addValue(Value $value){
        $this->_values[$value->getOffset()] = $value;
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function hasValueAtOffset($offset){
        return array_key_exists(strtoupper($offset), $this->_values);
    }

    /**
     * @param string $offset
     * @return Value
     */
    public function getValue($offset){
        return $this->_values[strtoupper($offset)];
    }

    /**
     * @return Value[]
     */
    public function getValues(){
        return $this->_values;
    }

    /**
     * @return string
     */
    public function getKey(){
        return $this->_key;
    }

    /**
     * @return string
     */
    public function getDescription(){
    	return $this->_description;
    }

    /**
     * @return string
     */
    public function getListEntry(){
    	return $this->_listEntry;
    }
}