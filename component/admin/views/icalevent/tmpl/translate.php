<?php

defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

HTMLHelper::_('behavior.formvalidator');

$app = Factory::getApplication();
$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
if ($app->isClient('administrator') || $params->get("newfrontendediting", 1))
{
	$translatePage = $this->loadTemplate('uikit');
	echo $translatePage;
	return;
}

$j4 = true;
$rowclass = "row";
if (version_compare(JVERSION, '4.0', 'lt'))
{
	HTMLHelper::_('formbehavior.chosen', 'select');
	$j4 = false;
	$rowclass = "row-fluid";
}

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

<form action="<?php echo Route::_('index.php?option=com_jevents&task=icalevent.savetranslation'); ?>" method="post"
      name="adminForm" id="translate-form" class="form-validate">

	<div class="form-horizontal">
		<div class="<?php echo $rowclass;?>">
			<div class="span12">
				<div class="<?php echo $rowclass;?> form-horizontal-desktop">
					<div class="span6 col-6">
						<?php echo $this->form->renderField('evdet_id'); ?>
						<?php echo $this->form->renderField('ev_id'); ?>
					</div>
					<div class="span6  col-6">
						<?php echo $this->form->renderField('language'); ?>
						<?php echo $this->form->renderField('trans_language'); ?>
						<?php echo $this->form->renderField('trans_evdet_id'); ?>
						<?php echo $this->form->renderField('trans_translation_id'); ?>
					</div>
				</div>
				<div class="<?php echo $rowclass;?> form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->getLabel('summary'); ?>
					</div>
				</div>
				<div class="<?php echo $rowclass;?> form-horizontal-desktop">
					<div class="span6  col-6">
						<?php echo $this->form->getInput('summary'); ?>
					</div>
					<div class="span6  col-6">
						<?php echo $this->form->getInput('trans_summary'); ?>
					</div>
				</div>
				<div class="<?php echo $rowclass;?> form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->getLabel('description'); ?>
					</div>
				</div>
				<div class="<?php echo $rowclass;?> form-horizontal-desktop">
					<div class="span6  col-6">
						<?php echo $this->form->getInput('description'); ?>
					</div>
					<div class="span6  col-6">
						<?php echo $this->form->getInput('trans_description'); ?>
					</div>
				</div>
				<div class="<?php echo $rowclass;?> form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->getLabel('location'); ?>
					</div>
				</div>
				<div class="<?php echo $rowclass;?> form-horizontal-desktop">
					<div class="span6  col-6">
						<?php echo $this->form->getInput('location'); ?>
					</div>
					<div class="span6  col-6">
						<?php echo $this->form->getInput('trans_location'); ?>
					</div>
				</div>
				<div class="<?php echo $rowclass;?> form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->getLabel('contact_info'); ?>
					</div>
				</div>
				<div class="<?php echo $rowclass;?> form-horizontal-desktop">
					<div class="span6  col-6">
						<?php echo $this->form->getInput('contact_info'); ?>
					</div>
					<div class="span6  col-6">
						<?php echo $this->form->getInput('trans_contact'); ?>
					</div>
				</div>
				<div class="<?php echo $rowclass;?> form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->getLabel('extra_info'); ?>
					</div>
				</div>
				<div class="<?php echo $rowclass;?> form-horizontal-desktop">
					<div class="span6  col-6">
						<?php echo $this->form->getInput('extra_info'); ?>
					</div>
					<div class="span6  col-6">
						<?php echo $this->form->getInput('trans_extra_info'); ?>
					</div>
				</div>
				<?php if (isset($this->row->customfieldTranslations))
				{
					foreach ($this->row->customfieldTranslations as $fieldid => $translation)
					{
						?>
						<div class="<?php echo $rowclass;?> form-horizontal-desktop">
							<div class="span12">
								<label title="" class="control-label hasTooltip"
								       for="<?php "cf" . $fieldid . "translation"; ?>"
								       id="<?php "cf" . $fieldid . "translation"; ?>-lbl">
									<?php echo $translation->label; ?>
								</label>
							</div>
						</div>
						<div class="<?php echo $rowclass;?> form-horizontal-desktop">
							<div class="span6  col-6">
								<?php echo $translation->original; ?>
							</div>
							<div class="span6  col-6">
								<?php echo $translation->translation; ?>
							</div>
						</div>
						<?php
					}
					$script = <<< SCRIPT
	window.setTimeout("setupTranslationBootstrap()", 500);

	function setupTranslationBootstrap(){
		(function($){
			// Turn radios into btn-group
			$('.radio.btn-group label').addClass('btn');
			var el = $(".radio.btn-group label");
			
			// Isis template and others may already have done this so remove these!
			$(".radio.btn-group label").unbind('click');
			
			$(".radio.btn-group label").click(function() {
				var label = $(this);
				var input = $('#' + label.attr('for'));
				if (!input.prop('checked') && !input.prop('disabled')) {
					label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
					if (input.prop('value')!=0){
						label.addClass('active btn-success');
					}
					else {
						label.addClass('active btn-danger');
					}
					input.prop('checked', true);
                                        input.trigger('change');
				}
			});

			// Turn checkboxes into btn-group
			$('.checkbox.btn-group label').addClass('btn');
			
			// Isis template and others may already have done this so remove these!
			$(".checkbox.btn-group label").unbind('click');
			$(".checkbox.btn-group label input[type='checkbox']").unbind('click');
			
			$(".checkbox.btn-group label").click(function(event) {
				event || (event = window.event);
				
				// stop the event being triggered twice is click on input AND label outside it!
				if (event.target.tagName.toUpperCase()=="INPUT"){
					//event.preventDefault();
					return;
				}
				
				var label = $(this);
				var input = $('#' + label.attr('for'));
				//alert(label.val()+ " "+event.target.tagName+" checked? "+input.prop('checked')+ " disabled? "+input.prop('disabled')+ " label disabled? "+label.hasClass('disabled'));
				if (input.prop('disabled')) {
					label.removeClass('active btn-success btn-danger btn-primary');
					input.prop('checked', false);
					event.stopImmediatePropagation();
                                        input.trigger('change');
					return;
				}
				if (!input.prop('checked')) {
					if (input.prop('value')!=0){
						label.addClass('active btn-success');
					}
					else {
						label.addClass('active btn-danger');
					}
				}
				else {
					label.removeClass('active btn-success btn-danger btn-primary');
				}
                                input.trigger('change');
				// bootstrap takes care of the checkboxes themselves!
				
			});
		
			$(".btn-group input[type=checkbox]").each(function() {
				var input = $(this);
				input.css('display','none');
			});		
		})(jQuery);

		initialiseTranslationBootstrapButtons();
	}
	
	function initialiseTranslationBootstrapButtons(){
		(function($){
			// this doesn't seem to find just the checked ones!'
			//$(".btn-group input[checked=checked]").each(function() {
			var clickelems = $(".btn-group input[type=checkbox] , .btn-group input[type=radio]");

			clickelems.each(function(idx, val) {
				if (!$(this).attr('id')){
					return;
				}
				var label = $("label[for=" + $(this).attr('id') + "]");
				var elem = $(this);
				if (elem.prop('disabled')) {
					label.addClass('disabled');
					label.removeClass('active btn-success btn-danger btn-primary');
					return;
				}
				label.removeClass('disabled');
				if (!elem.prop('checked')) {
					label.removeClass('active btn-success btn-danger btn-primary');
					return;
				}
				if (elem.val()!=0){
					label.addClass('active btn-success');
				}
				else {
					label.addClass('active btn-danger');
				}

			});
			
		})(jQuery);
	}
SCRIPT;
					Factory::getDocument()->addScriptDeclaration($script);
				}
				?>
			</div>
		</div>

	</div>
	<input type="hidden" name="task" value="icalevent.savetranslation"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
