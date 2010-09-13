<?php
class Setting {
    private $_key;
    private $_description;
    private $_listEntry;
    private $_values = array();

    public function __construct($key, $description, $listEntry){
        $this->_key = $key;
        $this->_listEntry = $listEntry;
        $this->_description = $description;
    }

    public function addValue(Value $value){
        array_push($this->_values, $value);
    }
}