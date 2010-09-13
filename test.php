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
//var_dump($files);
foreach($files as $file){
    if(strpos($file, '.2PF')===false)continue;
    $mapIni = parse_ini_file(_DIRECTORY.$file, true, INI_SCANNER_RAW);

    $map = new Map(str_replace('.2PF', '', $file));
?>
    <?=$map->getAbbreviation()?>: <select name="setting[<?=$map->getAbbreviation()?>]">
<?php

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
?>
                <option value="<?=$sectionName?>"><?=$section[_LISTENTRY]?></option>
<?php
                foreach($section as $key => $value){
                    if($key == _DESCRIPTION || $key == _LISTENTRY) continue;

                    $value = new Value($key, $value);
                    $setting->addValue($value);
                }

                $map->addSetting($setting);
                break;
        }
    }
?>
    </select><br />
<?php
}

?>