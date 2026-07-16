<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents\Administrator;

defined('_JEXEC') or die('Restricted access');

use JEvents\Site\Registry;
use Joomla\CMS\Language\Text;
use Joomla\String\StringHelper;

/**
 * J1.5-era parameter renderer extending Registry.
 * Legacy class name: JevParameter
 */
class Parameter extends Registry
{
	function render(string $name = 'params', string $group = '_default')
	{
		if (!isset($this->_xml[$group]))
		{
			return false;
		}

		$params = $this->getParams($name, $group);
		$html   = [];
		$html[] = '<table width="100%" class="paramlist admintable" cellspacing="1">';

		if ($description = $this->_xml[$group]->attributes('description'))
		{
			$html[] = '<tr><td class="paramlist_description" colspan="2">' . Text::_($description) . '</td></tr>';
		}

		foreach ($params as $param)
		{
			$class = "";

			foreach ($this->_xml[$group]->children() as $kid)
			{
				if ($kid->attributes("name") != "@spacer"
					&& $kid->attributes("name") == $param[5]
					&& $kid->attributes("label") == $param[3]
					&& $kid->attributes("description") == $param[2])
				{
					$class = $kid->attributes("class");
					break;
				}
			}

			if (StringHelper::strlen($class) > 0)
			{
				$class = " class='$class'";
			}

			$html[] = "<tr $class>";

			if ($param[0])
			{
				$html[] = '<td class="paramlist_key"><span class="editlinktip">' . $param[0] . '</span></td>';
				$html[] = '<td class="paramlist_value">' . $param[1] . '</td>';
			}
			else
			{
				$html[] = '<td class="paramlist_value" colspan="2">' . $param[1] . '</td>';
			}

			$html[] = '</tr>';
		}

		if (count($params) < 1)
		{
			$html[] = "<tr><td colspan=\"2\"><i>" . Text::_('THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM') . "</i></td></tr>";
		}

		$html[] = '</table>';

		return implode("\n", $html);
	}
}