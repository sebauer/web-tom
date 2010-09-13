<pre>
</pre>
<?php

require_once('includes/autoload.php');

define('_DIRECTORY', './R60_2005/');

define('_DESCRIPTION', 'Description');
define('_LISTENTRY', 'ListEntry');
define('_RANGES', 'Ranges');
define('_RANGESCOUNT', 'RangesCount');

$files = scandir(_DIRECTORY);
$maps = array( );
//var_dump($files);
foreach($files as $file){
    if(strpos($file, '.2PF')===false)continue;
    $mapIni = parse_ini_file(_DIRECTORY.$file, true, INI_SCANNER_RAW);

    $map = new Map(str_replace('.2PF', '', $file));

    foreach($mapIni as $sectionName => $section){
        switch($sectionName){
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
    $maps[] = $map;
}
$settingKeys = $maps[0]->getSettingKeys();
$mod1 = new Modification($maps[0], $maps[0]->getSetting($settingKeys[1]));

$patcher = new Patcher();
$patcher->addModification($mod1);
echo "<pre>";
$patcher->createTunedFile(_DIRECTORY.'original.rom');
echo "</pre>";