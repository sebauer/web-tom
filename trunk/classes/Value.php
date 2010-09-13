<?php
class Value {
    public $_offset = '';
    public $_value = '';

    public function __construct($offset, $value){
        $this->_offset = $offset;
        $this->_value = $value;
    }

    public function getOffset(){
        return $this->_offset;
    }

    public function getValue(){
        return $this->_value;
    }
}