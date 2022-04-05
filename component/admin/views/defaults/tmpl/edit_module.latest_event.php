<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit_icalevent.list_row.php 3333 2012-03-12 09:36:35Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

include_once('edit_icalevent.list_row.php');

// Lazy! close the tag from the edit.php and then open a new unclosed one!
?>
</div>
	<div class="form-group selectlayout gsl-width-1-4@m">
		<label for="published">Select Layout</label>
<?php
JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");

$target = 'value';
$csstarget = 'customcss';
$jstarget = 'customjs';
$ttop = 'params[header]';
$tbot = 'params[footer]';
$version = JEventsVersion::getInstance();
$release = $version->get("RELEASE", "1.0.0");
HTMLHelper::script("https://www.jevents.net/jevlayouts/module.latest_event.js?$release");

$html   =  "<script>jQuery(document).ready(function ($){loadJevPreview('$target', '$csstarget', '$jstarget', '$ttop',  '$tbot');});</script>";
$id     = $this->item->id;

if (version_compare(JVERSION, "4", "gt")) {
    $html .= <<<DROPDOWN
		<div class="dropdown btn-group" id="$id">
				  <button type="button" id="dropdown$target" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			Select Layout
			<span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdown$target" id="dropdownUL_$target" role="menu">
			<li role="presentation"><a role="menuitem" class="dropdownpopover" href="#" data-title="Current Customised Value" data-content="Custom Format String customised by you">Current Value</a></li>
			</ul>
		</div>
	DROPDOWN;
} else {
    $html .= <<<DROPDOWN
		<div class="dropdown btn-group" id="$id">
		  <button class="btn btn-default dropdown-toggle" type="button" id="dropdown$target" data-toggle="dropdown" aria-expanded="false">
			Select Layout
			<span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdown$target" id="dropdownUL_$target" role="menu">
			<li role="presentation"><a role="menuitem" class="dropdownpopover" href="#" data-title="Current Customised Value" data-content="Custom Format String customised by you">Current Value</a></li>
			</ul>
		</div>
	DROPDOWN;
}

// Do we need special parameters from the theme - just float for the time being
$layoutName = $this->item->name;
$moduleId = str_replace(array("module.latest_event", "."), "",$layoutName);
if (!empty($moduleId) && intval($moduleId) > 0)
{
	$moduleId = intval($moduleId);
	$db = Factory::getDbo();

	$query = $db->getQuery(true)
		->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params')
		->from('#__modules AS m')
		->where('m.client_id = 0')
		->where('m.id = ' . $moduleId);

	$db->setQuery($query);
	$module = $db->loadObject();

	if ($module)
	{
		ob_start()
		?>
		</div>
		<div class="form-group column-layout gsl-width-1-4@m">
			<label for="published"><?php echo Text::_("FLOAT_COLUMNS"); ?></label>
			<?php
			$params = is_string($this->item->params) ? json_decode($this->item->params) : $this->item->params;
			$columnsL = isset($params->columnsL) ? $params->columnsL : 3;
			$poptions   = array();
			$poptions[] = HTMLHelper::_('select.option', 1, Text::_("FLOAT_COLUMNS_1"));
			$poptions[] = HTMLHelper::_('select.option', 2, Text::_("FLOAT_COLUMNS_2"));
			$poptions[] = HTMLHelper::_('select.option', 3, Text::_("FLOAT_COLUMNS_3"));
			$poptions[] = HTMLHelper::_('select.option', 4, Text::_("FLOAT_COLUMNS_4"));
			$poptions[] = HTMLHelper::_('select.option', 5, Text::_("FLOAT_COLUMNS_5"));
			echo HTMLHelper::_('select.genericlist', $poptions, 'params[columnsL]', 'class=" "', 'value', 'text', $columnsL);
			?>
		</div>
		<div>
		<?php
		$html .= ob_get_clean();

	}
}
echo $html;