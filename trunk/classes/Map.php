<?php

class Map {
    private $_abbreviation;
    private $_settings = array();
    private $_ranges = array();

    public function __construct($abbreviation) {
        $this->_abbreviation = $abbreviation;
    }

    public function addSetting(Setting $setting){
        array_push($this->_settings, $setting);
    }

    public function addRange(Range $range){
        array_push($this->_ranges, $range);
    }

    public function getAbbreviation(){
        return $this->_abbreviation;
    }
}

?>