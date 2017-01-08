<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: catLegend.php 941 2010-05-20 13:21:57Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class catLegend {
	function __construct($id, $name, $color, $description,$parent_id=0)
	{
		$this->id=$id;
		$this->name=$name;
		$this->color=$color;
		$this->description=$description;
		$this->parent_id=$parent_id;
	}
}
