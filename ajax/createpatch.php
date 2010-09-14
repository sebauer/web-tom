<style>
body {
color: white;
}
</style>
<?php
echo "<pre>";
var_dump($_POST);
echo "</pre>";
require_once('../includes/bootstrap.php');
chdir('..');

function jsCallback($message, $isError = 'false'){
    echo "<script>window.parent.uploadCallback(\"{$message}\", {$isError});</script>";
}

function getAvailableMaps(){
    $files = scandir(_DIRECTORY);
    $maps = array( );
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
        $maps[$map->getAbbreviation()] = $map;
    }
    return $maps;
}

$originalFile = _ORIGINALUPLOAD.'tmp'.md5($sourceFilePath).date('YmdHi').'.bin';
$sourceFile = _SOURCEUPLOAD.'tmp'.md5($sourceFilePath).date('YmdHi').'.bin';

if(!array_key_exists('original', $_FILES)){
    jsCallback('Es wurde keine Originaldatei mit hochgeladen!', true);
    die();
}

if(!move_uploaded_file($_FILES['original']['tmp_name'], $originalFile) || !move_uploaded_file($_FILES['source']['tmp_name'], $sourceFile)){
    jsCallback('Dateiuploads konnten nicht verarbeitet werden!', true);
    die();
}

$maps = getAvailableMaps();
$patcher = new Patcher();

foreach($_POST['setting'] as $mapName => $settingVal){
    if($settingVal == $mapName.'0' && !array_key_exists('overwriteOriginal', $_POST)){
        continue;
    }
    $mod = new Modification($maps[$mapName], $maps[$mapName]->getSetting($settingVal));
    $patcher->addModification($mod);
}
if($patcher->getNumberOfModifications() == 0){
    jsCallback('Keine Änderungen vorgenommen!');
    die();
}
$patcher->createTunedFile($sourceFile, $originalFile);
?>