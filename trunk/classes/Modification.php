<?php

class Modification {
    private $_map;
    private $_setting;

    public function __construct(Map $map, Setting $setting){
        $this->_map = $map;
        $this->_setting = $setting;
    }

    public function getMap(){
        return $this->_map;
    }

    public function getSetting(){
        return $this->_setting;
    }
}