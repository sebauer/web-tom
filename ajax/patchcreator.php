<fieldset>
    <legend>Quelldateien</legend>
    <div class="singleMap">
        <label for="original">Original File (371568):</label><input type="file" name="original" />
    </div>
    <div class="singleMap">
        <label for="source">Datei zum Patchen (Basis 371568):<br /><span class="labelInfo">Wenn leergelassen, wird Originaldatei verwendet.</span></label><input type="file" name="source" />
    </div>
</fieldset>
<?php
require_once('../includes/bootstrap.php');
chdir('..');

$mapGroups = PatchLocator::getMapsGrouped('R60_2005');

/* @var Map $map */
foreach($mapGroups as $groupname => $maps){
    ?>
    <fieldset>
    <?php
    $groupname = htmlentities($groupname);
    if($groupname != 'none'){
       ?>
        <legend><?=$groupname?></legend>
       <?php
    }
    ?>
    <?php
    foreach($maps as $map){
        $settings = $map->getSettings();
        ?>
    <div class="singleMap"><label
        for="setting[<?=$map->getAbbreviation()?>]"><?=htmlentities(reset($settings)->getDescription())?>:</label><select
        name="setting[<?=$map->getAbbreviation()?>]">
        <?php

        foreach($settings as $settingName => $setting){
                    ?>
                    <option value="<?=$settingName?>"><?=htmlentities($setting->getListEntry())?></option>
                    <?php
        }
        ?>
    </select></div>
        <?php
    }
    ?>
    </fieldset>
    <?php
}

?>