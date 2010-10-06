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

if($_FILES['original']['tmp_name'] == ''){
    jsCallback('Es wurde keine Originaldatei mit hochgeladen!', true);
    die();
}
$requestId = md5(serialize($_POST).serialize($_FILES).microtime());
$originalFile = _ORIGINALUPLOAD.'tmp'.$requestId.'.bin';
$sourceFile = _SOURCEUPLOAD.'tmp'.$requestId.'.bin';

if(!move_uploaded_file($_FILES['original']['tmp_name'], $originalFile) || $_FILES['source']['tmp_name'] != '' && !move_uploaded_file($_FILES['source']['tmp_name'], $sourceFile)){
    jsCallback('Dateiuploads konnten nicht verarbeitet werden!', true);
    die();
}

if(md5_file($originalFile)!=_ORIGINAL_MD5){
    jsCallback('Die hochgeladene Originaldatei entspricht keiner gültigen Version 371568!');
    die();
}

if($_FILES['source']['tmp_name'] == ''){
    copy($originalFile, $sourceFile);
}

if(_SCOTTYCHECK==true && Patcher::checkForScottyFile($sourceFile)){
    jsCallback('Dies ist ein File von Scotty und ist nicht für Patches freigegeben!');
    die();
}

$maps = PatchLocator::getMaps('R60_2005');
$patcher = new Patcher();

foreach($_POST['setting'] as $mapName => $settingVal){
    if($settingVal == $mapName.'0' && !array_key_exists('overwriteOriginal', $_POST)){
        continue;
    }
    $mod = new Modification($maps[$mapName], $maps[$mapName]->getSetting($settingVal));
    $patcher->addModification($mod);
}
$presetString = '';
$presetMap = _PRESET_MAPPING();
foreach($presetMap as $preset){
	if(!array_key_exists($preset, $_POST['setting'])) {
		$presetString = '';
		break;
	}
	$presetString .= str_replace($preset, '', $_POST['setting'][$preset]);
}

if($patcher->getNumberOfModifications() == 0){
    jsCallback('Keine Änderungen vorgenommen!');
    die();
}

$staticMaps = PatchLocator::getStaticMaps('R60_2005');
foreach($staticMaps as $mapName => $map){
    $mod = new Modification($map, $map->getSetting($mapName.'0'));
    $patcher->addModification($mod);
}

$filename = $patcher->createTunedFile($sourceFile, $originalFile);
$newFilename = str_replace(basename($filename),'',$filename).'R60_2005-'.$presetString.'-'.$requestId.'.Bad Checksums!!';

rename($filename, $newFilename);
downloadFile($newFilename, basename($newFilename), md5_file($newFilename));
?>