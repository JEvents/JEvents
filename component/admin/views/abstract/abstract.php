<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: abstract.php 1599 2009-10-06 08:12:48Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class JEventsAbstractView extends JView {
	
	function __construct($config = null)
	{
		parent::__construct($config);
		$this->_addPath('template', $this->_basePath.DS.'views'.DS.'abstract'.DS.'tmpl');
	}

	/**
	 * Control Panel display function
	 *
	 * @param template $tpl
	 */
	function display($tpl = null)
	{
		$layout = $this->getLayout();
		
		if (method_exists($this,$layout)){
			$this->$layout($tpl);
		} 			

		// Allow the layout to be overriden by menu parameter - this only works if its valid for the task
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$newlayout = $params->getValue("layout",$layout);

		// check the template layout is valid for this task
		jimport('joomla.filesystem.path');
		$filetofind	= $this->_createFileName('template', array('name' => $newlayout));
		if (JPath::find($this->_path['template'], $filetofind)){
			$this->setLayout($newlayout);		
		}

		parent::display($tpl);
	}
	
	function displaytemplate($tpl = null)
	{
		return parent::display($tpl);
	}
	
	/**
	 * Routine to hide submenu suing CSS since there are no paramaters for doing so without hiding the main menu
	 *
	 */
	function _hideSubmenu(){
		JHTML::stylesheet( 'hidesubmenu.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );	 	
	}

	/**
	 * This method creates a standard cpanel button
	 *
	 * @param unknown_type $link
	 * @param unknown_type $image
	 * @param unknown_type $text
	 */
	function _quickiconButton( $link, $image, $text, $path='/administrator/images/', $target='', $onclick='' ) {
	 	if( $target != '' ) {
	 		$target = 'target="' .$target. '"';
	 	}
	 	if( $onclick != '' ) {
	 		$onclick = 'onclick="' .$onclick. '"';
	 	}
	 	if( $path === null || $path === '' ) {
	 		$path = '/administrator/images/';
	 	}
		?>
		<div style="float:left;">
			<div class="icon">
				<a href="<?php echo $link; ?>" <?php echo $target;?>  <?php echo $onclick;?>>
					<?php echo JHTML::_('image.administrator', $image, $path, NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}


	/**
	* Creates label and tool tip window as onmouseover event
	* if label is empty, a (i) icon is used
	*
	* @static
	* @param $tip	string	tool tip text declaring label
	* @param $label	string	label text
	* @return		string	html string
	*/
	function tip ( $tip='', $label='') {

		JHTML::_('behavior.tooltip');
		if (!$tip) {
			$str = $label;
		}
		//$tip = htmlspecialchars($tip, ENT_QUOTES);
		//$tip = str_replace('&quot;', '\&quot;', $tip);
		$tip = str_replace("'", "&#039;", $tip);
		$tip = str_replace('"', "&quot;", $tip);
		$tip = str_replace("\n", " ", $tip);
		if (!$label) {
			$str = JHTML::_('tooltip',$tip, null, 'tooltip.png', null, null, 0);
		} else {
			$str = '<span class="editlinktip">'
			. JHTML::_('tooltip',$tip, $label, null,  $label, '', 0)
			. '</span>';
		}
		return $str;
	}

	/**
	* Utility function to provide Warning Icons - should be in Joomla 1.5 but isn't!
	*/
	function jevWarning($warning, $title='Joomla! Warning') {

		$mouseover 	= 'return overlib(\''. $warning .'\', CAPTION, \''. $title .'\', BELOW, RIGHT);';

		$tip 		= "<!-- Warning -->\n";
		$tip 		.= '<a href="javascript:void(0)" onmouseover="'. $mouseover .'" onmouseout="return nd();">';
		$tip 		.= '<img src="'. JURI::root() .'/includes/js/ThemeOffice/warning.png" border="0"  alt="warning"/></a>';

		return $tip;
	}
	

}
