<?php
class Patcher {

    private $_sourceFile;
    private $_patchedFile;
    private $_modifications = array( );

    public function addModification(Modification $mod){
        array_push($this->_modifications, $mod);
    }

    public function createTunedFile($sourceFilePath){
        $this->_sourceFile = fopen($sourceFilePath, 'r');

        $targetFileName = 'tmp'.md5($sourceFilePath).date('YmdHi').'.bin';

        copy($sourceFilePath, $targetFileName);
        $this->_patchedFile = fopen($targetFileName, 'r+');

        if(count($this->_modifications) == 0){
            throw new Exception('No modifications given');
        }

        foreach($this->_modifications as $mod){
            $this->applyModification($mod);
        }

        fclose($this->_sourceFile);
        fclose($this->_patchedFile);
    }

    private function applyModification(Modification $mod){
        $map = $mod->getMap();
        var_dump($map->getAbbreviation());
        $setting = $mod->getSetting();

        $ranges = $map->getRanges();

        foreach($ranges as $range){
            $rangeStart = hexdec($range->getStart());
            $rangeEnd = hexdec($range->getEnd());

            for($offset = $rangeStart; $offset <= $rangeEnd; $offset++){

                $hexOffset = dechex($offset);
                fseek($this->_patchedFile, $offset);

                if($setting->hasValueAtOffset($hexOffset)){
                    fwrite($this->_patchedFile, $setting->getValue($hexOffset)->getValue());
                }
                var_dump($hexOffset);
            }
        }
    }
}