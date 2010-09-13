<?php

class Map {
    private $_abbreviation;
    private $_settings = array();
    private $_ranges = array();

    public function __construct($abbreviation) {
        $this->_abbreviation = $abbreviation;
    }

    public function addSetting(Setting $setting){
        $this->_settings[$setting->getKey()] =  $setting;
    }

    public function getSetting($key){
        return $this->_settings[$key];
    }

    public function hasSetting($key){
        return array_key_exists($key, $this->_settings);
    }

    public function getSettingKeys(){
        return array_keys($this->_settings);
    }

    public function addRange(Range $range){
        array_push($this->_ranges, $range);
    }

    public function getAbbreviation(){
        return $this->_abbreviation;
    }

    public function getRanges(){
        return $this->_ranges;
    }
}

?>