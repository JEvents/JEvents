<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: README.php 941 2010-05-20 13:21:57Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Readme Events - english language

defined( '_JEXEC' ) or die( 'Restricted access' );

// required during install!
include_once(JPATH_ADMINISTRATOR."/components/".JEV_COM_COMPONENT."/libraries/version.php");

$version = JEventsVersion::getInstance();
?>
<style type="text/css" media="screen">
    <!--
    h1 {
        color           : #30559C;
        font-size       : 112%;
        border-left     : 25px solid #30559C;
        border-bottom   : 1px solid #30559C;
        padding         : 0 0 2px 5px;
        width			: 95%;
        text-align		: left;
    }
    pre {
        color   		: #FF0000;
    }
    .text {
    	color			: #666666;
    	text-align		: left;
    	margin			: 10px;
    }
    hr {
    	border-bottom   : 1px solid #30559C;
    }
    .tip {
    	color			: #FF0000;
    	font-weight     : bold;
    }
    .ads {
        white-space 	: pre;
        border      	: 1px solid #336699;
        padding     	: 5px;
        margin      	: auto;
        width       	: 750px;
        background  	: #F9FDFF;
        text-align  	: center;
        clear			: both;
    }
    .hint {
        background  	: #FFDDDD;
        border      	: 1px solid #FF0000;
        margin      	: 5px;
        padding     	: 5px;
    }
    .highlight {
    	color           : #30559C;
    	font-weight     : bold;
    }
    .docinfo {
    	font-size		: 9pt;
    	color			: #666666;
    }
    -->
</style>
<h1>JEvents - Event Managment at its easiest!</h1>
<div class="text">
	<ul>
		<li>Version <?php echo $version->getShortVersion();?> - <a href="http://www.jevents.net" target="_blank" title="Project Website">Project Website</a></li>
		<li><?php echo $version->getLongCopyright();?></li>
		<li>Copyright (C) 2000 - 2003 Eric Lamette, Dave McDonnell</li>
		<li>Requirement: <a href="http://www.joomla.org" target="_blank" title="Joomla">Joomla</a> 1.5.x</li>
		<li>License: GNU/GPL <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank" title="License">License</a></li>
		<li>Website: <a href="http://www.jevents.net" target="_blank" title="JEvents">JEvents</a></li>
		<li>Support: <a href="http://www.jevents.net" target="_blank" title="JEvents">JEvents</a></li>
		<li>Email: <a href="http://www.jevents.net" target="_blank" title="JEvents">JEvents</a></li>
	</ul>
	<hr />

</div>
