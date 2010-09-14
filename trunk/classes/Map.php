<?php

class Map {
    private $_abbreviation;
    private $_settings = array();
    private $_ranges = array();

    /**
     * @param string $abbreviation
     * @return void
     */
    public function __construct($abbreviation) {
        $this->_abbreviation = $abbreviation;
    }

    /**
     * @param Setting $setting
     * @return void
     */
    public function addSetting(Setting $setting){
        $this->_settings[$setting->getKey()] =  $setting;
    }

    /**
     * @param string $key
     * @return Setting
     */
    public function getSetting($key){
        return $this->_settings[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasSetting($key){
        return array_key_exists($key, $this->_settings);
    }

    /**
     * @return array
     */
    public function getSettingKeys(){
        return array_keys($this->_settings);
    }

    /**
     * @param Range $range
     * @return void
     */
    public function addRange(Range $range){
        array_push($this->_ranges, $range);
    }

    /**
     * @return string
     */
    public function getAbbreviation(){
        return $this->_abbreviation;
    }

    /**
     * @return Range[]
     */
    public function getRanges(){
        return $this->_ranges;
    }
}

?>