<?php
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class FormFieldJeveventlocation extends FormField
{

	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Jeveventlocation';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput()
	{

		ob_start();
		$event      = $this->form->jevdata[$this->name]["event"];
		$res        = Factory::getApplication()->triggerEvent('onEditLocation', array(&$event));
		if (count($res) == 0 || !$res[0])
		{
			?>
			<input class="inputbox" type="text" name="location" size="80" maxlength="120"
			       value="<?php echo JEventsHtml::special($this->value); ?>"/>
			<?php
		}
		$input = ob_get_clean();

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		return $input;

	}

}
class_alias("FormFieldJeveventlocation", "JFormFieldJeveventlocation");
