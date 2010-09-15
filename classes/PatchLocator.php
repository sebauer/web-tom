<?php
class PatchLocator {

    /**
     * @param string $model
     * @return Map[]
     */
    public static function getMaps($model){
        return self::getMapsFromDirectory(_PATCHESDIR.$model.DIRECTORY_SEPARATOR);
    }


    /**
     * @param string $model
     * @return StaticMap[]
     */
    public static function getStaticMaps($model){
        return self::getMapsFromDirectory(_STATICPATCHESDIR.$model.DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $model
     * @return array
     */
    public static function getMapsGrouped($model){
        return self::groupMapsArraybyGroup(self::getMapsFromDirectory(_PATCHESDIR.$model.DIRECTORY_SEPARATOR));
    }

    /**
     * Groups the given array of maps by their groups.
     *
     * @param array $maps
     * @return array
     */
    public static function groupMapsArraybyGroup($maps){
        $groups = array( );
        foreach($maps as $key => $map) {
            $groupname = $map->getGroup();
            if($groupname!=''){
                $groups[$groupname][$key] = $map;
                continue;
            }
            $groups['none'][$key] = $map;
        }
        return $groups;
    }

    /**
     * @param string $directory
     * @return Map[]
     */
    private static function getMapsFromDirectory($directory = ''){
        $className = 'StaticMap';
        if(strpos($directory, '/static/')===false){
            $className = 'Map';
        }
        $files = scandir($directory);
        $maps = array( );
        foreach($files as $file){
            if(strpos($file, '.2PF')===false)continue;
            $mapIni = parse_ini_string(preg_replace("/\=(.+)\r/", "=\"$1\"\r", file_get_contents($directory.$file)), true);

            $map = new $className(str_replace('.2PF', '', $file));

            foreach($mapIni as $sectionName => $section){
                switch($sectionName){
                    case _FEATURES:
                        if(array_key_exists(_GROUP, $section)){
                            $map->setGroup($section[_GROUP]);
                        }
                        break;
                    case _RANGES:
                        $rangesCount = $section[_RANGESCOUNT];
                        for($i=1; $i <= $rangesCount; $i++){
                            if(!array_key_exists('Range'.$i.'Start', $section) || !array_key_exists('Range'.$i.'End', $section)) {
                                throw new Exception('RangesCount differs from actual number of ranges');
                            }
                            $range = new Range($section['Range'.$i.'Start'], $section['Range'.$i.'End']);
                            $map->addRange($range);
                        }
                        break;
                    default:
                        $setting = new Setting($sectionName, $section[_DESCRIPTION], $section[_LISTENTRY], array());
                        foreach($section as $key => $value){
                            if($key == _DESCRIPTION || $key == _LISTENTRY) continue;

                            $value = new Value($key, $value);
                            $setting->addValue($value);
                        }

                        $map->addSetting($setting);
                        break;
                }
            }
            $maps[$map->getAbbreviation()] = $map;
        }
        return $maps;
    }
}