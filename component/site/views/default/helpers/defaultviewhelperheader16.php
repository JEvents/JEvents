<?php 
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.module.helper' );

function DefaultViewHelperHeader16($view){

	$jinput = JFactory::getApplication()->input;

	$task = $jinput->getString('jevtask', '');
	$view->loadModules("jevprejevents");
	$view->loadModules("jevprejevents_".$task);
	
	$dispatcher	= JEventDispatcher::getInstance();
	$dispatcher->trigger( 'onJEventsHeader', array($view));

	$cfg		= JEVConfig::getInstance();
	$version	= JEventsVersion::getInstance();
	$jevtype	= $jinput->get('jevtype', null, null);
	$evid		= $jinput->getInt('evid', '');
	$pop		= $jinput->getInt('pop', '0');
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

	$view->copyrightComment();

	// stop crawler and set meta tag
	JEVHelper::checkRobotsMetaTag();

	// Call the MetaTag setter function.
	JEVHelper::SetMetaTags();
	
	$lang = JFactory::getLanguage();
?>
<div id="jevents">
<div class="contentpaneopen jeventpage<?php echo $params->get( 'pageclass_sfx' ); ?>  jevbootstrap" id="jevents_header">
	<?php if ($params->get('show_page_heading', 0)) : ?>
	<h1>
		<?php echo $view->escape($params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>	
	<?php
	$t_headline = '';
	switch ($cfg->get('com_calHeadline', 'comp')) {
		case 'none':
			$t_headline = '';
			break;
		case 'menu':
			$menu2   = JFactory::getApplication()->getMenu();
			$menu    = $menu2->getActive();
			if (isset($menu) && isset($menu->title)) {
				$t_headline = $menu->title;
			}
			break;
		default:
			$t_headline = JText::_('JEV_EVENT_CALENDAR');
			break;
	}
	if ($t_headline!=""){
		?>
		<h2 class="contentheading" ><?php echo $t_headline;?></h2>
		<?php
	}
	$task = $jinput->getString('jevtask', '');
	ob_start();
	$view->information16();
	$info = ob_get_clean();

	if ($cfg->get('com_print_icon_view', 1) || $cfg->get('com_email_icon_view', 1) || strpos($info, "<li>")!==false ){
	?>
	<ul class="actions">
	<?php
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
			<li class="print-icon">
			<a href="javascript:void(0);" rel="nofollow" onclick="javascript:window.print(); return false;" title="<?php echo JText::_('JEV_CMN_PRINT'); ?>">
		              	<span class="icon-print"> </span>
			</a>
			</li> <?php
		} else { ?>
			<li class="print-icon">
			<a href="javascript:void(0);" rel="nofollow" onclick="window.open('<?php echo $print_link; ?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=600,height=600,directories=no,location=no');" title="<?php echo JText::_('JEV_CMN_PRINT'); ?>">
				<span class="icon-print"> </span>
			</a>
			</li> <?php
		}
	}
	if ($cfg->get('com_email_icon_view', 1)){

		$task = $jinput->getString('jevtask', '');
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
		//if (strpos($link,"/")===0) $link = JString::substr($link,1);
		$uri	        = JURI::getInstance(JURI::base());
		$root = $uri->toString( array('scheme', 'host', 'port') );

		$link = $root.$link;
		require_once(JPATH_SITE.'/'.'components'.'/'.'com_mailto'.'/'.'helpers'.'/'.'mailto.php');
		$url	= JRoute::_('index.php?option=com_mailto&tmpl=component&link='.MailToHelper::addLink( $link ));

		?>
		<li class="email-icon">
			<a href="javascript:void(0);" rel="nofollow" onclick="javascript:window.open('<?php echo $url;?>','emailwin','width=400,height=350,menubar=yes,resizable=yes'); return false;" title="<?php echo JText::_( 'EMAIL' ); ?>">
				<span class="icon-envelope"> </span>
			</a>
		</li>
		<?php
	}
		echo $info;
	?>
		</ul>
	<?php

	}
	?>
</div>
<?php 
	$view->loadModules("jevprejevents2");
	$view->loadModules("jevprejevents2_".$task);
?>
<div class="contentpaneopen  jeventpage<?php echo $params->get( 'pageclass_sfx' );  ?>  jevbootstrap" id="jevents_body">
<?php
}
