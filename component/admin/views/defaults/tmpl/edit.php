<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit.php 2768 2011-10-14 08:43:42Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$jversion = new Joomla\CMS\Version;
if (!$jversion->isCompatible('4.0'))
{
	HTMLHelper::_('formbehavior.chosen', 'select');
}
jimport('joomla.filesystem.file');

if ($this->item->name == "month.calendar_cell" || $this->item->name == "month.calendar_tip" || $this->item->name == "icalevent.edit_page"  || $this->item->name == "icalevent.list_block3"  || $this->item->name == "icalevent.list_block4")
{
	$editor = Editor::getInstance("none");
}
else
{
	$editor = Factory::getConfig()->get('editor');
	$editor = Editor::getInstance($editor);
}

if (strpos($this->item->name, "com_") === 0)
{
	$lang  = Factory::getLanguage();
	$parts = explode(".", $this->item->name);
	$lang->load($parts[0]);
}

if ($this->item->value == "" && file_exists(dirname(__FILE__) . '/' . $this->item->name . ".3.html"))
	$this->item->value = file_get_contents(dirname(__FILE__) . '/' . $this->item->name . ".3.html");

if ($this->item->value == "" && file_exists(dirname(__FILE__) . '/' . $this->item->name . ".html"))
	$this->item->value = file_get_contents(dirname(__FILE__) . '/' . $this->item->name . ".html");

//Float layout check to load default value
if ($this->item->name == 'icalevent.list_block1' && $this->item->value == "" && Jfile::exists(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block1.html'))
{
	$this->item->value = file_get_contents(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block1.html');
}
if ($this->item->name == 'icalevent.list_block2' && $this->item->value == "" && Jfile::exists(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block2.html'))
{
	$this->item->value = file_get_contents(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block2.html');
}
if ($this->item->name == 'icalevent.list_block3' && $this->item->value == "" && Jfile::exists(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block3.html')) {
	$this->item->value = file_get_contents(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block3.html');
}
else if ($this->item->name == 'icalevent.list_block4' && $this->item->value == "" && Jfile::exists(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block4.html')) {
	$this->item->value = file_get_contents(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block4.html');
}


if (strpos($this->item->name, "module.") === 0)
{

	// Get the plugin
	if (JPluginHelper::getPlugin('editors', 'codemirror'))
	{
		$editor = Editor::getInstance("codemirror");
	}
	else
	{
		$editor = Editor::getInstance("none");
	}
}

if (strpos($this->item->name, "module.") === 0
	&& $this->item->value == ""
	&& file_exists(dirname(__FILE__) . '/' . preg_replace("#\.[0-9]+#", "", $this->item->name) . ".html")
)
{
	$this->item->value = file_get_contents(dirname(__FILE__) . '/' . preg_replace("#\.[0-9]+#", "", $this->item->name) . ".html");
}

$this->replaceLabels($this->item->value);

$templateparams = new stdClass();
if (isset($this->item->params) && !empty($this->item->params)) {
	$templateparams = @json_decode($this->item->params);
}
// is there custom css or js - if so push into the params
if (strpos($this->item->value, '{{CUSTOMJS}') !== false)
{
	preg_match('|' . preg_quote('{{CUSTOMJS}}') . '(.*?)' . preg_quote('{{/CUSTOMJS}}') . '|s', $this->item->value, $matches);

	if (count($matches) == 2)
	{
		$templateparams->customjs = $matches[1];
		$this->item->value = str_replace($matches[0], "",	$this->item->value);
	}
	else
	{
		$templateparams->customjs = "";
	}
}

if (strpos($this->item->value, '{{CUSTOMCSS}') !== false)
{
	preg_match('|' . preg_quote('{{CUSTOMCSS}}') . '(.*?)' . preg_quote('{{/CUSTOMCSS}}') . '|s', $this->item->value, $matches);

	if (count($matches) == 2)
	{
		$templateparams->customcss = $matches[1];
		$this->item->value = str_replace($matches[0], "",	$this->item->value);
	}
	else
	{
		$templateparams->customcss = "";
	}
}

// is there custom header or footer html - if so push into the params
if (strpos($this->item->value, '{{HTMLHEADER}') !== false)
{
	preg_match('|' . preg_quote('{{HTMLHEADER}}') . '(.*?)' . preg_quote('{{/HTMLHEADER}}') . '|s', $this->item->value, $matches);

	if (count($matches) == 2)
	{
		$templateparams->htmlheader = $matches[1];
		$this->item->value = str_replace($matches[0], "",	$this->item->value);
	}
}
if (strpos($this->item->value, '{{HTMLFOOTER}') !== false)
{
	preg_match('|' . preg_quote('{{HTMLFOOTER}}') . '(.*?)' . preg_quote('{{/HTMLFOOTER}}') . '|s', $this->item->value, $matches);

	if (count($matches) == 2)
	{
		$templateparams->htmlheader = $matches[1];
		$this->item->value = str_replace($matches[0], "",	$this->item->value);
	}
}

$this->item->params = json_encode($templateparams);

?>
<div id="jevents">
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="customlayouts">
		<div class="gsl-container gsl-container-expand">
			<div class="gsl-grid gsl-grid small">
				<div class="form-group gsl-width-1-4@m">
					<label for="title"><?php echo Text::_('TITLE'); ?>:</label>
					<input readonly class="inputbox form-control" type="text" id="title" size="50"
					       maxlength="100"
					       value="<?php echo htmlspecialchars(Text::_($this->item->title), ENT_QUOTES, 'UTF-8'); ?>"/>
				</div>
				<div class="form-group gsl-width-1-4@m">
					<label for="language"><?php echo Text::_('JFIELD_LANGUAGE_LABEL'); ?>:</label>
					<input readonly class="inputbox form-control" type="text" id="language" size="50"
					       maxlength="100"
					       value="<?php echo $this->item->language == "*" ? Text::alt('JALL', 'language') : $this->item->language; ?>"/>
				</div>
				<div class="form-group gsl-width-1-4@m">
					<label for="category"><?php echo Text::_('JCATEGORY'); ?>:</label>
					<!--
					<input readonly class="inputbox form-control" type="text" id="language" size="50"
					       maxlength="100"
					       value="<?php echo $this->item->catid == "0" ? Text::alt('JALL', 'language') : $this->item->category_title; ?>"/>
					<input type="hidden" name="catid" value="<?php echo $this->item->catid; ?>">
					       //-->
					<?php
					$catid = $this->item->catid == "0" ? "" :  $this->item->catid;
					$categorySelect = JEventsHTML::buildCategorySelect($catid, "", null, true, false, 0, 'catid');
					if (strpos($categorySelect, "<select "))
					{
						$categorySelect = str_replace("<select ", "<select onchange='if (confirm(\"" . Text::_("JEV_YOU_WILL_LOOSE_UNSAVED_CHANGES_WHEN_CHANGING_CATEGORY_DO_YOU_WISH_TO_CONTINUE", true). "\")) {this.form.submit();}' ", $categorySelect);
					}
					echo $categorySelect;
					//$catid, $args, $catidList = null, $with_unpublished = false, $require_sel = false, $catidtop = 0, $fieldname = "catid", $sectionname = JEV_COM_COMPONENT, $excludeid = false, $order = "ordering", $eventediting = false, $allowMultiCat = false
					?>
				</div>
				<div class="form-group gsl-width-1-4@m">
					<label for="name"><?php echo Text::_('NAME'); ?></label>
					<input readonly class="inputbox form-control" type="text" id="name" size="50"
					       maxlength="100"
					       value="<?php echo htmlspecialchars($this->item->name, ENT_QUOTES, 'UTF-8'); ?>"/>
				</div>
			</div>
			<div class="gsl-grid gsl-grid small">
				<div class="form-group jevpublished gsl-width-1-4@m">
					<label for="published"><?php echo Text::_("JSTATUS"); ?></label>
					<?php
					$poptions   = array();
					$poptions[] = HTMLHelper::_('select.option', 0, Text::_("JUNPUBLISHED"));
					$poptions[] = HTMLHelper::_('select.option', 1, Text::_("JPUBLISHED"));
					$poptions[] = HTMLHelper::_('select.option', -1, Text::_("JTRASHED"));
					echo HTMLHelper::_('select.genericlist', $poptions, 'state', 'class="inputbox form-control chzn-color-state"', 'value', 'text', $this->item->state);
					?>
				</div>
				<div class="form-group gsl-width-1-4@m">
					<?php
					$pattern   = "#.*([0-9]*).*#";
					$name      = preg_replace("#\.[0-9]+#", "", $this->item->name);
					$selectbox = $this->loadTemplate($name);
					echo $selectbox;
					?>
				</div>
			</div>
			<div class="gsl-grid gsl-grid small">
				<div class="form-group gsl-width-expand@m">
					<br>
					<label for="value"> <?php echo Text::_('JEV_LAYOUT'); ?></label>
				</div>
			</div>
			<div class="gsl-grid gsl-grid small">
				<div class="layouteditor">
					<?php
					// parameters : areaname, content, hidden field, width, height, rows, cols
					echo $editor->display('value', htmlspecialchars($this->item->value, ENT_QUOTES, 'UTF-8'), 700, 450, '70', '15', false);
					?>
				</div>
			</div>
		</div>

		<!-- Custom Module Form -->
		<?php
		if ($this->item->name != "month.calendar_tip" && strpos($this->item->name, "module.") === false && $this->item->name != "icalevent.edit_page" && strpos($this->item->name, "com_jevpeople") === false && strpos($this->item->name, "com_jevlocations") === false)
		{
			?>
			<div class="gsl-container gsl-container-expand">

				<div class="gsl-grid gsl-grid small">
					<h3><?php echo Text::_("JEV_DEFAULTS_CUSTOM_MODULES"); ?></h3>
				</div>
				<?php
				$params  = new JevRegistry($this->item->params);
				$modids  = $params->get("modid", array());
				$modvals = $params->get("modval", array());

				// Not sure how this can arise :(
				if (is_object($modvals))
				{
					$modvals = get_object_vars($modvals);
				}
				$modids  = array_values($modids);
				$modvals = array_values($modvals);

				$count     = 0;
				$conf      = Factory::getConfig();
				$modeditor = $editor;

				foreach ($modids as $modid)
				{
					if (trim($modid) == "")
					{
						$count++;
						continue;
					}
					?>
					<div class="gsl-grid gsl-grid small">
						<div class="form-group gsl-width-1-4@m">
							<label for="title"><?php echo Text::_('JEV_DEFAULTS_MODULE_ID'); ?>:</label>
							<input class="inputbox form-control" type="text" id="modid<?php echo $count; ?>" size="50"
							       maxlength="100" name="params[modid][]" value="<?php echo $modid ?>"/>
						</div>
						<div class="form-group gsl-width-3-4@m">
							<?php echo str_replace("value", "modval" . $count, str_replace("jevdefaults", "jevmods" . $count, $selectbox)); ?>
						</div>
					</div>
					<div class="gsl-grid gsl-grid small">
						<div class="form-group gsl-width-expand@m">
							<label for="title"><?php echo Text::_('JEV_DEFAULTS_MODULE_OUTPUT'); ?>:</label>
							<?php echo $modeditor->display('params[modval][' . $count . "]", htmlspecialchars($modvals[$count], ENT_QUOTES, 'UTF-8'), 700, 450, '70', '15', false, 'modval' . $count); ?>
						</div>
					</div>
					<div class="gsl-grid gsl-grid small">
						<hr/>
					</div>
					<?php
					$count++;
				}

				// Plus one extra one
				?>
				<div class="gsl-grid gsl-grid small">
					<div class="form-group gsl-width-1-4@m">
						<label for="title"><?php echo Text::_('JEV_DEFAULTS_MODULE_ID'); ?>:</label>
						<input class="inputbox form-control" type="text" id="modid<?php echo $count; ?>" size="50"
						       maxlength="100" name="params[modid][]"/>
					</div>
					<div class="form-group gsl-width-3-4@m">
						<?php echo str_replace("value", "modval" . $count, str_replace("jevdefaults", "jevmods" . $count, $selectbox)); ?>
					</div>
				</div>
				<div class="gsl-grid gsl-grid small">
					<div class="form-group gsl-width-expand@m">
						<label for="title"><?php echo Text::_('JEV_DEFAULTS_MODULE_OUTPUT'); ?>:</label>
						<?php echo $modeditor->display('params[modval][' . $count . "]", htmlspecialchars("", ENT_QUOTES, 'UTF-8'), 700, 450, '70', '15', false, 'modval' . $count); ?>
					</div>
				</div>
				<div class="gsl-grid gsl-grid small">
					<hr/>
				</div>
			</div>

		<?php
		}

		$params = new Registry($this->item->params);

		if (strpos($this->item->name,  "module." ) === 0 )
		{
			$headerhtml = $params->get("header", '');
			$footerhtml = $params->get("footer", '');

			?>
			<div class="gsl-container gsl-container-expand">
				<div class="gsl-grid gsl-grid small">
					<div class="form-group gsl-width-expand@m">
						<h3><?php echo Text::_("JEV_DEFAULTS_HTML_HEADER");?></h3>
						<?php
						echo $editor->display('params[header]', htmlspecialchars($headerhtml, ENT_QUOTES, 'UTF-8'), 600, 450, '70', '15', false);
						?>
					</div>
				</div>
				<div class="gsl-grid gsl-grid small">
					<div class="form-group gsl-width-expand@m">
						<h3><?php echo Text::_("JEV_DEFAULTS_HTML_FOOTER");?></h3>
						<?php
						echo $editor->display('params[footer]' , htmlspecialchars($footerhtml, ENT_QUOTES, 'UTF-8'), 600, 450, '70', '15', false);
						?>
					</div>
				</div>
			</div>
			<?php
		}

		// Custom CSS and Javascript
		$customcss = $params->get("customcss", '');
		$customjs = $params->get("customjs", '');

		?>
        <div class="gsl-container gsl-container-expand">
            <div class="gsl-grid gsl-grid small">
                <div class="form-group gsl-width-expand@m">
                <h3><?php echo Text::_("JEV_DEFAULTS_CUSTOM_CSS");?></h3>
                    <textarea id="customcss" name="params[customcss]"  class="gsl-width-expand@m  gsl-height-medium"><?php echo htmlspecialchars($customcss, ENT_QUOTES, 'UTF-8');?></textarea>
                </div>
            </div>
            <div class="gsl-grid gsl-grid small">
                <div class="form-group gsl-width-expand@m">
                <h3><?php echo Text::_("JEV_DEFAULTS_CUSTOM_JS");?></h3>
                    <textarea id="customjs" name="params[customjs]" class="gsl-width-expand@m gsl-height-medium"><?php echo htmlspecialchars($customjs, ENT_QUOTES, 'UTF-8');?></textarea>
                </div>
            </div>
        </div>

		<input type="hidden" name="name" value="<?php echo $this->item->name; ?>">
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
		<input type="hidden" name="language" value="<?php echo $this->item->language; ?>">
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="task" value="defaults.edit"/>
		<input type="hidden" name="act" value=""/>
		<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
	</form>
</div>
