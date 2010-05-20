<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevviewcopyright.php 1400 2009-03-30 08:45:17Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


function JevViewCopyright() {

	global $mainframe;

	$cfg	 = & JEVConfig::getInstance();

	$version = & JEventsVersion::getInstance();

	if ($cfg->get('com_copyright', 1) == 1) {
?>
		<p align="center">
			<a href="<?php echo $version->getUrl();?>" target="_blank" style="font-size:xx-small;" title="Events Website"><?php echo $version->getLongVersion();?></a>
			&nbsp;
			<span style="color:#999999; font-size:9px;"><?php echo $version->getShortCopyright();?></span>
		</p>
		<?php
	}
}
