<?php

class Modification {
    private $_map;
    private $_setting;

    /**
     * @param Map $map
     * @param Setting $setting
     * @return void
     */
    public function __construct(Map $map, Setting $setting){
        $this->_map = $map;
        $this->_setting = $setting;
    }

    /**
     * @return Map
     */
    public function getMap(){
        return $this->_map;
    }

    /**
     * @return Setting
     */
    public function getSetting(){
        return $this->_setting;
    }
}