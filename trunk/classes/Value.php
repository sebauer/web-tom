<?php
class Value {
    public $_key = '';
    public $_value = '';

    public function __construct($key, $value){
        $this->_key = $key;
        $this->_value = $value;
    }
}