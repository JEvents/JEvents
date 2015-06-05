<?php

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "translate.cancel" || document.formvalidator.isValid(document.getElementById("translate-form")))
		{
			' . $this->form->getField("trans_description")->save() . '
			Joomla.submitform(task, document.getElementById("translate-form"));
		}
	};
');

echo JToolbar::getInstance('toolbar')->render('toolbar');
?>

<form action="<?php echo JRoute::_('index.php?option=com_jevents&task=icalevent.savetranslation');?>" method="post" name="adminForm" id="translate-form" class="form-validate">

	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->renderField('evdet_id'); ?>
						<?php echo $this->form->renderField('ev_id'); ?>
					</div>
					<div class="span6">
						<?php echo $this->form->renderField('language'); ?>
						<?php echo $this->form->renderField('trans_language'); ?>
						<?php echo $this->form->renderField('trans_evdet_id'); ?>
						<?php echo $this->form->renderField('trans_translation_id'); ?>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->getLabel('summary'); ?>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->getInput('summary'); ?>
					</div>
					<div class="span6">
						<?php echo $this->form->getInput('trans_summary'); ?>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->getLabel('description'); ?>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->getInput('description'); ?>
					</div>
					<div class="span6">
						<?php echo $this->form->getInput('trans_description'); ?>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->getLabel('location'); ?>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->getInput('location'); ?>
					</div>
					<div class="span6">
						<?php echo $this->form->getInput('trans_location'); ?>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->getLabel('contact_info'); ?>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->getInput('contact_info'); ?>
					</div>
					<div class="span6">
						<?php echo $this->form->getInput('trans_contact_info'); ?>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->getLabel('extra_info'); ?>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->getInput('extra_info'); ?>
					</div>
					<div class="span6">
						<?php echo $this->form->getInput('trans_extra_info'); ?>
					</div>
				</div>
			</div>
		</div>

	</div>
	<input type="hidden" name="task" value="icalevent.savetranslation" />
	<?php echo JHtml::_('form.token'); ?>
</form>
