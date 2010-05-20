<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultViewHelperHeader($view){

	$cfg		= & JEVConfig::getInstance();
	$version	= & JEventsVersion::getInstance();
	$jevtype	= JRequest::getVar('jevtype');
	$evid		= JRequest::getInt('evid');
	$pop		= JRequest::getInt('pop', 0);
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

	echo "\n" . '<!-- '
	. $version->getLongVersion() . ', '
	. utf8_encode(@html_entity_decode($version->getLongCopyright(), ENT_COMPAT, 'ISO-8859-1')) . ', '
	. $version->getUrl()
	. ' -->' . "\n";

	// stop crawler and set meta tag
	JEVHelper::checkRobotsMetaTag();

?>
<table class="contentpaneopen jeventpage<?php echo $params->get( 'pageclass_sfx' ); ?>" id="jevents_header">
	<tr>
	<td class="contentheading" width="100%">
	<?php 
	$t_headline = '&nbsp;';
	switch ($cfg->get('com_calHeadline', 'comp')) {
		case 'none':
			$t_headline = '&nbsp;';
			break;
		case 'menu':
			$menu2   =& JSite::getMenu();
			$menu    = $menu2->getActive();
			if (isset($menu) && isset($menu->name)) {
				$t_headline = $menu->name;
			}
			break;
		default:
			$t_headline = JText::_('JEV_EVENT_CALENDAR');
			break;
	}
	echo $t_headline;
	?>
	</td>
	<?php
	$task = JRequest::getString("jevtask");
	if ($cfg->get('com_print_icon_view', 1)){
		$print_link = 'index.php?option=' . JEV_COM_COMPONENT
		. '&task=' . $task
		. ($evid ? '&evid=' . $evid : '')
		. ($jevtype ? '&jevtype=' . $jevtype : '')
		. ($view->year ? '&year=' . $view->year : '')
		. ($view->month ? '&month=' . $view->month : '')
		. ($view->day ? '&day=' . $view->day : '')
		. $view->datamodel->getItemidLink()
		. $view->datamodel->getCatidsOutLink()
		. '&pop=1'
		. '&tmpl=component';
		$print_link = JRoute::_($print_link);

		if ($pop) { ?>
			<td width="20" class="buttonheading" align="right">
			<a href="javascript:void(0);" onclick="javascript:window.print(); return false;" title="<?php echo JText::_('JEV_CMN_PRINT'); ?>">
              	<?php echo JHTML::_('image.site', 'printButton.png', '/images/M_images/', NULL, NULL, JText::_('JEV_CMN_PRINT'));?>
			</a>
			</td> <?php
		} else { ?>
			<td  width="20" class="buttonheading" align="right">
			<a href="javascript:void(0);" onclick="window.open('<?php echo $print_link; ?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=600,height=600,directories=no,location=no');" title="<?php echo JText::_('JEV_CMN_PRINT'); ?>">
              	<?php echo JHTML::_('image.site', 'printButton.png', '/images/M_images/', NULL, NULL, JText::_('JEV_CMN_PRINT'));?>
			</a>
			</td> <?php 
		}
	}
	if ($cfg->get('com_email_icon_view', 1)){

		$task = JRequest::getString("jevtask");
		$link = 'index.php?option=' . JEV_COM_COMPONENT
		. '&task=' . $task
		. ($evid ? '&evid=' . $evid : '')
		. ($jevtype ? '&jevtype=' . $jevtype : '')
		. ($view->year ? '&year=' . $view->year : '')
		. ($view->month ? '&month=' . $view->month : '')
		. ($view->day ? '&day=' . $view->day : '')
		. $view->datamodel->getItemidLink()
		. $view->datamodel->getCatidsOutLink()
		;
		$link =JRoute::_($link);
		if (strpos($link,"/")===0) $link = substr($link,1);
		$link = JURI::root().$link;

		$url	= JRoute::_('index.php?option=com_mailto&tmpl=component&link='.base64_encode( $link ));

		?>
		<td width="20" class="buttonheading" align="right">
			<a href="javascript:void(0);" onclick="javascript:window.open('<? echo $url;?>','emailwin','width=400,height=350,menubar=yes,resizable=yes'); return false;" title="<?php echo JText::_('Email'); ?>">
              	<?php echo JHTML::_('image.site', 'emailButton.png', '/images/M_images/', NULL, NULL, JText::_('Email'));?>
			</a>
		</td>
		<?php
	}
	
	$view->information();
	?>
	</tr>
</table>
<table class="contentpaneopen  jeventpage<?php echo $params->get( 'pageclass_sfx' ); ?>" id="jevents_body">
	<tr>
	<td width="100%">
<?php
}
