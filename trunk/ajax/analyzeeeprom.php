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

if($_FILES['eeprom']['tmp_name'] == ''){
    jsCallback('Es wurde kein EEPROM Dump hochgeladen!', true);
    die();
}
$requestId = md5(serialize($_POST).serialize($_FILES).microtime());
$eeprom = _EEPROMUPLOAD.'tmp'.$requestId.'.bin';

if(!move_uploaded_file($_FILES['eeprom']['tmp_name'], $eeprom)){
    jsCallback('Dateiuploads konnten nicht verarbeitet werden!', true);
    die();
}

if(filesize($eeprom)!=_EEPROM_SIZE){
    jsCallback('Unerwartete Dateigröße des hochgeladenen EEPROM Dumps!');
    die();
}

// DO ANALYITCAL STUFF

$content = file_get_contents($eeprom);

$byteDefinitions = array(
    "model" =>  array( "35", "37" ),
    "type"  =>  array( "38", "3A" ),
    "FIN"   =>  array( "32", "42" ),
    "writes"    =>  array("F2", "F2"),
    "writes_sc" =>  array("F3", "F3"),
    "features"  =>  array("7", "7")
);

$valueList = array(
    "model" =>  array(
        "452"   =>  "Smart Roadster",
        "450"   =>  "Smart ForTwo"
    ),
    "type"  =>  array(
        "332"   =>  "Coupé, 45kW",
        "334"   =>  "Coupé, 60kW",
        "337"   =>  "Coupé, Brabus, 74kW",
        "434"   =>  "Roadster, 60kW",
        "432"   =>  "Roadster, 45kW",
        "437"   =>  "Roadster Brabus, 74kW"
    ),
    "features"  =>  array(
        "Smart",
        "Schaltwippen",
        "Servolenkung",
        "Tempomat",
        "Softtip",
        "unbekannt (Bit6)",
        "ohne Klimaanlage",
        "unbekannt (Bit8)"
    )
);

$value = decbin(ord(substr($content, 7, 1)));
$resultsArray = array();

for($i=1;$i<strlen($value);$i++){
    if($value[$i]=='1'){
        $resultsArray[] = $valueList['features'][$i];
    }
}
$featureString = implode(', ', $resultsArray);


?>