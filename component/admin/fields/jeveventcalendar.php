<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJeveventcalendar extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jeveventcalendar';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		ob_start();
		$native = $this->form->jevdata[$this->name]["native"];
		$clistChoice = $this->form->jevdata[$this->name]["clistChoice"];
		$clist= $this->form->jevdata[$this->name]["clist"];
		$nativeCals = $this->form->jevdata[$this->name]["nativeCals"];
		
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);		
		
		if ($native && $clistChoice)
		{
			?>
			<script type="text/javascript" >
				function preselectCategory(select){
					var lookup = new Array();
					lookup[0]=0;
					<?php
					foreach ($nativeCals as $nc)
					{
						echo 'lookup[' . $nc->ics_id . ']=' . $nc->catid . ';';
					}
					if ((int)$params->get('defaultcat', 1) === 0) {
						echo "document.adminForm['catid'].value=0;";
					} else {	
						echo "document.adminForm['catid'].value=lookup[select.value];";
					}

					?>
					// trigger Bootstrap Chosen replacement
					try {
						jQuery(document.adminForm['catid']).trigger("liszt:updated");
					}
					catch (e){									
					}
				}
			</script>
			<?php 
			echo $clist;
		}
		else if ($clistChoice)
		{
			echo $clist;
		}
		else
		{
			echo $clist;
		}
		$input = ob_get_clean();

		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		return $input;

	}

	protected function getLabel()
	{
		$clistChoice = $this->form->jevdata[$this->name]["clistChoice"];
		if ($this->getInput() && $clistChoice)
		{
			return parent::getLabel();
		}
		return "";

	}

}