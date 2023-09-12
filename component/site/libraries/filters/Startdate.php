<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: Startdate.php 1976 2011-04-27 15:54:31Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_VALID_MOS') or defined('_JEXEC') or die('No Direct Access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;
use Joomla\CMS\Component\ComponentHelper;

// Event repeat startdate fitler
class jevStartdateFilter extends jevFilter
{

	var $dmap = "";
	var $_onorbefore = false;
	var $_date = "";

	function __construct($tablename, $filterfield, $isstring = true)
	{

		$this->fieldset = true;

		$this->valueNum            = 3;
		$this->filterNullValue     = 0;
		$this->filterNullValues[0] = 0; // n/a, before, after
		$this->filterNullValues[1] = ""; // the date
		$this->filterNullValues[2] = 0; // true means the form is submitted

		$this->filterType  = "startdate";
		$this->filterLabel = "";
		$this->dmap        = "rpt";
		parent::__construct($tablename, $filterfield, true);

		// This filter is special and always remembers for logged in users
		if (Factory::getUser()->id > 0)
		{
			$this->filter_value = Factory::getApplication()->getUserStateFromRequest($this->filterType . '_fv_ses', $this->filterType . '_fv', $this->filterNullValue);
			for ($v = 0; $v < $this->valueNum; $v++)
			{
				$this->filter_values[$v] = Factory::getApplication()->getUserStateFromRequest($this->filterType . '_fvs_ses' . $v, $this->filterType . '_fvs' . $v, $this->filterNullValues[$v]);
			}
		}

		$this->_date       = $this->filter_values[1];
		$this->_onorbefore = $this->filter_values[0];

	}

	function _createFilter($prefix = "")
	{

		if (!$this->filterField) return "";
		// first time visit
		if (isset($this->filter_values[2]) && $this->filter_values[2] == 0)
		{
			$this->filter_values    = array();
			$this->filter_values[0] = 1;
			// default scenario is only events starting after 2 weeeks ago			
			$fulldate               = date('Y-m-d H:i:s', JevDate::strtotime("-2 weeks"));
			$this->filter_values[1] = StringHelper::substr($fulldate, 0, 10);
			$this->filter_values[2] = 1;

			return $this->dmap . ".startrepeat>='$fulldate'";
		}
		else if ($this->filter_values[0] == 0)
		{
			$this->filter_values[1] = "";
			$this->_date            = $this->filter_values[1];
		}
		else if ($this->filter_values[0] == -1 && $this->filter_values[1] == "")
		{
			$fulldate               = date('Y-m-d H:i:s', JevDate::strtotime("+2 weeks"));
			$this->filter_values[1] = StringHelper::substr($fulldate, 0, 10);
			$this->_date            = $this->filter_values[1];
		}
		else if ($this->filter_values[0] == 1 && $this->filter_values[1] == "")
		{
			$fulldate               = date('Y-m-d H:i:s', JevDate::strtotime("-2 weeks"));
			$this->filter_values[1] = StringHelper::substr($fulldate, 0, 10);
			$this->_date            = $this->filter_values[1];
		}
		$filter = "";

		if ($this->_date != "" && $this->_onorbefore != 0)
		{
			$date     = JevDate::strtotime($this->_date);
			$fulldate = date('Y-m-d H:i:s', $date);
			if ($this->_onorbefore > 0)
			{
				$date = $this->dmap . ".startrepeat>='$fulldate'";
			}
			else
			{
				$date = $this->dmap . ".startrepeat<'$fulldate'";
			}
		}
		else
		{
			$date = "";
		}
		$filter = $date;

		return $filter;
	}

	function _createfilterHTML($class = "inputbox")
	{

		if (!$this->filterField) return "";

		// only works on admin list events pages
		if (Factory::getApplication()->input->getCmd("jevtask", "") != "admin.listevents")
		{
			$filterList          = array();
			$filterList["title"] = "";

			$filterList["html"] = "";

			return $filterList;
		}

		$filterList          = array();
		$filterList["title"] = Text::_('WITH_INSTANCES');

		$filterList["html"] = "";

		$options            = array();
		$options[]          = HTMLHelper::_('select.option', '0', Text::_('WHEN'));
		$options[]          = HTMLHelper::_('select.option', '1', Text::_('On_or_after'));
		$options[]          = HTMLHelper::_('select.option', '-1', Text::_('BEFORE'));
		$filterList["html"] .= HTMLHelper::_('select.genericlist', $options, $this->filterType . '_fvs0', 'onchange="form.submit()" class="' . $class . '" size="1" ', 'value', 'text', $this->filter_values[0]);

		//$filterList["html"] .=  HTMLHelper::calendar($this->filter_values[1],$this->filterType.'_fvs1', $this->filterType.'_fvs1', '%Y-%m-%d',
		//array('size'=>'12','maxlength'=>'10','onchange'=>'form.submit()'));

		$params   = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$minyear  = JEVHelper::getMinYear();
		$maxyear  = JEVHelper::getMaxYear();
		$document = Factory::getDocument();

		$inputdateformat = $params->get("com_editdateformat", "d.m.Y");
		ob_start();
		JEVHelper::loadElectricCalendar($this->filterType . '_fvs1', $this->filterType . '_fvs1', $this->filter_values[1],
			$minyear, $maxyear, '', "jQuery('#" . $this->filterType . "_fvs1').parents('form').submit();", "Y-m-d",
			array( "maxlength"=>"10", "size"=>"12"),
			false);
		$filterList["html"] .= "<div style='display:inline-block;'>" . ob_get_clean() . "</div>";

		//$filterList["html"] .= '<input type="text" name="' . $this->filterType . '_fvs1" id="' . $this->filterType . '_fvs1" value="' . $this->filter_values[1] . '" maxlength="10" size="12"  />';

		$filterList["html"] .= "<input type='hidden' name='" . $this->filterType . "_fvs2' value='1'/>";

		return $filterList;


	}
}
