<?php
class Patcher {

    private $_originalFile;
    private $_patchedFile;
    private $_modifications = array( );

    public function addModification(Modification $mod){
        array_push($this->_modifications, $mod);
    }

    public function createTunedFile($sourceFilePath, $originalFilePath){
    	if(md5_file($originalFilePath)!='2e63a949d99dc19f62c36b43cb28d94e'){
    		throw new Exception('MD5 Checksum of original file incorrect!');
    	}
        $this->_originalFile = fopen($originalFilePath, 'r');

        $targetFileName = 'tmp'.md5($sourceFilePath).date('YmdHi').'.bin';

        copy($sourceFilePath, $targetFileName);
        $this->_patchedFile = fopen($targetFileName, 'r+b');

        if(count($this->_modifications) == 0){
            throw new Exception('No modifications given');
        }

        foreach($this->_modifications as $mod){
            $this->applyModification($mod);
        }

        fclose($this->_originalFile);
        fclose($this->_patchedFile);
    }

    private function applyModification(Modification $mod){
        $map = $mod->getMap();
        $setting = $mod->getSetting();

        $ranges = $map->getRanges();

        foreach($ranges as $range){
            $rangeStart = hexdec($range->getStart());
            $rangeEnd = hexdec($range->getEnd());

            for($offset = $rangeStart; $offset <= $rangeEnd; $offset++){

                $hexOffset = dechex($offset);
                fseek($this->_patchedFile, $offset);

                if($setting->hasValueAtOffset($hexOffset)){
                	$value = $setting->getValue($hexOffset);
                    fwrite($this->_patchedFile, pack('H*', $setting->getValue($hexOffset)->getValue()));
                    continue;
                }
                fseek($this->_originalFile, $offset);
                fwrite($this->_patchedFile, fread($this->_originalFile, 1));
            }
        }
    }
}