<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevparams.php 1975 2011-04-27 15:52:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JevParameter extends  JRegistry{
	
	/**
	 * Render
	 *
	 * @access	public
	 * @param	string	The name of the control, or the default text area if a setup file is not found
	 * @return	string	HTML
	 * @since	1.5
	 */
	function render($name = 'params', $group = '_default')
	{
		if (!isset($this->_xml[$group])) {
			return false;
		}

		$params = $this->getParams($name, $group);
		$html = array ();
		$html[] = '<table width="100%" class="paramlist admintable" cellspacing="1">';

		if ($description = $this->_xml[$group]->attributes('description')) {
			// add the params description to the display
			$desc	= JText::_($description);
			$html[]	= '<tr><td class="paramlist_description" colspan="2">'.$desc.'</td></tr>';
		}

		foreach ($params as $param)
		{
			$class="";
			$rawparam = false;
			// find extra non-standard information
			foreach ($this->_xml[$group]->children() as $kid)  {
				if ($kid->attributes("name")!="@spacer" && $kid->attributes("name")==$param[5] && $kid->attributes("label")==$param[3] && $kid->attributes("description")==$param[2]){
					$class=$kid->attributes("class");
					$rawparam = $kid;
					break;
				}
			}
			if (JString::strlen($class)>0){
				$class=" class='$class'";
			}
			$html[] = "<tr $class>";
			if ($param[0]) {
				$html[] = '<td class="paramlist_key"><span class="editlinktip">'.$param[0].'</span></td>';
				$html[] = '<td class="paramlist_value">'.$param[1].'</td>';
			} else {
				$html[] = '<td class="paramlist_value" colspan="2">'.$param[1].'</td>';
			}

			$html[] = '</tr>';
		}

		if (count($params) < 1) {
			$html[] = "<tr><td colspan=\"2\"><i>".JText::_( 'THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM' )."</i></td></tr>";
		}

		$html[] = '</table>';

		return implode("\n", $html);
	}

}

