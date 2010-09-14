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
        $this->_values[$value->getOffset()] = $value;
    }

    public function hasValueAtOffset($offset){
        return array_key_exists(strtoupper($offset), $this->_values);
    }

    public function getValue($offset){
        return $this->_values[strtoupper($offset)];
    }

    public function getKey(){
        return $this->_key;
    }
    
    public function getDescription(){
    	return $this->_description;
    }
    
    public function getListEntry(){
    	return $this->_listEntry;
    }
}