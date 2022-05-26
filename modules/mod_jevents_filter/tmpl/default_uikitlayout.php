<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: default_layout.php 3323 2012-03-08 13:37:46Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module JEvents Filter
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.gwesystems.com
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;

if (count($filterHTML) > 0)
{
	ob_start();

	JEVHelper::script("mod_jevents_filter.js", "modules/mod_jevents_filter/", false);
	?>
	<form action="<?php echo $form_link; ?>" id="jeventspost" name="jeventspost<?php echo $module->id; ?>" method="post"
	      class="jevfiltermodule">
		<input type='hidden' name='catids' id='catidsfv' value='<?php echo trim($datamodel->catidsOut); ?>'/>
		<input type='hidden' name='option' value='com_jevents'/>
		<?php
		// This forces category settings in URL to reset too since they could be set by SEF
		$script = "try {JeventsFilters.filters.push({id:'catidsfv',value:0});} catch (e) {}\n";

		$document = Factory::getDocument();
		$document->addScriptDeclaration($script);
		$style = <<<STYLE
.jevfiltertable, .jevfiltertable td, .jevfiltertable tr {
	border:none;
}
.jevfiltertable td {
	vertical-align:top;
	padding-bottom:4px;
}
.jevfilterlist, .jevfilterfloatlist {
	list-style-type: none;
	display:block;
	margin-left:0px
}
.jevfilterlist .jevfilterinput .chzn-container, .jevfilterlist .jevfilterinput input {
	max-width:100%;
}
   .jevfilterfloatlist li {
	float:left;
	margin-right:5px;
}
.jevfilterfloatlist li li {
	float:none;
}
STYLE;
		$document->addStyleDeclaration($style);
		switch ($params->get("filterlayout", "vt"))
		{
			case "vt":
				?>
				<table class="jevfiltertable uk-table">
					<?php
					$hasreset = false;
					foreach ($filterHTML as $filter)
					{
						if (!isset($filter["title"]))
						{
							continue;
						}
						?>
						<tr>
							<?php
							if (StringHelper::strlen($filter["title"]) > 0 && $params->get("showlabels", 1))
							{
								?>
								<td><?php echo $filter["title"]; ?></td>
								<?php
							}
							else if ($params->get("showlabels", 1))
							{
								?>
								<td/>
								<?php
							}
							?>
							<td><?php echo $filter["html"] ?></td>
						</tr>
						<?php
						if (strpos($filter["html"], 'filter_reset') > 0)
						{
							$hasreset = true;
						}
					}
					?>
					<tr>
						<td colspan="<?php echo $params->get("showlabels", 1) ? 2 : 1;?> ?>">
							<div class="uk-button uk-button-group">
								<input class="modfilter_button uk-button uk-button-secondary" type="button" onclick="JeventsFilters.reset(this.form)"
							           value="<?php echo Text::_('RESET'); ?>"/>
								<input class="modfilter_button uk-button uk-button-primary" type="submit" value="<?php echo Text::_('ok'); ?>"
								       name="jevents_filter_submit"/>
							</div>
						</td>
					</tr>
					</tr>
				</table>
				<?php
				break;

			case "ht":
				?>
				<table class="jevfiltertable uk-table">
					<tr>
						<?php
						$hasreset = false;
						foreach ($filterHTML as $filter)
						{
							if (!isset($filter["title"]))
							{
								continue;
							}
							if (StringHelper::strlen($filter["title"]) > 0 && $params->get("showlabels", 1))
							{
								?>
								<td><?php echo $filter["title"]; ?></td>
								<?php
							}
							else if ($params->get("showlabels", 1))
							{
								?>
								<td/>
								<?php
							}
							?>

							<?php
							if (strpos($filter["html"], 'filter_reset') > 0)
							{
								$hasreset = true;
							}
						}
						if ($params->get("showlabels", 1))
						{
							?>
							<td><input class="modfilter_button uk-button uk-button-secondary" type="button" onclick="JeventsFilters.reset(this.form)"
							           value="<?php echo Text::_('RESET'); ?>"/></td>
						<?php } ?>
					</tr>
					<tr>
						<?php
						foreach ($filterHTML as $filter)
						{
							if (!isset($filter["title"]))
							{
								continue;
							}
							?>
							<td><?php echo $filter["html"] ?></td>
							<?php
							if (strpos($filter["html"], 'filter_reset') > 0)
							{
								$hasreset = true;
							}
						}
						?>
						<td colspan="<?php echo $params->get("showlabels", 1) ? 2 : 1;?> ?>">
							<div class="uk-button uk-button-group">
								<input class="modfilter_button uk-button uk-button-secondary" type="button" onclick="JeventsFilters.reset(this.form)"
							           value="<?php echo Text::_('RESET'); ?>"/>
								<input class="modfilter_button uk-button uk-button-primary" type="submit" value="<?php echo Text::_('ok'); ?>"
								       name="jevents_filter_submit"/>
							</div>
						</td>
					</tr>
				</table>

				<?php
				break;

			case "ul":
			case "ful":
				?>
				<ul class="<?php echo $params->get("filterlayout", "vt") == "ul" ? "jevfilterlist" : "jevfilterfloatlist"; ?> uk-list">
					<?php
					$hasreset = false;
					foreach ($filterHTML as $filter)
					{
						if (!isset($filter["title"]))
						{
							continue;
						}
						?>
						<li>
							<?php
							if (StringHelper::strlen($filter["title"]) > 0 && $params->get("showlabels", 1))
							{
								?>
								<?php echo $filter["title"]; ?>
								<?php
							}
							else if ($params->get("showlabels", 1))
							{
								?>
								<label>&nbsp;</label>
								<?php
							}
							?>
							<div class="jevfilterinput">
								<?php echo $filter["html"] ?>
							</div>
						</li>
						<?php
						if (strpos($filter["html"], 'filter_reset') > 0)
						{
							$hasreset = true;
						}
					}
					?>
					<li>
						<div class="jevfilterinput uk-button uk-button-group">
							<input class="modfilter_button uk-button uk-button-secondary" type="button" onclick="JeventsFilters.reset(this.form)"
							       value="<?php echo Text::_('RESET'); ?>"/>
							<input class="modfilter_button uk-button uk-button-primary" type="submit" value="<?php echo Text::_('ok'); ?>"
							       name="jevents_filter_submit"/>
						</div>
					</li>
				</ul>
				<?php
				if ($params->get("filterlayout", "vt") == "ful")
				{
					echo "<div style='clear:left'></div>";
				}
				break;

			default:
				$hasreset = false;
				break;
		}
		if (!$hasreset)
		{
			echo "<input type='hidden' name='filter_reset' id='filter_reset' value='0' />";
		}
		?>
	</form>
	<?php
	$output = ob_get_clean();
	$output = str_replace(array("gsl-", "evsearch", "inputbox") , array("uk-","evsearch uk-input","uk-input"), $output);
	echo $output;
}
