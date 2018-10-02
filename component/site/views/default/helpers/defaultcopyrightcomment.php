<?php
defined('_JEXEC') or die('Restricted access');

function DefaultcopyrightComment($view)
{

	$version = JEventsVersion::getInstance();

	?>
	<!-- Event Calendar and Lists Powered by JEvents //-->
	<?php
	/*
	echo "\n" . '<!-- '
	. $version->getLongVersion() . ', '
	. utf8_encode(@html_entity_decode($version->getLongCopyright(), ENT_COMPAT, 'ISO-8859-1')) . ', '
	. $version->getUrl()
	. ' -->' . "\n";
	*/
}
