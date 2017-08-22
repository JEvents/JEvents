<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJeveventprimarycategory extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jeveventprimarycategory';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$cfg = JEVConfig::getInstance();
		if ($cfg->get("multicategory", 0)) {
			$catids = $this->form->getValue("catid", array());
			if (!is_array($catids)) {
				$catids = array($catids);
			}
			$primarycatid = $this->value;
			$options = array();
			$options[]  = JHTML::_('select.option', '0',JText::_("JEV_SELECT_PRIMARY_CATEGORY"));
			$allcategories = JHtml::_('category.categories', "com_jevents");
			$sortedcategories = array();
			foreach ($allcategories as $cat) {
				$sortedcategories[$cat->value] = $cat->text;
			}
			foreach ($catids as $cat) {
				if (isset($sortedcategories[$cat])) {
					$options[]  = JHTML::_('select.option', $cat, $sortedcategories[$cat]);
				}
			}
			$input = JHTML::_('select.genericlist', $options, $this->name, "", 'value', 'text', $primarycatid);
			return $input;
		}
		else {
			$input = "";
		}

		return  $input;
	}
	
	protected function getLabel() {
		$cfg = JEVConfig::getInstance();
		if ($cfg->get("multicategory", 0)) {
			return parent::getLabel();
		}
		return "";
	}

}