<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

include_once JEV_HELPERS . '/jevExportHelper.php';

function DefaultEventIcalDialog($view, $row, $mask, $bootstrap = false)
{

	JevHtmlBootstrap::modal("ical_dialogJQ" . $row->rp_id());
	?>
	<div id="ical_dialogJQ<?php echo $row->rp_id(); ?>" class="ical_dialogJQ modal hide fade" tabindex="-1"
	     role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel"><?php echo Text::_("JEV_EXPORT_EVENT"); ?></h4>
				</div>
				<div class="modal-body">

					<?php
					if ($row->hasRepetition())
					{
						?>
						<div id="unstyledical">
							<div>
								<a href="<?php echo $row->vCalExportLink(false, true); ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/save.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AS_ICAL"); ?></span>
								</a>
							</div>
							<div>
								<a href="<?php echo JevExportHelper::getAddToGCal($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/google.png', Text::_("JEV_ADDTOGCAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL"); ?></span>
								</a>
							</div>
							<div>
								<a href="<?php echo JevExportHelper::getAddToYahooCal($row); ?>"
								   title="<?php echo Text::_("JEV_ADDTOYAHOO") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/yahoo.png', Text::_("JEV_SAVE_EVENT_IN_YAHOO"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_YAHOO"); ?></span>
								</a>
							</div>
							<div>
								<a href="<?php echo $row->vCalExportLink(false, false); ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/ical_repeats.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AND_ALL_RECURRENCES_AS_ICAL"); ?></span>
								</a>
							</div>
						</div>
						<div id="styledical">
							<div>
								<a href="<?php echo $row->vCalExportLink(false, true) . "&icf=1"; ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/save.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AS_ICAL"); ?></span>
								</a>
							</div>
							<div>
								<a href="<?php echo $row->vCalExportLink(false, false) . "&icf=1"; ?>"
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
								<a href="<?php echo $row->vCalExportLink(false, false); ?>"
								   title="<?php echo Text::_("JEV_SAVEICAL") ?>">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/save.png', Text::_("JEV_SAVEICAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_AS_ICAL"); ?></span>
								</a>
							</div>
							<div>
								<a href="<?php echo JevExportHelper::getAddToGCal($row); ?>"
								   title="<?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/google.png', Text::_("JEV_ADDTOGCAL"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_GCAL"); ?></span>
								</a>
							</div>
							<div>
								<a href="<?php echo JevExportHelper::getAddToYahooCal($row); ?>"
								   title="<?php echo Text::_("JEV_ADDTOYAHOO") ?>" target="_blank">
									<span style="display:inline-block;width:24px;"><?php echo HTMLHelper::image('com_jevents/icons-32/yahoo.png', Text::_("JEV_SAVE_EVENT_IN_YAHOO"), null, true); ?></span>
									<span style="display:inline-block;"><?php echo Text::_("JEV_SAVE_EVENT_IN_YAHOO"); ?></span>
								</a>
							</div>
						</div>
						<div id="styledical">
							<div>
								<a href="<?php echo $row->vCalExportLink(false, false) . "&icf=1"; ?>"
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
					        data-dismiss="modal"><?php echo Text::_("JEV_CLOSE"); ?></button>
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
