<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

include_once JEV_HELPERS . '/jevExportHelper.php';

function DefaultEventIcalDialog($view, $row, $mask, $bootstrap = false)
{

	JevModal::framework();

	$jevparams = ComponentHelper::getParams('com_jevents');
	if (strpos($jevparams->get('framework', 'bootstrap'), 'uikit') === 0)
	{
		?>
		<div id="ical_dialogJQ<?php echo $row->rp_id(); ?>" class="ical_dialogJQ" tabindex="-1" data-uk-modal
		     role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="uk-modal-dialog">
				<button type="button" class="uk-modal-close-default"  data-uk-close ></button>
				<div class="uk-modal-header">
					<h4 class="uk-modal-title" id="myModalLabel"><?php echo Text::_("JEV_EXPORT_EVENT"); ?></h4>
				</div>
				<div class="uk-modal-body">
					<?php
					if ($row->hasRepetition())
					{
						?>
						<div id="unstyledical">
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo $row->vCalExportLink(false, true); ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/save.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AS_ICAL"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToGCal($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/gcal32.png', Text::_("JEV_ADDTOGCAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToYahooCal($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_YAHOO") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/yahoo32.png', Text::_("JEV_SAVE_EVENT_IN_YAHOO"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_YAHOO"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToOutlookLive($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/outlookicon31.png', Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToMsOutlook($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/outlookicon31.png', Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo $row->vCalExportLink(false, false); ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/ical_repeats.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AND_ALL_RECURRENCES_AS_ICAL"); ?></span>
								</a>
							</div>
						</div>
						<div id="styledical">
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo $row->vCalExportLink(false, true) . "&icf=1"; ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/save.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AS_ICAL"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo $row->vCalExportLink(false, false) . "&icf=1"; ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/ical_repeats.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AND_ALL_RECURRENCES_AS_ICAL"); ?></span>
								</a>
							</div>
						</div>
						<?php
					}
					else
					{
						?>
						<div id="unstyledical">
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo $row->vCalExportLink(false, false); ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/save.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AS_ICAL"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToGCal($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/gcal32.png', Text::_("JEV_ADDTOGCAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToYahooCal($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_YAHOO") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/yahoo32.png', Text::_("JEV_SAVE_EVENT_IN_YAHOO"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_YAHOO"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToOutlookLive($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/outlookicon31.png', Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToMsOutlook($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/outlookicon31.png', Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK"); ?></span>
								</a>
							</div>
						</div>
						<div id="styledical">
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo $row->vCalExportLink(false, false) . "&icf=1"; ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/save.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AS_ICAL"); ?></span>
								</a>
							</div>
						</div>
						<?php
					}
					$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
					if ($params->get("icalformatted", 1) == 1)
					{
						?>
						<label style="display:inline;">
							<input name="icf" type="checkbox" value="1" onclick="if (this.checked) {
								jevjq('#unstyledical').css('display',  'none');
								jevjq('#styledical').css('display',  'block');
							} else {
								jevjq('#styledical').css('display',  'none');
								jevjq('#unstyledical').css('display',  'block');
							}"/>
							<?php echo Text::_("JEV_PRESERVE_HTML_FORMATTING"); ?>
						</label>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
	}
	else
	{
		?>
	<div id="ical_dialogJQ<?php echo $row->rp_id(); ?>" class="ical_dialogJQ modal hide fade" tabindex="-1"
	     role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<?php if (version_compare(JVERSION, '4.0.0', 'ge'))  { ?>
						<h4 class="modal-title" id="myModalLabel"><?php echo Text::_("JEV_EXPORT_EVENT"); ?></h4>
						<button type="button" class="close btn-close uk-modal-close-default" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true"></button>
					<?php } else { ?>
						<button type="button" class="close uk-modal-close-default" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel"><?php echo Text::_("JEV_EXPORT_EVENT"); ?></h4>
					<?php } ?>
				</div>
				<div class="modal-body">

					<?php
					if ($row->hasRepetition())
					{
						?>
						<div id="unstyledical">
							<div>
								<a style="text-decoration:none" href="<?php echo $row->vCalExportLink(false, true); ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/save.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AS_ICAL"); ?></span>
								</a>
							</div>
							<div>
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToGCal($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/gcal32.png', Text::_("JEV_ADDTOGCAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL"); ?></span>
								</a>
							</div>
							<div>
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToYahooCal($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_YAHOO") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/yahoo32.png', Text::_("JEV_SAVE_EVENT_IN_YAHOO"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_YAHOO"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToOutlookLive($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/outlookicon31.png', Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToMsOutlook($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/outlookicon31.png', Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK"); ?></span>
								</a>
							</div>
							<div>
								<a style="text-decoration:none" href="<?php echo $row->vCalExportLink(false, false); ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/ical_repeats.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AND_ALL_RECURRENCES_AS_ICAL"); ?></span>
								</a>
							</div>
						</div>
						<div id="styledical">
							<div>
								<a style="text-decoration:none" href="<?php echo $row->vCalExportLink(false, true) . "&icf=1"; ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/save.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AS_ICAL"); ?></span>
								</a>
							</div>
							<div>
								<a style="text-decoration:none" href="<?php echo $row->vCalExportLink(false, false) . "&icf=1"; ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/ical_repeats.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AND_ALL_RECURRENCES_AS_ICAL"); ?></span>
								</a>
							</div>
						</div>
						<?php
					}
					else
					{
						?>
						<div id="unstyledical">
							<div>
								<a style="text-decoration:none" href="<?php echo $row->vCalExportLink(false, false); ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/save.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AS_ICAL"); ?></span>
								</a>
							</div>
							<div>
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToGCal($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/gcal32.png', Text::_("JEV_ADDTOGCAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL"); ?></span>
								</a>
							</div>
							<div>
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToYahooCal($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_YAHOO") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/yahoo32.png', Text::_("JEV_SAVE_EVENT_IN_YAHOO"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_YAHOO"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToOutlookLive($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/outlookicon31.png', Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"); ?></span>
								</a>
							</div>
							<div class="uk-margin-bottom">
								<a style="text-decoration:none" href="<?php echo JevExportHelper::getAddToMsOutlook($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/outlookicon31.png', Text::_("JEV_SAVE_EVENT_IN_OUTLOOK_LIVE"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_MSOUTLOOK"); ?></span>
								</a>
							</div>
						</div>
						<div id="styledical">
							<div>
								<a style="text-decoration:none" href="<?php echo $row->vCalExportLink(false, false) . "&icf=1"; ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/save.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AS_ICAL"); ?></span>
								</a>
							</div>
						</div>
						<?php
					}
					$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
					if ($params->get("icalformatted", 1) == 1)
					{
					?>
					<label style="display:inline;">
						<input name="icf" type="checkbox" value="1" onclick="if (this.checked) {
								jevjq('#unstyledical').css('display',  'none');
								jevjq('#styledical').css('display',  'block');
							} else {
								jevjq('#styledical').css('display',  'none');
								jevjq('#unstyledical').css('display',  'block');
							}"/>
						<?php echo Text::_("JEV_PRESERVE_HTML_FORMATTING"); ?>
					</label>
                    <?php } ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default"
					        data-dismiss="modal" data-bs-dismiss="modal" ><?php echo Text::_("JEV_CLOSE"); ?></button>
				</div>

			</div>
		</div>
	</div>

	<script>
        jevjq(".ical_dialogJQ a").click(function () {
            jevjq('.ical_dialogJQ').modal('hide')
        });
	</script>
	<?php
	}
}
