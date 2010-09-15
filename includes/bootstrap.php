<?php
/**
 * NEVER CHANGE ANYTHING IN THIS CONFIGURATION!
 * 
 * DO **NOT** TOUCH ANY OF THE DEFINES!
 * 
 */
require_once('autoload.php');
require_once('config.php');
require_once('parse_ini_string.php');

define('_VERSION', '0.8.1');

define('_DIRECTORY', './patches/tom/R60_2005/');

define('_PATCHESDIR', './patches/tom/');
define('_STATICPATCHESDIR', './patches/static/');

define('_DESCRIPTION', 'Description');
define('_LISTENTRY', 'ListEntry');
define('_RANGES', 'Ranges');
define('_FEATURES', 'Features');
define('_GROUP', 'Group');
define('_RANGESCOUNT', 'RangesCount');

define('_SCOTTY_OFFSET', '1C3A8');
define('_SCOTTY_STRING', 'CA0079006100CA009401A100CA00');