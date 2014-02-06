<?php 
defined('_JEXEC') or die('Restricted access');

// stop crawler from indexing this site but allow follow
JEVHelper::checkRobotsMetaTag("robots", "noindex,follow");

echo $this->loadTemplate("body");
//$this->_footer();


