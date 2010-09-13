<?php
class Range {
    private $_start;
    private $_end;

    public function __construct($start, $end){
        $this->_start = $start;
        $this->_end = $end;
    }
}