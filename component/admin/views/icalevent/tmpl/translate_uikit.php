<?php

defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.formvalidator');

$j4 = true;
if (version_compare(JVERSION, '4.0', 'lt'))
{
	$j4 = false;
}

$app = Factory::getApplication();

Factory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "translate.cancel" || document.formvalidator.isValid(document.getElementById("translate-form")))
		{
			' . (!$j4 ? $this->form->getField("trans_description")->save()  : '') . '
			Joomla.submitform(task, document.getElementById("translate-form"));
		}
	};
');


?>
<div id="jevents">
	<div id="jevents_body">
<form action="<?php echo Route::_('index.php?option=com_jevents&task=icalevent.savetranslation'); ?>" method="post"
      name="adminForm" id="translate-form" class="gsl-form-horizontal form-validate">

	<div class="adminform">
		<div class="gsl-grid ">
			<div class="gsl-width-1-2">
				<?php echo $this->form->renderField('evdet_id'); ?>
				<?php echo $this->form->renderField('ev_id'); ?>
			</div>
			<div class="gsl-width-1-2">
				<?php echo $this->form->renderField('language'); ?>
				<?php echo $this->form->renderField('trans_language'); ?>
				<?php echo $this->form->renderField('trans_evdet_id'); ?>
				<?php echo $this->form->renderField('trans_translation_id'); ?>
			</div>
		</div>
		<div class="gsl-grid">
			<?php echo $this->form->getLabel('summary'); ?>
		</div>
		<div class="gsl-grid ">
			<div class="gsl-width-1-2">
				<?php echo $this->form->getInput('summary'); ?>
			</div>
			<div class="gsl-width-1-2">
				<?php echo $this->form->getInput('trans_summary'); ?>
			</div>
		</div>
		<div class="gsl-grid">
			<?php echo $this->form->getLabel('description'); ?>
		</div>
		<div class="gsl-grid ">
			<div class="gsl-width-1-2">
				<?php echo $this->form->getInput('description'); ?>
			</div>
			<div class="gsl-width-1-2">
				<?php echo $this->form->getInput('trans_description'); ?>
			</div>
		</div>
		<div class="gsl-grid">
			<?php echo $this->form->getLabel('location'); ?>
		</div>
		<div class="gsl-grid ">
			<div class="gsl-width-1-2">
				<?php echo $this->form->getInput('location'); ?>
			</div>
			<div class="gsl-width-1-2">
				<?php echo $this->form->getInput('trans_location'); ?>
			</div>
		</div>
		<div class="gsl-grid ">
			<div class="gsl-grid">
				<?php echo $this->form->getLabel('contact_info'); ?>
			</div>
		</div>
		<div class="gsl-grid ">
			<div class="gsl-width-1-2">
				<?php echo $this->form->getInput('contact_info'); ?>
			</div>
			<div class="gsl-width-1-2">
				<?php echo $this->form->getInput('trans_contact'); ?>
			</div>
		</div>
		<div class="gsl-grid">
			<?php echo $this->form->getLabel('extra_info'); ?>
		</div>
		<div class="gsl-grid ">
			<div class="gsl-width-1-2">
				<?php echo $this->form->getInput('extra_info'); ?>
			</div>
			<div class="gsl-width-1-2">
				<?php echo $this->form->getInput('trans_extra_info'); ?>
			</div>
		</div>
		<?php if (isset($this->row->customfieldTranslations))
		{
			foreach ($this->row->customfieldTranslations as $fieldid => $translation)
			{
				?>
				<div class="gsl-grid">
					<label title="" class="control-label hasTooltip"
					       for="<?php "cf" . $fieldid . "translation"; ?>"
					       id="<?php "cf" . $fieldid . "translation"; ?>-lbl">
						<?php echo $translation->label; ?>
					</label>
				</div>
				<div class="gsl-grid ">
					<div class="gsl-width-1-2">
						<?php echo $translation->original; ?>
					</div>
					<div class="gsl-width-1-2">
						<?php echo $translation->translation; ?>
					</div>
				</div>
				<?php
			}
		}
		?>
	</div>
	
	<input type="hidden" name="task" value="icalevent.savetranslation"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
	</div>
</div>