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

define('_VERSION', '0.8.3');

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

if (!defined('_REVISION')) {
	if (file_exists('.svn' . DIRECTORY_SEPARATOR. 'entries')) {
		$svn = file('.svn' . DIRECTORY_SEPARATOR . 'entries');
		if (is_numeric(trim($svn[3]))) {
			$version = $svn[3];
		} else { // pre 1.4 svn used xml for this file
			$version = explode('"', $svn[4]);
			$version = $version[1];
		}
		define ('_REVISION', trim($version));
		unset ($svn);
		unset ($version);
	} else {
		define ('_REVISION', 0); // default if no svn data avilable
	}
}