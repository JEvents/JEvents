<?php
defined('_JEXEC') or die('Restricted access');

$this->_header();
$this->_showNavTableBar();

echo $this->loadTemplate("form");

$this->_footer();
