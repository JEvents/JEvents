<?php
jimport("joomla.html.parameter.element");

class JElementJevUserGroup extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'JevUserGroup';

	function fetchElement($name, $value, &$node, $control_name)
	{
		// Must load admin language files
		$lang = JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		// TODO 1.6 ACL VERSION OF THIS
		return "";
				
		$acl	= JFactory::getACL();
		$gtree	= $acl->get_group_children_tree( null, 'USERS', false );
		foreach ($gtree as &$item) {
			if ($item->value>25) $item->disable=true;
		}
		unset($item);
		$ctrl	= $control_name .'['. $name .']';

		$attribs	= ' ';
		if ($v = $node->attributes('size')) {
			$attribs	.= 'size="'.$v.'"';
		}
		if ($v = $node->attributes('class')) {
			$attribs	.= 'class="'.$v.'"';
		} else {
			$attribs	.= 'class="inputbox"';
		}
		if ($m = $node->attributes('multiple'))
		{
			$attribs	.= 'multiple="multiple"';
			$ctrl		.= '[]';
			//$value		= implode( '|', )
		}
		//array_unshift( $editors, JHTML::_('select.option',  '', '- '. JText::_( 'SELECT_EDITOR' ) .' -' ) );

		return JHTML::_('select.genericlist',   $gtree, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );
	}
}
