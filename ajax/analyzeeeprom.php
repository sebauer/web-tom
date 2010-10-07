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

$output = '<dl>';

// DO ANALYITCAL STUFF
$byteDefinitions = array(
    "model" =>  array( "35", "37" ),
    "type"  =>  array( "38", "3A" ),
    "side"  =>  array( "3B", "3B" ),
    "FIN"   =>  array( "32", "42" ),
    "writes"    =>  array("F2", "F2"),
    "scn"    =>  array("05", "06"),
    "features"  =>  array('07', '07')
);

$valueList = array(
    "model" =>  array(
        "452"   =>  "Smart Roadster",
        "450"   =>  "Smart ForTwo"
    ),
    "type"  =>  array(
        "332"   =>  " Coupé, 45kW",
        "334"   =>  " Coupé, 60kW",
        "337"   =>  " Coupé, 74kW (Brabus)",
        "434"   =>  "60kW",
        "432"   =>  "45kW",
        "437"   =>  "74kW (Brabus)"
    ),
    "side"  =>  array(
        "1" => "Linkslenker",
        "2" => "Rechtslenker"
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
    ),
    "scn0"  =>  array(
        "",
        "SCN5: ??? (Bit 2)",
        "",
        "",
        "",
        "SCN5: ohne Klima (Bit 6)",
        "SCN5: SB2 (Bit 7)",
        "SCN5: Roadster (Bit 8)"
    ),
    "scn1"  =>  array(
        "",
        "",
        "",
        "SCN6: 45kW (Bit 4)",
        "SCN6: 60kW (Bit 5)",
        "",
        "SCN6: 74kW (Bit 7)",
        ""
    )
);

$sEeprom = fopen($eeprom, 'r');

$model = '';
foreach( $byteDefinitions as $name => $pos ) {

    $start = hexdec($pos[0]);
    $end = hexdec($pos[1]);
    $length = ($end-$start)+1;

    fseek($sEeprom, $start);
    $value = fread($sEeprom, $length);
    switch( $name ) {
        case "model":
            $model = $valueList["model"][$value];
            break;
        case "type":
            $output .= "
            <dt>Modell</dt>
            <dd>$model ".$valueList["type"][$value]."</dd>";
            break;
        case "FIN":
            $output .= "
            <dt>FIN</dt>
            <dd>$value</dd>";
            break;
        case "writes":
            fseek($sEeprom, hexdec('ED'));
            $ed = fread($sEeprom, 1);
            $ed = ord($ed);
            if($ed == hexdec("FF")){
                $output .= "
                    <dt>MEG Flashes (Tuningfiles)</dt>
                    <dd>".ord($value)."</dd>";
            } else {
                $output .= "
                    <dt>MEG Flashes (sC)</dt>
                    <dd>".ord($value)." ".ord($ed).")</dd>";
            }
            break;
        case "features":
            $value = getBinaryString($value);
            $featuresArray = array_reverse($valueList['features']);
            $resultsArray = array();

            for($i=0;$i<strlen($value);$i++){
                if($value[$i]=='1'){
                    $resultsArray[] = $featuresArray[$i];
                }
            }
            $featureString = implode(', ', $resultsArray);
            $output .= "
                <dt>Features:</dt>
                <dd>$featureString</dd>";
            break;
        case "scn":
            $scnBytes = str_split($value, 1);

            $scnArray = array();

            foreach($scnBytes as $offset => $scnByte){
                $scnByte = getBinaryString($scnByte);
                $scnValueList = array_reverse($valueList['scn'.$offset]);
                for($i=0;$i<strlen($scnByte);$i++){
                    if($scnByte[$i]=='1'){
                        $scnArray[] = $scnValueList[$i];
                    }
                }
            }
            $scnString = implode('<br />', $scnArray);
            $output .= "
                <dt>SCN Codierung:</dt>
                <dd>$scnString</dd>";
            break;
        case "side":
            $output .= "
            <dt>Fahrzeugaufbau</dt>
            <dd>".$valueList[$name][$value]."</dd>";
            break;
        default:
            $output .= "
            <dt>$name</dt>
            <dd>".$valueList[$name][$value]."</dd>";
            break;
    }
}
$output .= '</dl>';
echo "<div id='analysisOutput'>";
echo $output;
echo "</div>";
analyzerCallback();
//analyzerCallback(htmlentities($output));

function getBinaryString($value){
    return str_pad(decbin(hexdec(reset(unpack('H*', $value)))), 8, '0', STR_PAD_LEFT);
}

?>