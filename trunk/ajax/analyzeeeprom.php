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
$fileName = $_FILES['eeprom']['name'];
$output = "<h3>EEPROM Analyzer Ausgabe für Datei: {$fileName}</h3><br /><dl>";

// DO ANALYITCAL STUFF
$byteDefinitions = array(
    "model"         =>  array( "35", "37" ),
    "type"          =>  array( "38", "3A" ),
    "side"          =>  array( "3B", "3B" ),
    "FIN"           =>  array( "32", "42" ),
    "idcode"           =>  array( "0A", "19" ),
    "fahrzaehler"   =>  array('6C', '6D'),
    "writes"        =>  array("F2", "F2"),
    "scn"           =>  array("05", "06"),
    "features"      =>  array('07', '07'),
    "schluesselcode" =>  array('A4', 'A5'),
    "wegfahrsperre" =>  array('52', '52'),
    "wegfahrsperre1" =>  array('B4', 'B5'),
    "wegfahrsperre2" =>  array('B6', 'B7'),
    "key"           =>  array('56', '56'),
    "keycode"       =>  array('5C', '5D'),
    "adaption"      =>  array('E3', 'E6'),
    "kupplungsc1" =>  array('9A', '9B'),
    "kupplungsc2" =>  array('9C', '9D'),
    "kupplungsc3" =>  array('9E', '9F'),
    "kupplungsc3zeit" =>  array('A2', 'A3'),
    "fehlercode" =>  array('B6', 'B6'),
    "zaehler" =>  array('A0', 'A1'),
    "killbytes" =>  array('96', '97')
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
                case "idcode":
                    $output .= "
                        <dt>ID Code (SCN)</dt>
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
                        <dd>".ord($value)."</dd>";
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
                    $resultsArray = array_reverse($resultsArray);
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
                                if($scnValueList[$i] != ''){
                                    $scnArray[] = $scnValueList[$i];
                                }
                            }
                        }
                    }
                    var_dump($scnArray);
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
                case "wegfahrsperre":
                    $valueBinary = getBinaryString($value);
                    $valueDec = hexdec(reset(unpack('H*', $value)));
                    $output .= "
                        <dt>Wegfahrsperre:</dt>
                        <dd>Byte 0x52 = $valueBinary ($valueDec)</dd>";
                    break;
                case "wegfahrsperre1":
                    $valueDec = reset(unpack('S*', $value));
                    $output .= "
                        <dt>Wegfahrsperre I:</dt>
                        <dd>Short B4B5 = $valueDec</dd>";
                    break;
                case "wegfahrsperre2":
                    $valueDec = reset(unpack('S*', $value));
                    $output .= "
                        <dt>Wegfahrsperre II:</dt>
                        <dd>Short B6B7 = $valueDec</dd>";
                    break;
                case "key":
                    $valueDec = hexdec(reset(unpack('H*', $value)));
                    $output .= "
                        <dt>Key (???):</dt>
                        <dd>Byte 0x56 = $valueDec</dd>";
                    break;
                case "keycode":
                    $valueDec = reset(unpack('S*', $value));
                    $output .= "
                        <dt>Key Code (???):</dt>
                        <dd>Short 5C5D = $valueDec</dd>";
                    break;
                case "fahrzaehler":
                    $valueDec = reset(unpack('S*', $value));
                    $output .= "
                        <dt>Fahrzähler:</dt>
                        <dd>Short 6C6D = {$valueDec} km</dd>";
                    break;
                case "kupplungsc1":
                    $valueDec = reset(unpack('S*', $value));
                    $output .= "
                        <dt>Kupplungsschutzklasse I:</dt>
                        <dd>Short 9A9B = {$valueDec}</dd>";
                    break;
                case "kupplungsc2":
                    $valueDec = reset(unpack('S*', $value));
                    $output .= "
                        <dt>Kupplungsschutzklasse II:</dt>
                        <dd>Short 9C9D = {$valueDec}</dd>";
                    break;// kupplungsc3zeit
                case "kupplungsc3":
                    $valueDec = reset(unpack('S*', $value));
                    $output .= "
                        <dt>Kupplungsschutzklasse III:</dt>
                        <dd>Short 9E9F = {$valueDec}</dd>";
                    break;
                case "kupplungsc3zeit":
                    $valueDec = reset(unpack('S*', $value));
                    $output .= "
                        <dt>Zeit in Kupplungsschutzklasse III:</dt>
                        <dd>Short A2A3 = {$valueDec} Minuten</dd>";
                    break;
                case "zaehler":
                    $valueDec = reset(unpack('S*', $value));
                    $output .= "
                        <dt>Zähler (???):</dt>
                        <dd>Short A0A1 = {$valueDec}</dd>";
                    break;
                case "killbytes":
                    $valueDec = reset(unpack('S*', $value));
                    $output .= "
                        <dt>Kill Bytes (???):</dt>
                        <dd>Short 9697 = {$valueDec}</dd>";
                    break;
                case "schluesselcode":
                    $valueDec = reset(unpack('S*', $value));
                    $output .= "
                        <dt>Schlüsselcode:</dt>
                        <dd>Short A4A5 = {$valueDec}</dd>";
                    break;
                case "fehlercode":
                    $valueBinary = getBinaryString($value);
                    $valueDec = hexdec(reset(unpack('H*', $value)));
                    $output .= "
                        <dt>Fehlercode:</dt>
                        <dd>Byte 0xB6 = $valueBinary</dd>";
                    break;
                case "adaption":
                    $adaptionsBytes = str_split($value, 1);
                    $start = $byteDefinitions['adaption'][0];
                    $bytesOutput = array( );
                    foreach($adaptionsBytes as $key => $byte){
                        $byteVal = "0x".strtoupper(dechex(hexdec($start)+$key));
                        $valueBinary = getBinaryString($byte);
                        $valueDec = hexdec(reset(unpack('H*', $byte)));
                        $bytesOutput[] = "$byteVal\t$valueBinary ($valueDec)";
                    }
                    $output .= "
                        <dt>Adaptionswerte:</dt>
                        <dd>".implode('<br/>',$bytesOutput)."</dd>";
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

        function getBinaryString($value){
            return str_pad(decbin(hexdec(reset(unpack('H*', $value)))), 8, '0', STR_PAD_LEFT);
        }

        ?>