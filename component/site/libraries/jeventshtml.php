<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jeventshtml.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\String\StringHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Registry\Registry;

// TODO replace with JevDate

class JEventsHTML
{
	public static function buildReccurDaySelect($reccurday, $tag_name, $args)
	{

		// get array
		$day_name = JEVHelper::getWeekdayLetter(null, 1);
		$day_name[0] = '<span class="sunday">' . $day_name[0] . '</span>';
		$day_name[6] = '<span class="saturday">' . $day_name[6] . '</span>';

		$daynamelist[] = HTMLHelper::_('select.option', '-1', '&nbsp;' . Text::_('JEV_BYDAYNUMBER') . '<br />');

		for ($a = 0; $a < 7; $a++)
		{
			$name_of_day = '&nbsp;' . $day_name[$a]; //getDayName($a);
			$daynamelist[] = HTMLHelper::_('select.option', $a, $name_of_day);
		}

		$tosend = JEventsHTML::buildRadioOption($daynamelist, $tag_name, $args, 'value', 'text', $reccurday);
		echo $tosend;
	}

	public static function buildRadioOption($arr, $tag_name, $tag_attribs, $key, $text, $selected)
	{
		$html = ''; //"\n<div name=\"$tag_name\" $tag_attribs>";

		for ($i = 0, $n = count($arr); $i < $n; $i++)
		{
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;

			$sel = '';

			if (is_array($selected))
			{
				foreach ($selected as $obj)
				{
					$k2 = $obj->$key;
					if ($k == $k2)
					{
						$sel = ' checked="checked"';
						break;
					}
				}
			}
			else
			{
				$sel = ( $k == $selected ? ' checked="checked"' : '' );
			}

			$html .= "\n\t"
				. '<input name="' . $tag_name . '" type="radio" value="' . $k . '" id="' . $tag_name . $k . '"'
				. $sel . ' '
				. $tag_attribs
				. ' />' . "\n"
				. '<label for="' . $tag_name . $k . '">'
				. $t
				. '</label>'
				. "\n"
			;
		}
		//$html .= "\n</select>\n";
		return $html;
	}

	public static function buildMonthSelect($month, $args)
	{
		for ($a = 1; $a < 13; $a++)
		{
			$mnh = $a;
			if ($mnh <= '9')
			{
				$mnh = '0' . $mnh;
			}
			$name_of_month = JEVHelper::getMonthName($mnh);
			$monthslist[] = HTMLHelper::_('select.option', $mnh, $name_of_month);
		}

		$tosend = HTMLHelper::_('select.genericlist', $monthslist, 'month', $args, 'value', 'text', $month);
		echo $tosend;
	}

	public static function buildDaySelect($year, $month, $day, $args)
	{
		$nbdays = date('d', JevDate::mktime(0, 0, 0, ( $month + 1), 0, $year));

		for ($a = 1; $a <= $nbdays; $a++)
		{ //32
			$dys = $a;
			if ($dys <= '9')
			{
				$dys = '0' . $dys;
			}
			$dayslist[] = HTMLHelper::_('select.option', $dys, $dys);
		}

		$tosend = HTMLHelper::_('select.genericlist', $dayslist, 'day', $args, 'value', 'text', $day);
		echo $tosend;
	}

	public static function buildYearSelect($year, $args)
	{
		$y = date('Y');

		$cfg = JEVConfig::getInstance();
		$earliestyear = JEVHelper::getMinYear();
		$latestyear = JEVHelper::getMaxYear();

		if ($year < $y - 2 && $year >= $earliestyear && $year <= $latestyear)
		{
			$yearslist[] = HTMLHelper::_('select.option', $year, $year);
		}

		for ($i = $y - 2; $i <= $y + 5; $i++)
		{
			if ($i >= $earliestyear && $i <= $latestyear)
			{
				$yearslist[] = HTMLHelper::_('select.option', $i, $i);
			}
		}

		if ($year > $y + 5 && $year >= $earliestyear && $year <= $latestyear)
		{
			$yearslist[] = HTMLHelper::_('select.option', $year, $year);
		}

		$tosend = HTMLHelper::_('select.genericlist', $yearslist, 'year', $args, 'value', 'text', $year);
		echo $tosend;
	}

	public static function buildViewSelect($viewtype, $args)
	{

		$cfg = JEVConfig::getInstance();

		$iconstoshow = $cfg->get('iconstoshow', array('byyear', 'bymonth', 'byweek', 'byday', 'search'));
		$viewlist = array();

		if (in_array("byday", $iconstoshow))
			$viewlist[] = HTMLHelper::_('select.option', 'day.listevents', Text::_('JEV_VIEWBYDAY'));
		if (in_array("byweek", $iconstoshow))
			$viewlist[] = HTMLHelper::_('select.option', 'week.listevents', Text::_('JEV_VIEWBYWEEK'));
		if (in_array("bymonth", $iconstoshow))
			$viewlist[] = HTMLHelper::_('select.option', 'month.calendar', Text::_('JEV_VIEWBYMONTH'));
		if (in_array("byyear", $iconstoshow))
			$viewlist[] = HTMLHelper::_('select.option', 'year.listevents', Text::_('JEV_VIEWBYYEAR'));
		if (in_array("bycat", $iconstoshow))
			$viewlist[] = HTMLHelper::_('select.option', 'cat.listevents', Text::_('JEV_VIEWBYCAT'));
		if (in_array("search", $iconstoshow))
			$viewlist[] = HTMLHelper::_('select.option', 'search.form', Text::_('JEV_SEARCH_TITLE'));

		$tosend = HTMLHelper::_('select.genericlist', $viewlist, 'task', $args, 'value', 'text', $viewtype);
		echo $tosend;
	}

	public static function buildHourSelect($start, $end, $inc, $tag_name, $tag_attribs, $selected, $format = '')
	{

		$cfg = JEVConfig::getInstance();

		$start = intval($start);
		$end = intval($end);
		$inc = intval($inc);
		$arr = array();
		$tmpi = '';

		for ($i = $start; $i <= $end; $i += $inc)
		{
			if ($cfg->get('com_dateformat') == '1')
			{ // US time
				if ($i > 11)
				{
					$tmpi = ($i - 12) . ' pm';
				}
				else
				{
					$tmpi = $i . ' am';
				}
			}
			else
			{
				$tmpi = $format ? sprintf($format, $i) : $i;
			}

			$fi = $format ? sprintf($format, $i) : $i;
			$arr[] = HTMLHelper::_('select.option', $fi, $tmpi);
		}

		return HTMLHelper::_('select.genericlist', $arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
	}

	/**
	 * Build HTML selection list of categories
	 *
	 * @param int $catid				Selected catid
	 * @param string $args				Additional HTML attributes for the <select> tag
	 * @param string $catidList			Restriction list of categories
	 * @param boolean $with_unpublished	Set true to build list with unpublished categories
	 * @param boolean $require_sel		First entry: true = Choose one category, false = All categories
	 * @param int $catidtop				Top level category ancestor
	 */
	public static function buildCategorySelect($catid, $args, $catidList = null, $with_unpublished = false, $require_sel = false, $catidtop = 0, $fieldname = "catid", $sectionname = JEV_COM_COMPONENT, $excludeid = false, $order = "ordering", $eventediting = false, $allowMultiCat = false, $skipgslclass = false)
	{
		// need to declare this because of bug in Joomla HTMLHelper::_('select.options', on content pages - it loade the WRONG CLASS!
		//include_once(JPATH_SITE . "/libraries/cms/html/category.php");

		ob_start();
		$t_first_entry = ($require_sel) ? Text::_('JEV_EVENT_CHOOSE_CATEG') : Text::_('JEV_EVENT_ALLCAT');

        $db     = Factory::getDbo();
        $app    = Factory::getApplication();
        $input  = $app->input;
        $user   = Factory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());

        $query = $db->getQuery(true)
            ->select('a.id, a.title, a.level, a.language, a.parent_id, a.params')
            ->from('#__categories AS a')
            ->where('a.parent_id > 0');

        // Filter on extension.
        $query->where('extension = ' . $db->quote($sectionname));

        // Filter on user access level
        $query->where('a.access IN (' . $groups . ')');

        // If not show unpublished then force published = 1
        if (!$with_unpublished) {
            $query->where('a.published = 1');
        } else {
            // Only show published and unpublished
            $query->where('a.published NOT IN (-2,2)');
        }

        $query->order('a.lft');

        $db->setQuery($query);
        $items = $db->loadObjectList();

        // Assemble the list options.
        $options = array();
        $parents = array();

		$lang = Factory::getLanguage();
        foreach ($items as &$item)
        {
            $repeat = ($item->level - 1 >= 0) ? $item->level - 1 : 0;
            $item->title = str_repeat('- ', $repeat) . $item->title;

			$jevtask = $input->getString("jevtask", "");
			if (strpos($jevtask, "icalevent.edit") !== false || strpos($jevtask, "icalrepeat.edit") !== false)
			{
	            if ($item->language !== '*' && $item->language != $lang->getTag())
	            {
	                $item->title .= ' (' . $item->language . ')';
	            }
			}
			else if ($item->language !== '*')
            {
                $item->title .= ' (' . $item->language . ')';
            }

            $option = HTMLHelper::_('select.option', $item->id, $item->title);
            $option->level = $item->level;
			$itemParams = new Registry($item->params);
			$option->attr  = ['data-level' => $item->level, 'data-colour' => $itemParams->get('catcolour', 'transparent')];
            $option->parent_id = $item->parent_id;
            if ($option->parent_id > 1 && !array_key_exists($option->parent_id, $parents))
            {
                $parents[$option->parent_id] = 1;
            }
            $options[] = $option;
        }

		if ($catidList != null)
		{
			$cats = explode(',', $catidList);
			$count = count($options);
			for ($o = 0; $o < $count; $o++)
			{
				if (array_key_exists($o, $options) && !in_array($options[$o]->value, $cats))
				{
					unset($options[$o]);
				}
			}
			$options = array_values($options);
		}

		// Needs to be ordered so that selected values appear first when editing an event with sortable multiple categories
		$params = ComponentHelper::getParams($input->getCmd("option", "com_jevents"));
		if ($allowMultiCat || ($eventediting && $params->get("multicategory", 0)))
		{
			if (is_string($catid) && !empty($catid))
			{
				$catid = explode(",", $catid);
			}
		}
		if (is_array($catid) && $eventediting)
		{
            for ($c = 0; $c < count($catid); $c ++)
            {
                for ($o = 0; $o < count($options); $o++)
                {
                    if (!empty($options[$o]->value) && $options[$o]->value == $catid[$c])
                    {
                        $options[ - (count($catid) - $c)] = $options[$o];
                        unset($options[$o]);
                        break;
                    }
                }
            }
            ksort($options);
            $options = array_values($options);
		}

		// translate where appropriate
		$count = count($options);
		for ($o = 0; $o < $count; $o++)
		{
			$options[$o]->text = strpos($options[$o]->text, "JEV_") === 0 ? Text::_($options[$o]->text) : $options[$o]->text;
		}

		// Thanks to ssobada
		// when editing events we restrict the available list!
		$jevtask = $input->getString("jevtask", "");
		if (strpos($jevtask, "icalevent.edit") !== false || strpos($jevtask, "icalrepeat.edit") !== false)
		{
			$user = Factory::getUser();
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$authorisedonly = $params->get("authorisedonly", 0);
			if ($authorisedonly)
			{
				$jevuser = JEVHelper::getAuthorisedUser();
				if ($jevuser)
				{
					if ($jevuser->categories == "all")
					{
						$cats = array();
						foreach ($options as $opt)
						{
							$cats[] = $opt->value;
						}
					}
					else if ($jevuser->categories != "")
					{
						$cats = explode("|", $jevuser->categories);
					}
					else
					{
						if ($input->getInt("evid", 0) > 0)
						{
							// TODO - this should check the creator of the event
							$action = 'core.edit';
							$cats = $user->getAuthorisedCategories('com_jevents', $action);
							$action = 'core.edit.own';
							$cats = array_merge($cats, $user->getAuthorisedCategories('com_jevents', $action));
						}
						else
						{
							$action = 'core.create';
							$cats = $user->getAuthorisedCategories('com_jevents', $action);
						}
					}
				}
				else
				{
					if ($input->getInt("evid", 0) > 0)
					{
						// TODO - this should check the creator of the event
						$action = 'core.edit';
						$cats = $user->getAuthorisedCategories('com_jevents', $action);
						$action = 'core.edit.own';
						$cats = array_merge($cats, $user->getAuthorisedCategories('com_jevents', $action));
					}
					else
					{
						$action = 'core.create';
						$cats = $user->getAuthorisedCategories('com_jevents', $action);
					}
				}
			}
			else
			{
				if ($input->getInt("evid", 0) > 0)
				{
					// TODO - this should check the creator of the event
					$action = 'core.edit';
					$cats = $user->getAuthorisedCategories('com_jevents', $action);
					$action = 'core.edit.own';
					$cats = array_merge($cats, $user->getAuthorisedCategories('com_jevents', $action));
				}
				else
				{
					$action = 'core.create';
					$cats = $user->getAuthorisedCategories('com_jevents', $action);
				}
			}

			$app->triggerEvent('onGetAccessibleCategoriesForEditing', array(& $cats));

			// allow anon-user event creation through IF anon user event creation plugin is not enabled
			$plugin = PluginHelper::getPlugin("jevents", "jevanonuser");
			if (!$plugin || (!isset($user->id) && $user->id > 0))
			{
				$count = count($options);
				for ($o = 0; $o < $count; $o++)
				{
					if (!in_array($options[$o]->value, $cats))
					{
						unset($options[$o]);
					}
				}
				$options = array_values($options);
			}

			// Do we disable top level categories
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("blocktoplevelcategories", 0))
			{
				$count = count($options);
				for ($o = 0; $o < $count; $o++)
				{
					if ($options[$o]->level == 1)
					{
					    // Do not block if there is no child
					    if (!array_key_exists($options[$o]->value, $parents))
					        continue;
					    $options[$o]->disable = true;
					}
				}
			}
		}
		else
		{
			
		}
		// if only one category then preselect it but NOT when editing layouts
		if (count($options) == 1 && $jevtask !== "defaults.edit")
		{
			$catid = current($options)->value;
		}

		$gslclass = $skipgslclass ? '' : 'gsl-select';

		// sort categories alphabetically
		// usort($options, function($a, $b) { return strcmp($a->text,$b->text);});
		// should we offer multi-choice categories?
		// do not use jev_com_component incase we call this from locations etc.
		$params = ComponentHelper::getParams($input->getCmd("option", "com_jevents"));
		if ($allowMultiCat || ($eventediting && $params->get("multicategory", 0)))
		{
			$size = count($options) > 6 ? 6 : count($options) + 1;
			?>
			<label class="sr-only " for="<?php echo $fieldname;?>"><?php echo Text::_('JEV_CATEGORY_SELECT_LBL'); ?></label>
			<select name="<?php echo $fieldname; ?>[]"  id="<?php echo $fieldname; ?>" <?php echo $args; ?> multiple="multiple" class="<?php echo $gslclass;?>" size="<?php echo $size; ?>" style="width:300px;">
			    <?php
		    }
		    else
		    {
			    ?>
			    <label class="sr-only " for="<?php echo $fieldname;?>"><?php echo Text::_('JEV_CATEGORY_SELECT_LBL'); ?></label>
			    <select name="<?php echo $fieldname; ?>" class="<?php echo $gslclass;?>"  <?php echo $args; ?>  id="<?php echo $fieldname; ?>" >
				<option value="" <?php echo $catid == "" ? 'selected="true" ' : '';?> ><?php echo $t_first_entry; ?></option>
				<?php
			}
            // Get options from the parameters
            $optionParameters['option.key']     = 'value';
            $optionParameters['option.text']    = 'text';
			$optionParameters['option.attr']    = 'attr';
            $optionParameters['list.select']    = $catid;
            $optionParameters['list.translate'] = false;

			?>
			<?php echo HTMLHelper::_('select.options', $options, $optionParameters); ?>
		    </select>
		    <?php
		    $html = ob_get_clean();
		    if (count($options) == 1  && $jevtask !== "defaults.edit")
		    {
			$html   =  "<div class='catname'>".  $options[0]->text. "</div><div style='display:none'>" . $html . "</div>";
			// this doesn't work because it throws JS error on showon!
			//$html  .= "<input type='hidden' id='" . $fieldname . "' name='" . $fieldname . "[]' value='$catid' />";
		    }
		return $html;
	    }

	    public static function buildWeekDaysCheck($reccurweekdays, $args, $name = "reccurweekdays")
	    {

		    // get array
		    $day_name = JEVHelper::getWeekdayLetter(null, 1);
		    $day_name[0] = '<span class="sunday">' . $day_name[0] . '</span>';
		    $day_name[6] = '<span class="saturday">' . $day_name[6] . '</span>';

		    $tosend = '';

		    if ($reccurweekdays === '')
		    {
			    $split = array();
			    $countsplit = 0;
		    }
		    else
		    {
			    $split = explode('|', $reccurweekdays);
			    $countsplit = count($split);
		    }

		    $cfg                = JEVConfig::getInstance();
    		$offset             = $cfg->get("com_starday", 1);

		    for ($a = 0; $a < 7; $a++)
		    {
                $d = $a + $offset;
                $d %= 7;
			    $checked = '';
			    for ($x = 0; $x < $countsplit; $x++)
			    {
				    if ($split[$x] == $d)
				    {
					    $checked = ' checked="checked"';
				    }
			    }
			    $tosend .= ''
				    . '<input type="checkbox" id="cb_wd' . $d . '" name="' . $name . '[]" value="'
				    . $d . '" ' . $args . $checked . ' onclick="updateRepeatWarning();" class="checkbox " />'
				    . '<label for="cb_wd' . $d . '" class="checkbox btn">'
				    . $day_name[$d]
				    . '</label>' . "\n"
			    ;
		    }
		    echo $tosend;
	    }

	    public static function buildWeekDaysCheckUikit($reccurweekdays, $args, $name = "reccurweekdays")
	    {

		    // get array
		    $day_name = JEVHelper::getWeekdayLetter(null, 1);
		    $day_name[0] = '<span class="sunday">' . $day_name[0] . '</span>';
		    $day_name[6] = '<span class="saturday">' . $day_name[6] . '</span>';

		    $tosend = '';

		    if ($reccurweekdays === '')
		    {
			    $split = array();
			    $countsplit = 0;
		    }
		    else
		    {
			    $split = explode('|', $reccurweekdays);
			    $countsplit = count($split);
		    }

		    $cfg                = JEVConfig::getInstance();
    		$offset             = $cfg->get("com_starday", 1);

		    for ($a = 0; $a < 7; $a++)
		    {
                $d = $a + $offset;
                $d %= 7;
			    $checked = '';
			    for ($x = 0; $x < $countsplit; $x++)
			    {
				    if ($split[$x] == $d)
				    {
					    $checked = ' checked="checked"';
				    }
			    }
			    $tosend .= ''
				    . '<input type="checkbox" id="cb_wd' . $d . '" name="' . $name . '[]" value="'
				    . $d . '" ' . $args . $checked . ' onclick="updateRepeatWarning();" class="gsl-hidden " data-activeclass="primary"/>'
				    . '<label for="cb_wd' . $d . '" class="gsl-button gsl-button-small ' . (empty($checked) ? "gsl-button-default" : "gsl-button-default  gsl-button-primary") .'">'
				    . $day_name[$d]
				    . '</label>' . "\n"
			    ;
		    }
		    echo $tosend;
	    }

	    public static function buildWeeksCheck($reccurweeks, $args, $name = "reccurweeks", $direction = 0)
	    {
		    // language check
		    if (Text::_('JEV_FIRST') !== "JEV_FIRST")
		    {
			    $week_name = array('',
				Text::_('JEV_FIRST'),
				Text::_('JEV_SECOND'),
				Text::_('JEV_THIRD'),
				Text::_('JEV_FOURTH'),
				Text::_('JEV_FIFTH')
			    );
			    $backwards_week_name = array('',
				Text::_('JEV_LAST'),
				Text::_('JEV_SECOND_TO_LAST'),
				Text::_('JEV_THIRD_FROM_LAST'),
				Text::_('JEV_FOURTH_FROM_LAST'),
				Text::_('JEV_FIFTH_FROM_LAST')
			    );
		    }
		    else
		    {
			    $week_name = array('',
				Text::_('JEV_REP_WEEK') . ' 1 ',
				Text::_('JEV_REP_WEEK') . ' 2 ',
				Text::_('JEV_REP_WEEK') . ' 3 ',
				Text::_('JEV_REP_WEEK') . ' 4 ',
				Text::_('JEV_REP_WEEK') . ' 5 '
			    );
			    $backwards_week_name = $week_name;
		    }
		    $tosend = '';
		    $checked = '';

		    if ($reccurweeks == '')
		    {
			    $split = array();
			    $countsplit = 0;
		    }
		    else
		    {
			    $split = explode('|', $reccurweeks);
			    $countsplit = count($split);
		    }

		    for ($a = 1; $a < 6; $a++)
		    {
			    $checked = '';
			    if ($reccurweeks == '')
			    {
				    $checked = ' checked="checked"';
			    }

			    for ($x = 0; $x < $countsplit; $x++)
			    {
				    if ($split[$x] == $a)
				    {
					    $checked = ' checked="checked"';
				    }
			    }

			    // bootstrap version
			    $fwdstyle = $direction ? '' : 'style="display:none"';
			    $bckstyle = $direction ? 'style="display:none"' : '';

			    $tosend .= ''
				    . '<input type="checkbox" id="cb_wn' . $a . '" name="' . $name . '[]" value="'
				    . $a . '" ' . $args . $checked . ' onclick="updateRepeatWarning();" />'
				    . '<label for="cb_wn' . $a . '" class="checkbox btn">'
				    . '<span class="weeknameforward" ' . $fwdstyle . '>' . $week_name[$a] . "</span>"
				    . '<span class="weeknameback" ' . $bckstyle . '>' . $backwards_week_name[$a] . "</span>"
				    . '</label>' . "\n"
			    ;
		    }
		    echo $tosend;
	    }

	    public static function buildWeeksCheckUikit($reccurweeks, $args, $name = "reccurweeks", $direction = 0)
	    {
		    // language check
		    if (Text::_('JEV_FIRST') !== "JEV_FIRST")
		    {
			    $week_name = array('',
				Text::_('JEV_FIRST'),
				Text::_('JEV_SECOND'),
				Text::_('JEV_THIRD'),
				Text::_('JEV_FOURTH'),
				Text::_('JEV_FIFTH')
			    );
			    $backwards_week_name = array('',
				Text::_('JEV_LAST'),
				Text::_('JEV_SECOND_TO_LAST'),
				Text::_('JEV_THIRD_FROM_LAST'),
				Text::_('JEV_FOURTH_FROM_LAST'),
				Text::_('JEV_FIFTH_FROM_LAST')
			    );
		    }
		    else
		    {
			    $week_name = array('',
				Text::_('JEV_REP_WEEK') . ' 1 ',
				Text::_('JEV_REP_WEEK') . ' 2 ',
				Text::_('JEV_REP_WEEK') . ' 3 ',
				Text::_('JEV_REP_WEEK') . ' 4 ',
				Text::_('JEV_REP_WEEK') . ' 5 '
			    );
			    $backwards_week_name = $week_name;
		    }
		    $tosend = '';
		    $checked = '';

		    if ($reccurweeks == '')
		    {
			    $split = array();
			    $countsplit = 0;
		    }
		    else
		    {
			    $split = explode('|', $reccurweeks);
			    $countsplit = count($split);
		    }

		    for ($a = 1; $a < 6; $a++)
		    {
			    $checked = '';
			    if ($reccurweeks == '')
			    {
				    $checked = ' checked="checked"';
			    }

			    for ($x = 0; $x < $countsplit; $x++)
			    {
				    if ($split[$x] == $a)
				    {
					    $checked = ' checked="checked"';
				    }
			    }

			    // bootstrap version
			    $fwdstyle = $direction ? '' : 'style="display:none"';
			    $bckstyle = $direction ? 'style="display:none"' : '';

			    $tosend .= ''
				    . '<input type="checkbox" id="cb_wn' . $a . '" name="' . $name . '[]" value="'
				    . $a . '" ' . $args . $checked . ' onclick="updateRepeatWarning();" class="gsl-hidden"  data-activeclass="primary"/>'
				    . '<label for="cb_wn' . $a . '"  class="gsl-button gsl-button-small ' . (empty($checked) ? "gsl-button-default" : "gsl-button-primary") .'">'
				    . '<span class="weeknameforward" ' . $fwdstyle . '>' . $week_name[$a] . "</span>"
				    . '<span class="weeknameback" ' . $bckstyle . '>' . $backwards_week_name[$a] . "</span>"
				    . '</label>' . "\n"
			    ;
		    }
		    echo $tosend;
	    }

	    public static function getUserMailtoLink($evid, $userid, $admin = false, $event = null)
	    {

		    $db = Factory::getDbo();

		    static $arr_userids;
		    static $arr_evids;

		    $cfg = JEVConfig::getInstance();

		    if (!$arr_userids)
		    {
			    $arr_userids = array();
		    }
		    if (!$arr_evids)
		    {
			    $arr_evids = array();
		    }


		    $agenda_viewmail = $cfg->get('com_mailview');
		    $agenda_viewmail |= $admin;

		    if ($userid)
		    {
			    if (!isset($arr_userids[$userid]))
			    {
				    $params = ComponentHelper::getParams(JEV_COM_COMPONENT);
				    $userdet = JEVHelper::getContact($userid);

				    $contactlink = "";
				    if ($userdet)
				    {
					    if (isset($userdet->slug) && $userdet->slug && $agenda_viewmail == '1')
					    {
						    $contactlink = Route::_('index.php?option=com_contact&view=contact&id=' . $userdet->slug . '&catid=' . $userdet->catslug);
						    $contactlink = '<a href="' . $contactlink . '" title="' . Text::_('JEV_EMAIL_TO_AUTHOR') . '" target="_blank" >' . $userdet->contactname . '</a>';
					    }
					    else if ($userdet->email && $agenda_viewmail == '1')
					    {
						    //$contactlink = '<a href="mailto:' . $userdet->email
						    //. '" title="' . Text::_('JEV_EMAIL_TO_AUTHOR') . '">'
						    //. $userdet->username . '</a>';
						    if ($params->get('contact_display_name', 0) == 1)
						    {
							    $contactlink = HTMLHelper::_('email.cloak', $userdet->email, 1, $userdet->name, 0);
						    }
						    else
						    {
							    $contactlink = HTMLHelper::_('email.cloak', $userdet->email, 1, $userdet->username, 0);
						    }
					    }
					    else
					    {
						    if ($params->get('contact_display_name', 0) == 1)
						    {
							    $contactlink = $userdet->name;
						    }
						    else
						    {
							    $contactlink = $userdet->username;
						    }
					    }
				    }
				    $arr_userids[$userid] = $contactlink;
			    }
			    return $arr_userids[$userid];
		    }
		    else
		    {
			    if (!isset($arr_evids[$evid]))
			    {
				    $contactlink = Text::_('JEV_ANONYME');
				    $anonplugin = PluginHelper::getPlugin("jevents", "jevanonuser");
				    if ($anonplugin)
				    {
					    $db = Factory::getDbo();
					    $db->setQuery("SELECT a.* FROM #__jev_anoncreator as a LEFT JOIN #__jevents_repetition as r on a.ev_id=r.eventid where r.rp_id=" . intval($evid) . " LIMIT 1");
					    $anonrow = $db->loadObject();
					    if ($anonrow)
					    {
						    if ($agenda_viewmail == '1')
						    {
							    $contactlink = HTMLHelper::_('email.cloak', $anonrow->email, 1, $anonrow->name, 0);
						    }
						    else
						    {
							    $contactlink = $anonrow->name;
						    }
					    }
				    }
				    $arr_evids[$evid] = $contactlink;
			    }
			    return $arr_evids[$evid];
		    }

		    return '?';
	    }

	    /**
	     * returns name of the day longversion
	     *
	     * @param	daynb		int		# of day
	     * @param	colored		bool	color sunday	[ new mic, because inside tooltips a color forces an error! ]
	     * */
	    public static function getDayName($daynb, $colored = false)
	    {

		    $i = $daynb % 7; // modulo 7
		    if ($i == '0' && $colored === true)
		    {
			    $dayname = '<span class="sunday">' . JEVHelper::getDayName($i) . '</span>';
		    }
		    else if ($i == '6' && $colored === true)
		    {
			    $dayname = '<span class="saturday">' . JEVHelper::getDayName($i) . '</span>';
		    }
		    else
		    {
			    $dayname = JEVHelper::getDayName($i);
		    }
		    return $dayname;
	    }

	    public static function getColorBar($event_id = null, $newcolor = '')
	    {
		    $db = Factory::getDbo();

		    $cfg = JEVConfig::getInstance();

		    if ($event_id != null)
		    {
			    $query = "SELECT color_bar"
				    . "\n FROM #__events"
				    . "\n WHERE id = '$event_id'"
			    ;
			    $db->setQuery($query);
			    $rows = $db->loadResultList();

			    $row = $rows[0];

			    if ($newcolor)
			    {
				    if ($newcolor <> $row->color_bar)
				    {
					    $query = "UPDATE #__events"
						    . "\n SET color_bar = '$newcolor'"
						    . "\n WHERE id = '$event_id'"
					    ;
					    $db->setQuery($query);

					    return $newcolor;
				    }
			    }
			    else
			    {
				    return $row->color_bar;
			    }
		    }
		    else
		    {
			    // dmcd May 20/04  check the new config parameter to see what the default
			    // color should be
			    switch ($cfg->get('com_defColor'))
			    {
				    case 'none':
					    return '';

				    case 'category':
					    // fetch the category color for this event?
					    // Note this won't work for a new event since
					    // the user can change the category on-the-fly
					    // in the event entry form.  We need to dump a
					    // javascript array of all the category colors
					    // into the event form so the color can track the
					    // chosen category.
					    return '';

				    case 'random':
				    default:
					    $event_id = rand(1, 50);
					    // BAR COLOR GENERATION
					    //$start_publish = JevDate::mktime (0, 0, 0, date("m"),date("d"),date("Y"));
					    //$colorgenerate = intval(($start_publish/$event_id));
					    //$bg1color = StringHelper::substr($colorgenerate, 5, 1);
					    //$bg2color = StringHelper::substr($colorgenerate, 3, 1);
					    //$bg3color = StringHelper::substr($colorgenerate, 7, 1);
					    $bg1color = rand(0, 9);
					    $bg2color = rand(0, 9);
					    $bg3color = rand(0, 9);
					    $newcolorgen = '#' . $bg1color . 'F' . $bg2color . 'F' . $bg3color . 'F';

					    return $newcolorgen;
			    }
		    }
	    }

	    /*	     * ************ Date format ******************
	     *       case "0":
	     *            // Fr style : Monday 23 Juillet 2003
	     *            // Us style : Monday, Juillet 23 2003
	     *       case "1":
	     *            // Fr style : 23 Juillet 2003
	     *            // Us style : Juillet 23, 2003
	     *       case "2":
	     *    	 // Fr style : 23 Juillet
	     *            // Us style : Juillet, 23
	     *       case "3":
	     *    	 // Fr style : Juillet 2003
	     *            // Us style : Juillet 2003
	     *       case "4":
	     *            // Fr style : 23/07/2003
	     *            // Us style : 07/23/2003
	     *       case "5":
	     *            // Fr style : 23/07
	     *            // Us style : 07/23
	     *       case "6":
	     *            // Fr style : 07/2003
	     *            // Us style : 07/2003
	     * ****************************************** */
	    public static function getDateFormat($year, $month, $day, $type)
	    {
		    // Transform to translation strings
		    if (empty($year))
		    {
			    $year = 0;
		    }

		    if (empty($month))
		    {
			    $month = 0;
		    }

		    if (empty($day))
		    {
			    $day = 1;
		    }

		    static $format_type;
		    if (!isset($format_type))
		    {
			    $cfg = JEVConfig::getInstance();
			    $format_type = $cfg->get('com_dateformat');
		    }
		    $datestp = ( JevDate::mktime(0, 0, 0, $month, $day, $year));

		    // if date format is from langauge file then do this first
		    if ($format_type == 3 && is_numeric($type))
		    {
			    return JEV_CommonFunctions::jev_strftime(Text::_("DATE_FORMAT_" . $type), $datestp);
		    }

		    switch ($type)
		    {
			    case '0':
				    if ($format_type == 0)
				    {
					    return JEV_CommonFunctions::jev_strftime("%A %d %B %Y", $datestp);
					    // Fr style : Monday 03 Juillet 2003
				    }
				    elseif ($format_type == 1)
				    {
					    return JEV_CommonFunctions::jev_strftime("%A, %B %d, %Y", $datestp);
					    // Us style : Monday, July 03, 2003
				    }
				    else
				    {
					    //return JevDate::strftime("%A, %e. %B %Y",$datestp);
					    // %e not supported by windows
					    return sprintf(JEV_CommonFunctions::jev_strftime("%A, %d. %B %Y", $datestp), intval(JEV_CommonFunctions::jev_strftime('%d', $datestp)));
					    // De style : Montag, 3. Juli 2003
				    }
				    break;

			    case '1':
				    if ($format_type == 0)
				    {
					    return JEV_CommonFunctions::jev_strftime("%d %B %Y", $datestp);
					    // Fr style : 23 Juillet 2003
				    }
				    elseif ($format_type == 1)
				    {
					    return JEV_CommonFunctions::jev_strftime("%B %d, %Y", $datestp);
					    // Us style : July 23, 2003
				    }
				    else
				    {
					    return JEV_CommonFunctions::jev_strftime("%d. %B %Y", $datestp);
					    // De style : 23. Juli 2003
				    }
				    break;

			    case '2':
				    if ($format_type == 0)
				    {
					    return JEV_CommonFunctions::jev_strftime("%d %B", $datestp);
					    // Fr style : 23 Juillet
				    }
				    elseif ($format_type == 1)
				    {
					    return JEV_CommonFunctions::jev_strftime("%B %d", $datestp);
					    // Us style : Juillet 23
				    }
				    else
				    {
					    return JEV_CommonFunctions::jev_strftime("%d. %B", $datestp);
					    // De style : 23. Juli
				    }
				    break;

			    case '3':
				    if ($format_type == 0)
				    {
					    return JEV_CommonFunctions::jev_strftime("%B %Y", $datestp);
					    // Fr style : Juillet 2003
				    }
				    elseif ($format_type == 1)
				    {
					    return JEV_CommonFunctions::jev_strftime("%B, %Y", $datestp);
					    // Us style : Juillet, 2003
				    }
				    else
				    {
					    return JEV_CommonFunctions::jev_strftime("%B %Y", $datestp);
					    // De style : Juli 2003
				    }
				    break;

			    case '4':
				    if ($format_type == 0)
				    {
					    return JEV_CommonFunctions::jev_strftime("%d/%m/%Y", $datestp);
					    // Fr style : 23/07/2003
				    }
				    elseif ($format_type == 1)
				    {
					    return JEV_CommonFunctions::jev_strftime("%m/%d/%Y", $datestp);
					    // Us style : 07/23/2003
				    }
				    else
				    {
					    return JEV_CommonFunctions::jev_strftime("%d.%m.%Y", $datestp);
					    // De style : 23.07.2003
				    }
				    break;

			    case '5':
				    if ($format_type == 0)
				    {
					    return JEV_CommonFunctions::jev_strftime("%d/%m", $datestp);
					    // Fr style : 23/07
				    }
				    elseif ($format_type == 1)
				    {
					    return JEV_CommonFunctions::jev_strftime("%m/%d", $datestp);
					    // Us style : 07/23
				    }
				    else
				    {
					    return JEV_CommonFunctions::jev_strftime("%d.%m.", $datestp);
					    // De style : 23.07.
				    }
				    break;

			    case '6':
				    if ($format_type == 0)
				    {
					    return JEV_CommonFunctions::jev_strftime("%m/%Y", $datestp);
					    // Fr style : 07/2003
				    }
				    elseif ($format_type == 1)
				    {
					    return JEV_CommonFunctions::jev_strftime("%m/%Y", $datestp);
					    // Us style : 07/2003
				    }
				    else
				    {
					    return JEV_CommonFunctions::jev_strftime("%m/%Y", $datestp);
					    // De style : 07/2003
				    }
				    break;

			    case '7':
				    if ($format_type == 0)
				    {
					    return JEV_CommonFunctions::jev_strftime("%A, %d", $datestp);
					    // Fr style : Monday 23
				    }
				    elseif ($format_type == 1)
				    {
					    return JEV_CommonFunctions::jev_strftime("%A, %d", $datestp);
					    // Us style : Monday, 23
				    }
				    else
				    {
					    return JEV_CommonFunctions::jev_strftime("%A, %d.", $datestp);
					    // De style : Montag, 23.
				    }
				    break;

			    default:
				    // in this case $type is a format!
				    return JEV_CommonFunctions::jev_strftime($type, $datestp);
				    break;
		    }
		    return $newdate;
	    }

	    /**
	     * Convert special characters to html entities
	     * Required for edit fields containing html code
	     *
	     * @static
	     *
	     * @param $html	string	html text
	     *
	     * @return		string	html string
	     */
	    public static function special($html = '')
	    {

		    return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
	    }

	    /**
	     * Generate javascript start and end tags
	     *
	     * @access public
	     *
	     * @param string $type 'start' or 'end' tag
	     *
	     * @return string html sequence
	     */
	    public static function buildScriptTag($type = 'start')
	    {

		    $html = "";
		    switch ($type)
		    {

			    case 'start':
				    $html = "\n" . '<script type="text/javascript">' . "\n"
					    . "/* <![CDATA[ */\n"
					    . "// inserted by JEvents\n";
				    break;
			    case 'end':
				    $html = "\n" . '/* ]]> */' . "\n"
					    . '</script>' . "\n";
				    break;
			    default;
				    $html = '<!-- wrong javascript tag parameter-->' . "\n";
		    }
		    return $html;
	    }

	    public static function buildAccessSelect($access, $attribs = 'class="inputbox" onchange="this.form.submit()"', $text = "", $fieldname = "access")
	    {
		    $assetGroups = HTMLHelper::_('access.assetgroups');
		    // only offer access levels the user has access to
            $access  = (int) $access;
            if($access === 0) {
                // Set default config value
                $config         = Factory::getConfig();
                $access          = (int) $config->get('access', 1); // 1 = public.
            }

		    $user = Factory::getUser();

		    $access  = (int) $access;
            if($access === 0) {
                // Set default config value
                $config         = Factory::getConfig();
                $access          = (int) $config->get('access', 1); // 1 = public.
            }

		    if (!$user->get("isRoot", 0))
		    {
			    $viewlevels = $user->getAuthorisedViewLevels();
			    foreach ($assetGroups as $i => $level)
			    {
				    if (!in_array($level->value, $viewlevels))
				    {
					    unset($assetGroups[$i]);
				    }
			    }
			    $assetGroups = array_values($assetGroups);
		    }
		    if (count($assetGroups) == 1)
		    {
			    return "<input type='hidden' name='" . $fieldname . "' value='" . $assetGroups[0]->value . "'/>";
		    }
		    ob_start();
		    ?>
		    <select name="<?php echo $fieldname; ?>" <?php echo $attribs; ?> id="<?php echo $fieldname; ?>" >
			<?php
			if ($text != "")
			{
				?>
				<option value=""><?php echo $text; ?></option>
				<?php
			}
			echo HTMLHelper::_('select.options', $assetGroups, 'value', 'text', $access);
			?>
		    </select>
		    <?php
		    return ob_get_clean();
	    }

    }
    
