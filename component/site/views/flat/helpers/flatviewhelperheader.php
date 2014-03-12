<?php 
defined('_JEXEC') or die('Restricted access');

function FlatViewHelperHeader($view){
	if (version_compare(JVERSION, "1.6.0", 'ge')){
		return $view->_header16();
	}

	$task = JRequest::getString("jevtask");
	$view->loadModules("jevprejevents");
	$view->loadModules("jevprejevents_".$task);
		
	$dispatcher	= JDispatcher::getInstance();
	$dispatcher->trigger( 'onJEventsHeader', array($view));

	$cfg		= JEVConfig::getInstance();
	$version	= JEventsVersion::getInstance();
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

	// Call the MetaTag setter function.
	if (is_callable(array("JEVHelper","SetMetaTags"))){		
		JEVHelper::SetMetaTags();
	}
	
	$lang = JFactory::getLanguage();	
?>
<table class="contentpaneopen jeventpage <?php echo $params->get( 'pageclass_sfx' ); echo $params->get("darktemplate",0)?" jeventsdark":"jeventslight"; echo $lang->isRTL()?" jevrtl":" ";?>" id="jevents_header">
	<tr>
	<td class="contentheading" width="100%">
	<?php 
	$t_headline = '&nbsp;';
	switch ($cfg->get('com_calHeadline', 'comp')) {
		case 'none':
			$t_headline = '&nbsp;';
			break;
		case 'menu':
			$menu2   = JFactory::getApplication()->getMenu();
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
				<?php echo JEVHelper::imagesite( 'printButton.png',JText::_('JEV_CMN_PRINT'));?>
			</a>
			</td> <?php
		} else { ?>
			<td  width="20" class="buttonheading" align="right">
			<a href="javascript:void(0);" onclick="window.open('<?php echo $print_link; ?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=600,height=600,directories=no,location=no');" title="<?php echo JText::_('JEV_CMN_PRINT'); ?>">
				<?php echo JEVHelper::imagesite( 'printButton.png',JText::_('JEV_CMN_PRINT'));?>
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

		require_once(JPATH_SITE.'/'.'components'.'/'.'com_mailto'.'/'.'helpers'.'/'.'mailto.php');
		$url	= JRoute::_('index.php?option=com_mailto&tmpl=component&link='.MailToHelper::addLink( $link ));
		$path = version_compare(JVERSION, "1.6.0", 'ge')?"media/system/images":'/images/M_images/';

		?>
		<td width="20" class="buttonheading" align="right">
			<a href="javascript:void(0);" onclick="javascript:window.open('<?php echo $url;?>','emailwin','width=400,height=350,menubar=yes,resizable=yes'); return false;" title="<?php echo JText::_( 'EMAIL' ); ?>">
				<?php echo JEVHelper::imagesite( 'emailButton.png',JText::_( 'EMAIL' ));?>
			</a>
		</td>
		<?php
	}
	
	?>
	</tr>
</table>
<div class="jev_month_premods"><?php 
	$view->loadModules("jevprejevents2");
	$view->loadModules("jevprejevents2_".$task);
?></div>

<table class="contentpaneopen  jeventpage<?php echo $params->get( 'pageclass_sfx' );  echo $params->get("darktemplate",0)?" jeventsdark":"jeventslight "; echo $lang->isRTL()?" jevrtl":" ";?>" id="jevents_body">
	<tr>
	<td width="100%">
<?php
}
