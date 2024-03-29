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
    ob_start();
    $mapIni = parse_ini_string(preg_replace("/\=(.+)\r/", "=\"$1\"\r", file_get_contents(_DIRECTORY.$file)), true);
    $output = ob_get_clean();
    ob_end_flush();
    
    if(!$mapIni){
    	echo '<span style="color: red;">Failed loading '.$file.': '.$output.'</span><br />';
    	continue;
    }

    $map = new Map(str_replace('.2PF', '', $file));
?>
    <?=$mapIni[$map->getAbbreviation().'0'][_DESCRIPTION]?>: <select name="setting[<?=$map->getAbbreviation()?>]">
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