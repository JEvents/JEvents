<?php

defined('_JEXEC') or die('Restricted access');

$this->_header();

//  don't show navbar stuff for events detail popup
if (!$this->pop)
{
	$this->_showNavTableBar();
}

if (version_compare(JVERSION, "3.0.0", 'ge'))
{
	echo $this->loadTemplate("body");
}
else
{
	echo $this->loadTemplate("body16");
}

if (!$this->pop)
{
	$this->_viewNavAdminPanel();
}

$this->_footer();

