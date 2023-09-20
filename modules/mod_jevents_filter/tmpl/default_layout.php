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
use Joomla\String\StringHelper;

if (count($filterHTML) > 0)
{

	JEVHelper::script("mod_jevents_filter.js", "modules/mod_jevents_filter/", false);
	?>
	<form action="<?php echo $form_link; ?>" id="jeventspost" name="jeventspost<?php echo $module->id; ?>" method="post"
	      class="jevfiltermodule">
		<input type='hidden' name='catids' id='catidsfv' value='<?php echo trim($datamodel->catidsOut); ?>'/>
		<input type='hidden' name='option' value='com_jevents'/>
		<?php
		// This forces category settings in URL to reset too since they could be set by SEF
		$script = "try {JeventsFilters.filters.push({id:'catidsfv',value:0});} catch (e) {}\n";

		$autoSubmitFilter = (int) $params->get("autosubmitonchange", 0);
		$script .= "var autoSubmitFilter = $autoSubmitFilter;\n";

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
				<table class="jevfiltertable">
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
						<td><input class="modfilter_button btn btn-secondary" type="button" onclick="JeventsFilters.reset(this.form)"
						           value="<?php echo Text::_('RESET'); ?>"/>
							<?php if ($params->get("showlabels", 1)) { ?>
						</td>
						<td>
							<?php } ?>
							<input class="modfilter_button  btn btn-primary" type="submit" value="<?php echo Text::_('ok'); ?>"
							       name="jevents_filter_submit"/></td>
					</tr>
				</table>
				<?php
				break;

			case "ht":
				?>
				<table class="jevfiltertable">
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
							<td><input class="modfilter_button btn btn-secondary" type="button" onclick="JeventsFilters.reset(this.form)"
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
						if (!$params->get("showlabels", 1))
						{
							?>
							<td><input class="modfilter_button btn btn-secondary" type="button" onclick="JeventsFilters.reset(this.form)"
							           value="<?php echo Text::_('RESET'); ?>"/></td>
						<?php } ?>
						<td><input class="modfilter_button btn btn-primary" type="submit" value="<?php echo Text::_('ok'); ?>"
						           name="jevents_filter_submit"/></td>
					</tr>
				</table>

				<?php
				break;

			case "ul":
			case "ful":
				?>
				<ul class="<?php echo $params->get("filterlayout", "vt") == "ul" ? "jevfilterlist" : "jevfilterfloatlist"; ?>">
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
						<?php if ($params->get("showlabels", 1)) { ?>
							<br>
						<?php } ?>
						<div class="jevfilterinput btn-group">
							<button class="modfilter_button btn btn-secondary" type="button" onclick="JeventsFilters.reset(this.form)">
								<?php echo Text::_('RESET'); ?></button>
							<button class="modfilter_button  btn btn-primary" type="submit" name="jevents_filter_submit">
								<?php echo Text::_('ok'); ?>
							</button>
						</div>
					</li>
				</ul>
				<?php
				if ($params->get("filterlayout", "vt") == "ful")
				{
					echo "<div style='clear:left'></div>";
				}
				break;

            case "custom":
				$HTML = $params->get("customlayout", "<p>{Search LBL}{Search}</p><p>{Category LBL}{Category}</p><p>{SUBMIT_BUTTON}</p><p>{RESET_BUTTON}</p>");
                $hasreset = false;
                foreach ($filterHTML as $filterId => $filter)
                {
                    if (empty($filterId))
                    {
                        continue;
                    }
					if ($filterId == "catid")
                    {
                        $filterId = "Category";
                    }
					$HTML = str_replace(array("{" . $filterId ."}", "{" . $filterId ." LBL}"), array($filter["html"], $filter["title"]), $HTML);
                }

                // second pass to deal with special case if custom field
                foreach ($filterHTML as $filterId => $filter)
                {
                    if (empty($filterId) || $filterId == "catid")
                    {
                        continue;
                    }
                    $filterId = "Customfield:" . str_replace("_", " ", $filterId);
                    $HTML = str_replace(array("{" . $filterId ."}", "{" . $filterId ." LBL}"), array($filter["html"], $filter["title"]), $HTML);
                }
				ob_start();?>
	            <button class="modfilter_button btn btn-secondary" type="button" onclick="JeventsFilters.reset(this.form)">
                    <?php echo Text::_('MOD_JEV_FILTER_MODULE_RESET'); ?>
	            </button>
                <?php
				$reset = ob_get_clean();
				$HTML = str_replace("{RESET_BUTTON}", $reset, $HTML );

				ob_start();?>
						<button class="modfilter_button  btn btn-primary" type="submit" name="jevents_filter_submit">
                            <?php echo Text::_('MOD_JEV_FILTER_MODULE_SUBMIT'); ?>
						</button>
                <?php
				$submit = ob_get_clean();
				$HTML = str_replace("{SUBMIT_BUTTON}", $submit, $HTML );
			    echo $HTML;
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
	if (Factory::getApplication()->input->getCmd("jevents_filter_submit") == "ok")
	{
		//	Factory::getApplication()->enqueueMessage("Search Filters applied successfully");
	}
}
