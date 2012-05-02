<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: category.php 1142 2010-09-08 10:10:52Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.categories');


class JEventsCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__jevents';
		$options['extension'] = 'com_jevents';
		parent::__construct($options);
	}
}