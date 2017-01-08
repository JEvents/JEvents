<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: colorMap.php 941 2010-05-20 13:21:57Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2017 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
 
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\String\StringHelper;

function JevMapColor($background) {

	$colorMap = array();

	$colorMap['#FFFFFF'] = '#000000';
	$colorMap['#FFCCCC'] = '#000000';
	$colorMap['#FFCC99'] = '#000000';
	$colorMap['#FFFF99'] = '#000000';
	$colorMap['#FFFFCC'] = '#000000';
	$colorMap['#99FF99'] = '#000000';
	$colorMap['#99FFFF'] = '#000000';
	$colorMap['#CCFFFF'] = '#000000';
	$colorMap['#CCCCFF'] = '#000000';
	$colorMap['#FFCCFF'] = '#000000';

	$colorMap['#CCCCCC'] = '#000000';
	$colorMap['#FF6666'] = '#FFFFFF' ;
	$colorMap['#FF9966'] = '#000000';
	$colorMap['#FFFF66'] = '#000000';
	$colorMap['#FFFF33'] = '#000000';
	$colorMap['#66FF99'] = '#000000';
	$colorMap['#33FFFF'] = '#000000';
	$colorMap['#66FFFF'] = '#000000';
	$colorMap['#9999FF'] = '#FFFFFF' ;
	$colorMap['#FF99FF'] = '#000000';

	$colorMap['#C0C0C0'] = '#000000';
	$colorMap['#FF0000'] = '#FFFFFF' ;
	$colorMap['#FF9900'] = '#FFFFFF' ;
	$colorMap['#FFCC66'] = '#000000';
	$colorMap['#FFFF00'] = '#000000';
	$colorMap['#33FF33'] = '#000000';
	$colorMap['#66CCCC'] = '#000000';
	$colorMap['#33CCFF'] = '#000000';
	$colorMap['#6666CC'] = '#FFFFFF' ;
	$colorMap['#CC66CC'] = '#FFFFFF' ;

	$colorMap['#999999'] = '#FFFFFF' ;
	$colorMap['#CC0000'] = '#FFFFFF' ;
	$colorMap['#FF6600'] = '#FFFFFF' ;
	$colorMap['#FFCC33'] = '#000000';
	$colorMap['#FFCC00'] = '#000000';
	$colorMap['#33CC00'] = '#FFFFFF' ;
	$colorMap['#00CCCC'] = '#000000';
	$colorMap['#3366FF'] = '#FFFFFF' ;
	$colorMap['#6633FF'] = '#FFFFFF' ;
	$colorMap['#CC33CC'] = '#FFFFFF' ;

	$colorMap['#666666'] = '#FFFFFF' ;
	$colorMap['#990000'] = '#FFFFFF' ;
	$colorMap['#CC6600'] = '#FFFFFF' ;
	$colorMap['#CC9933'] = '#FFFFFF' ;
	$colorMap['#999900'] = '#FFFFFF' ;
	$colorMap['#009900'] = '#FFFFFF' ;
	$colorMap['#339999'] = '#FFFFFF' ;
	$colorMap['#3333FF'] = '#FFFFFF' ;
	$colorMap['#6600CC'] = '#FFFFFF' ;
	$colorMap['#993399'] = '#FFFFFF' ;

	$colorMap['#333333'] = '#FFFFFF' ;
	$colorMap['#660000'] = '#FFFFFF' ;
	$colorMap['#993300'] = '#FFFFFF' ;
	$colorMap['#996633'] = '#FFFFFF' ;
	$colorMap['#666600'] = '#FFFFFF' ;
	$colorMap['#006600'] = '#FFFFFF' ;
	$colorMap['#336666'] = '#FFFFFF' ;
	$colorMap['#000099'] = '#FFFFFF' ;
	$colorMap['#333399'] = '#FFFFFF' ;
	$colorMap['#663366'] = '#FFFFFF' ;

	$colorMap['#000000'] = '#FFFFFF' ;
	$colorMap['#330000'] = '#FFFFFF' ;
	$colorMap['#663300'] = '#FFFFFF' ;
	$colorMap['#663333'] = '#FFFFFF' ;
	$colorMap['#333300'] = '#FFFFFF' ;
	$colorMap['#003300'] = '#FFFFFF' ;
	$colorMap['#003333'] = '#FFFFFF' ;
	$colorMap['#000066'] = '#FFFFFF' ;
	$colorMap['#330099'] = '#FFFFFF' ;
	$colorMap['#330033'] = '#FFFFFF' ;

	if (array_key_exists($background,$colorMap)) return $colorMap[$background];

	// see http://24ways.org/2010/calculating-color-contrast/
	$hexcolor = str_replace("#", "", $background);
	$r = hexdec(JString::substr($hexcolor,0,2));
	$g = hexdec(JString::substr($hexcolor,2,2));
	$b = hexdec(JString::substr($hexcolor,4,2));
	$yiq = (($r*299)+($g*587)+($b*114))/1000;
	return ($yiq >= 128) ? '#000':'#fff';

}
?>
