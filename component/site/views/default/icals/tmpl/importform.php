<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

$cfg = JEVConfig::getInstance();

$view             = $this->getViewName();
$this->dataModel  = new JEventsDataModel("JEventsAdminDBModel");
$this->queryModel = new JEventsDBModel($this->dataModel);

Factory::getDocument()->addStyleDeclaration("#main {min-height:auto;}");

$action = Factory::getApplication()->isClient('administrator') ? "index.php" : Uri::root() . "index.php?option=" . JEV_COM_COMPONENT . "&Itemid=" . JEVHelper::getItemid();

?>
	<div id="jevents">
		<div class="p10px jevbootstrap">

			<script type="text/javascript">
                function submitbutton() {
                    var form = document.ical;

                    // do field validation
                    if (form.upload.value == "" && form.uploadURL.value == "") {
                        alert("<?php echo Text::_('JEV_MISSING_FILE_AND_URL_SELECTION', true); ?>");
                    }
                    else if (form.catid && form.catid.value == 0 && form.catid.options && form.catid.options.length) {
                        alert('<?php echo Text::_('JEV_SELECT_CATEGORY', true); ?>');
                    }
                    else if (form.icsid.value == "0") {
                        alert("<?php echo Text::_('JEV_MISSING_ICAL_SELECTION', true); ?>");
                    }
                    else {
                        Joomla.submitform();
                        return true;
                    }
                    return false;
                }
			</script>

			<form name="ical" method="post" accept-charset="UTF-8" enctype="multipart/form-data"
			      onsubmit="return submitbutton()" class="adminform">

				<div>
					<strong><?php echo Text::_("JEV_FROM_FILE"); ?></strong><br/>
					<input class="inputbox" type="file" name="upload" id="upload" size="30"/>
				</div>
				<br/>
				<div>
					<strong><?php echo Text::_("JEV_FROM_URL"); ?></strong><br/>
					<input class="inputbox" type="text" name="uploadURL" id="uploadURL" size="30"/>
				</div>

				<?php if ($this->clistChoice) { ?>
					<script type="text/javascript">
                        function preselectCategory(select) {
                            var lookup = [];
                            lookup[0] = 0;
							<?php
							foreach ($this->nativeCals as $nc)
							{
								echo 'lookup[' . $nc->ics_id . ']=' . $nc->catid . ';';
							}
							?>
                            document.ical['catid'].value = lookup[select.value];
                        }
					</script>
					<strong><?php echo Text::_("Select Ical (from raw icals)"); ?></strong><br/>
					<?php
				}
				if ($this->clist)
				{
					echo $this->clist . "<Br/>";
				} ?>

				<?php
				if (!isset($this->editItem->createnewcatories) || $this->editItem->createnewcatories == 0)
				{
					$checked0 = ' checked="checked"';
					$checked1 = '';
				}
				else
				{
					$checked1 = ' checked="checked"';
					$checked0 = '';
				}
				?>
				<div >
					<strong><?php echo Text::_('JEV_CREATE_NEW_IMPORTED_CATEGORIES'); ?></strong>
					<br>
					<div >
						<fieldset class="radio gsl-button-group" id="createnewcatories">
							<input id="createnewcatories0" type="radio" class='gsl-hidden' value="0"
							       name="createnewcatories" <?php echo $checked0; ?>/>
							<label for="createnewcatories0" class="gsl-button <?php echo empty($checked0) ? "gsl-button-default" : "gsl-button-danger";?> gsl-button-small"><?php echo Text::_('JEV_NO'); ?></label>
							<input id="createnewcatories1" type="radio" class='gsl-hidden' value="1"
							       name="createnewcatories" <?php echo $checked1; ?>/>
							<label for="createnewcatories1" class="gsl-button <?php echo empty($checked1) ? "gsl-button-default" : "gsl-button-primary";?> gsl-button-small"><?php echo Text::_('JEV_YES'); ?></label>
						</fieldset>
					</div>
				</div>

				<strong><?php echo Text::_('SELECT_CATEGORY'); ?></strong><br/>
				<?php echo JEventsHTML::buildCategorySelect(0, '', $this->dataModel->accessibleCategoryList(), false, true, 0, 'catid', JEV_COM_COMPONENT, $this->excats); ?>
				<br/>
				<br/>
				<div>
					<strong><?php echo Text::_('JEV_IGNORE_EMBEDDED_CATEGORIES'); ?></strong>
					<label for="ignoreembedcat0" style="display:inline;">
						<input id="ignoreembedcat0" type="radio" value="0" name="ignoreembedcat" checked="checked"/>
						<?php echo Text::_('JEV_NO'); ?>
					</label>
					<label for="ignoreembedcat1" style="display:inline;">
						<input id="ignoreembedcat1" type="radio" value="1" name="ignoreembedcat"/>
						<?php echo Text::_('JEV_YES'); ?>
					</label>
				</div>
				<br/>
				<br/>

				<input type="submit" name="submit" value="<?php echo Text::_('JEV_IMPORT', true) ?>"/>

				<input type="hidden" name="task" value="icals.importdata"/>
				<input type="hidden" name="option" value="com_jevents"/>
				<?php echo HTMLHelper::_('form.token'); ?>
			</form>
		</div>
	</div>
<?php
/*
// Load Bootstrap
JevHtmlBootstrap::framework();

//HTMLHelper::_('behavior.formvalidation');
$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
if ($params->get("bootstrapchosen", 1))
{
	$jversion = new Joomla\CMS\Version;
	if (!$jversion->isCompatible('4.0'))
	{
		HTMLHelper::_('formbehavior.chosen', '#jevents select:not(.notchosen)');
	}
}
if ($params->get("bootstrapcss", 1)==1)
{
	// This version of bootstrap has maximum compatability with JEvents due to enhanced namespacing
	HTMLHelper::stylesheet("com_jevents/bootstrap.css", array(), true);
}
else if ($params->get("bootstrapcss", 1)==2)
{
	JHtmlBootstrap::loadCss();
}

*/