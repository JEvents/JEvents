<?php
defined('_JEXEC') or die('Restricted access');

function DefaultEventIcalDialog($view, $row, $mask)
{

	if (version_compare(JVERSION, "3.2", "lt"))
	{
		return $view->eventIcalDialog16($row, $mask);
	}

	JHtml::_('bootstrap.modal', "ical_dialogJQ".$row->rp_id());
	?>
	<div id="ical_dialogJQ<?php echo $row->rp_id();?>" class="ical_dialogJQ modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel"><?php echo JText::_("JEV_EXPORT_EVENT"); ?></h4>
				</div>
				<div class="modal-body">

					<?php
					if ($row->hasRepetition())
					{
						?>
						<div id="unstyledical">
							<a href="<?php echo $row->vCalExportLink(false, false); ?>" title="<?php echo JText::_("JEV_SAVEICAL") ?>"  >
								<?php
								echo '<img src="' . JURI::root() . 'components/' . JEV_COM_COMPONENT . '/assets/images/save_f2.png" alt="' . JText::_("JEV_SAVEICAL") . '" />';
								echo JText::_("JEV_All_Recurrences");
								?>
							</a><br/>
							<a href="<?php echo $row->vCalExportLink(false, true); ?>" title="<?php echo JText::_("JEV_SAVEICAL") ?>" >
								<?php
								echo '<img src="' . JURI::root() . 'components/' . JEV_COM_COMPONENT . '/assets/images/save_f2.png" alt="' . JText::_("JEV_SAVEICAL") . '" />';
								echo JText::_("JEV_Single_Recurrence");
								?>
							</a>
						</div>
						<div id="styledical">
							<a href="<?php echo $row->vCalExportLink(false, false) . "&icf=1"; ?>" title="<?php echo JText::_("JEV_SAVEICAL") ?>" >
								<?php
								echo '<img src="' . JURI::root() . 'components/' . JEV_COM_COMPONENT . '/assets/images/save_f2.png" alt="' . JText::_("JEV_SAVEICAL") . '" />';
								echo JText::_("JEV_All_Recurrences");
								?>
							</a><br/>
							<a href="<?php echo $row->vCalExportLink(false, true) . "&icf=1"; ?>" title="<?php echo JText::_("JEV_SAVEICAL") ?>" >
								<?php
								echo '<img src="' . JURI::root() . 'components/' . JEV_COM_COMPONENT . '/assets/images/save_f2.png" alt="' . JText::_("JEV_SAVEICAL") . '" />';
								echo JText::_("JEV_Single_Recurrence");
								?>
							</a>
						</div>
						<?php
					}
					else
					{
						?>
						<div id="unstyledical">
							<a href="<?php echo $row->vCalExportLink(false, false); ?>" title="<?php echo JText::_("JEV_EXPORT_EVENT") ?>"  >
								<?php
								echo '<img src="' . JURI::root() . 'components/' . JEV_COM_COMPONENT . '/assets/images/save_f2.png" alt="' . JText::_("JEV_EXPORT_EVENT") . '" />';
								echo JText::_("JEV_EXPORT_EVENT");
								?>
							</a>
						</div>
						<div id="styledical">
							<a href="<?php echo $row->vCalExportLink(false, false) . "&icf=1"; ?>" title="<?php echo JText::_("JEV_EXPORT_EVENT") ?>"  >
								<?php
								echo '<img src="' . JURI::root() . 'components/' . JEV_COM_COMPONENT . '/assets/images/save_f2.png" alt="' . JText::_("JEV_EXPORT_EVENT") . '" />';
								echo JText::_("JEV_EXPORT_EVENT");
								?>
							</a>
						</div>
						<?php
					}
					?>
					<label><input name="icf" type="checkbox" value="1" onclick="if (this.checked) {
									$('unstyledical').style.display = 'none';
									$('styledical').style.display = 'block';
								} else {
									$('styledical').style.display = 'none';
									$('unstyledical').style.display = 'block';
								}" /><?php echo JText::_("JEV_PRESERVE_HTML_FORMATTING"); ?></label>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo JText::_("JEV_CLOSE"); ?></button>
				</div>

			</div>
		</div>
	</div>

	<script>
		jevjq(".ical_dialogJQ a").click(function(){
			jevjq('.ical_dialogJQ').modal('hide')
		});
	</script>
	<?php

}
