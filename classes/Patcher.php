<?php
class Patcher {

    private $_originalFile;
    private $_patchedFile;
    private $_modifications = array( );

    /**
     * @param Modification $mod
     * @return void
     */
    public function addModification(Modification $mod){
        array_push($this->_modifications, $mod);
    }

    /**
     * @param string $sourceFilePath
     * @param string $originalFilePath
     * @return void
     */
    public function createTunedFile($sourceFilePath, $originalFilePath){

    	// Check if the original file matches the proper one
    	if(md5_file($originalFilePath)!='2e63a949d99dc19f62c36b43cb28d94e'){
    		throw new Exception('MD5 Checksum of original file incorrect!');
    	}

    	// Open the original file read only
        $this->_originalFile = fopen($originalFilePath, 'r');

        // Create a temporary output file and open it in binary write mode
        // We'll create a copy of the source file which will then be modified
        $targetFileName = 'generated/'.basename($sourceFilePath);
        copy($sourceFilePath, $targetFileName);
        $this->_patchedFile = fopen($targetFileName, 'r+b');

        // Check if there are any modifications to be done
        if(count($this->_modifications) == 0){
            throw new Exception('No modifications given');
        }

        // Now apply all modifications to the copy of our source file
        foreach($this->_modifications as $mod){
            $this->applyModification($mod);
        }

        // And close..
        fclose($this->_originalFile);
        fclose($this->_patchedFile);

        return $targetFileName;
    }

    /**
     * @return number
     */
    public function getNumberOfModifications(){
        return count($this->_modifications);
    }

    /**
     * @param Modification $mod
     * @return void
     */
    private function applyModification(Modification $mod){

    	// First get the map for this modification
        $map = $mod->getMap();
        // Get the selected setting
        $setting = $mod->getSetting();

        // Get all ranges and iterate through them
        $ranges = $map->getRanges();
        foreach($ranges as $range){

        	// Define start and end of the range
            $rangeStart = hexdec($range->getStart());
            $rangeEnd = hexdec($range->getEnd());

            // Iterate through every single position within the range
            for($offset = $rangeStart; $offset <= $rangeEnd; $offset++){

            	// Hex represantation of our decimal offset
                $hexOffset = str_pad(dechex($offset), 5, '0', STR_PAD_LEFT);

                // Seek to offset position
                fseek($this->_patchedFile, $offset);

                // Check if the setting contains a value for this position
                if($setting->hasValueAtOffset($hexOffset)){
                	$value = $setting->getValue($hexOffset);
                    fwrite($this->_patchedFile, pack('H*', $value->getValue()));
                    continue;
                }
                // Setting does not contain a value for this position, so
                // we'll write the value of the original file from this position
                fseek($this->_originalFile, $offset);
                fwrite($this->_patchedFile, fread($this->_originalFile, 1));
            }
        }
    }
}