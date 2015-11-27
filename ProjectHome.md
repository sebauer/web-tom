Der WebTune-O-Matic ist eine web-basierte Version des Tune-O-Matics von Thomas "2eck" Drechsler.

WebTOM benötigt die gleichen .2PF-Patchfiles, wie sie auch der Tune-O-Matic verwendet.

Zudem wird ein Originaldatenstand des jeweiligen Steuergerätes benötigt, welcher aus urheberrechtlichen Gründen nicht mitgeliefert werden darf.

2PF-Files ab TOM Version 1.08 sind mit dem WebTOM getestet und verifiziert!

### Systemvorraussetzungen ###
  * Webserver (Apache, lighttpd, ...)
  * PHP > 5.0
  * Schreibrechte auf die enthaltenen Verzeichnisse:
    * upload/
    * upload/original/
    * upload/source/
    * upload/eeprom/
    * generated/
  * 2PF Patchfiles

### Konfigurationsdatei ###
Die Datei `includes/config.php` wird mit folgendem Inhalt benötigt ~~(muss manuell angelegt werden)~~:
```
<?php

define('_ORIGINALUPLOAD', 'upload/original/');
define('_SOURCEUPLOAD', 'upload/source/');
define('_EEPROMUPLOAD', 'upload/eeprom/');
define('_SCOTTYCHECK', true);

define('_DEBUG', false);
?>
```