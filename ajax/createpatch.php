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

function downloadFile($filepath, $filename, $md5){
    echo "<script>window.parent.showDownloadInfo(\"{$filepath}\", \"{$filename}\", \"{$md5}\");</script>";
}

function getAvailableMaps(){
    $files = scandir(_DIRECTORY);
    $maps = array( );
    foreach($files as $file){
        if(strpos($file, '.2PF')===false)continue;
        $mapIni = parse_ini_string(preg_replace("/\=(.+)\r/", "=\"$1\"\r", file_get_contents(_DIRECTORY.$file)), true);

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

if($_FILES['original']['tmp_name'] == ''){
    jsCallback('Es wurde keine Originaldatei mit hochgeladen!', true);
    die();
}

$originalFile = _ORIGINALUPLOAD.'tmp'.md5($_FILES['original']['name'].date('YmdHi')).'.bin';
$sourceFile = _SOURCEUPLOAD.'tmp'.md5($_FILES['original']['name'].date('YmdHi')).'.bin';

if(!move_uploaded_file($_FILES['original']['tmp_name'], $originalFile) || $_FILES['source']['tmp_name'] != '' && !move_uploaded_file($_FILES['source']['tmp_name'], $sourceFile)){
    jsCallback('Dateiuploads konnten nicht verarbeitet werden!', true);
    die();
}

if($_FILES['source']['tmp_name'] == ''){
    copy($originalFile, $sourceFile);
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
$filename = $patcher->createTunedFile($sourceFile, $originalFile);
downloadFile($filename, basename($filename), md5_file($filename));
?>