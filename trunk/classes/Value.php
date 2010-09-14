<?php
class Value {
    private $_offset = '';
    private $_value = '';

    /**
     * @param string $offset
     * @param string $value
     * @return unknown_type
     */
    public function __construct($offset, $value){
        $this->_offset = $offset;
        $this->_value = $value;
    }

    /**
     * @return string
     */
    public function getOffset(){
        return $this->_offset;
    }

    /**
     * @return string
     */
    public function getValue(){
        return $this->_value;
    }
}