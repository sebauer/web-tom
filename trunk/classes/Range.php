<?php
class Range {
    private $_start;
    private $_end;

    /**
     * @param string $start
     * @param string $end
     * @return void
     */
    public function __construct($start, $end){
        $this->_start = $start;
        $this->_end = $end;
    }

    /**
     * @return string
     */
    public function getStart(){
        return $this->_start;
    }

    /**
     * @return string
     */
    public function getEnd(){
        return $this->_end;
    }
}