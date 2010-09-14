<form enctype="multipart/form-data" method="post" action="ajax/createpatch.php" id="patchCreatorForm" target="uploadFrame">
<div id="patchCreatorContent">
<fieldset>
    <div class="singleMap">
        <label for="original">Original File (371568):</label><input type="file" name="original" />
    </div>
    <div class="singleMap">
        <label for="source">Datei zum Patchen (Basis 371568):</label><input type="file" name="source" />
    </div>
</fieldset>
<fieldset>
<?php
require_once('../includes/bootstrap.php');
chdir('..');

$files = scandir(_DIRECTORY);
//var_dump($files);
foreach($files as $file){
    if(strpos($file, '.2PF')===false)continue;

    ob_start();
    $mapIni = parse_ini_file(_DIRECTORY.$file, true, INI_SCANNER_RAW);
    $output = ob_get_clean();
    ob_end_flush();

    if(!$mapIni){
        echo '<span style="color: red;">Failed loading '.$file.': '.$output.'</span><br />';
        continue;
    }

    $map = new Map(str_replace('.2PF', '', $file));
    ?>
<div class="singleMap"><label
	for="setting[<?=$map->getAbbreviation()?>]"><?=htmlentities($mapIni[$map->getAbbreviation().'0'][_DESCRIPTION])?>:</label><select
	name="setting[<?=$map->getAbbreviation()?>]">
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
            	<option value="<?=$sectionName?>"><?=htmlentities($section[_LISTENTRY])?></option>
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
</select></div>
	<?php
}

?>
</fieldset>
</div>
<script type="text/javascript">
    $(function() {
        $("button").button();
    });
</script>
<div id="patchCreateFooter">
    <div style="float: left;">
        <button onclick="showDisclaimer();return false;">Tuning File erstellen</button>
    </div>
    <div style="float: left;margin-left: 30px;">
        <label for="overwriteOriginal"><input type="checkbox" id="overwriteOriginal" name="overwriteOriginal" value="1" /> Auf original stehendes mit Original überschreiben</label>
    </div>
</div>
</form>
<iframe name="uploadFrame" style="width: 100%;border: none;"></iframe>