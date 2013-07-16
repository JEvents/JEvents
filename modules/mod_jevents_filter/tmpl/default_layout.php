<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: default_layout.php 3323 2012-03-08 13:37:46Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module JEvents Filter
 * @copyright   Copyright (C) 2008 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.gwesystems.com
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
if (count($filterHTML) > 0)
{
	if (JVersion::isCompatible("3.0"))
	{
		// Load Bookstrap
		JHtml::_('bootstrap.framework');
		JHtml::_('formbehavior.chosen', '.jevfiltermodule select');
	}

	JEVHelper::script("mod_jevents_filter.js", "modules/mod_jevents_filter/", true);
	?>
	<form action="<?php echo $form_link; ?>" id="jeventspost" name="jeventspost<?php echo $module->id; ?>" method="post" class="jevfiltermodule" >
		<input type='hidden' name='catids' id='catidsfv' value='<?php echo trim($datamodel->catidsOut); ?>' />
		<?php
		// This forces category settings in URL to reset too since they could be set by SEF
		$script = "try {JeventsFilters.filters.push({id:'catidsfv',value:0});} catch (e) {}\n";

		$document = JFactory::getDocument();
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
}
.jevfilterfloatlist li {
	float:left;
	margin-right:5px;
}
STYLE;
		$document->addStyleDeclaration($style);
		switch ($params->get("filterlayout", "vt")) {
			case "vt":
				?>
				<table class="jevfiltertable" >
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
							if (strlen($filter["title"]) > 0)
							{
								?>
								<td><?php echo $filter["title"]; ?></td>
								<?php
							}
							else
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
						<td><input class="modfilter_button" type="button" onclick="JeventsFilters.reset(this.form)" value="<?php echo JText::_('RESET'); ?>" /></td>
						<td ><input class="modfilter_button" type="submit" value="<?php echo JText::_('ok'); ?>" /></td>
					</tr>
				</table>
				<?php
				break;

			case "ht":
				?>
				<table class="jevfiltertable" >
					<tr>
						<?php
						$hasreset = false;
						foreach ($filterHTML as $filter)
						{
							if (!isset($filter["title"]))
							{
								continue;
							}
							if (strlen($filter["title"]) > 0)
							{
								?>
								<td><?php echo $filter["title"]; ?></td>
								<?php
							}
							else
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
						?>
						<td><input class="modfilter_button" type="button" onclick="JeventsFilters.reset(this.form)" value="<?php echo JText::_('RESET'); ?>" /></td>
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
						<td ><input class="modfilter_button" type="submit" value="<?php echo JText::_('ok'); ?>" /></td>
					</tr>
				</table>

				<?php
				break;

			case "ul":
			case "ful":
				?>
				<ul class="<?php echo $params->get("filterlayout", "vt")=="ul"?"jevfilterlist":"jevfilterfloatlist";?>" >
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
							if (strlen($filter["title"]) > 0)
							{
								?>
								<?php echo $filter["title"]; ?>
								<?php
							}
							else
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
						<label><input class="modfilter_button" type="button" onclick="JeventsFilters.reset(this.form)" value="<?php echo JText::_('RESET'); ?>" /></label>
						<div class="jevfilterinput"><input class="modfilter_button" type="submit" value="<?php echo JText::_('ok'); ?>" /></div>
					</li>
				</ul>
				<?php
				if ($params->get("filterlayout", "vt")=="ful"){
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
}