<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultViewHelperHeader16($view){

	$task = JRequest::getString("jevtask");
	$view->loadModules("jevprejevents");
	$view->loadModules("jevprejevents_".$task);
	
	$dispatcher	=& JDispatcher::getInstance();
	$dispatcher->trigger( 'onJEventsHeader', array($view));

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

	$lang = &JFactory::getLanguage();
?>
<div class="contentpaneopen jeventpage<?php echo $params->get( 'pageclass_sfx' ); ?>" id="jevents_header">
	<?php if ($params->get('show_page_heading', 0)) : ?>
	<h1>
		<?php echo $view->escape($params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>	
	<?php
	$t_headline = '&nbsp;';
	switch ($cfg->get('com_calHeadline', 'comp')) {
		case 'none':
			$t_headline = '';
			break;
		case 'menu':
			$menu2   =& JSite::getMenu();
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
	$task = JRequest::getString("jevtask");
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
			<a href="javascript:void(0);" onclick="javascript:window.print(); return false;" title="<?php echo JText::_('JEV_CMN_PRINT'); ?>">
		              	<?php echo JHTML::_('image', 'media/system/images/printButton.png', JText::_('JEV_CMN_PRINT'), array("title"=>JText::_('JEV_CMN_PRINT')),false);?>
			</a>
			</li> <?php
		} else { ?>
			<li class="print-icon">
			<a href="javascript:void(0);" onclick="window.open('<?php echo $print_link; ?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=600,height=600,directories=no,location=no');" title="<?php echo JText::_('JEV_CMN_PRINT'); ?>">
				<?php echo JHTML::_('image', 'media/system/images/printButton.png', JText::_('JEV_CMN_PRINT'), array("title"=>JText::_('JEV_CMN_PRINT')),false);?>
			</a>
			</li> <?php
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
		//if (strpos($link,"/")===0) $link = substr($link,1);
		$uri	        =& JURI::getInstance(JURI::base());
		$root = $uri->toString( array('scheme', 'host', 'port') );

		$link = $root.$link;
		require_once(JPATH_SITE.'/'.'components'.'/'.'com_mailto'.'/'.'helpers'.'/'.'mailto.php');
		$url	= JRoute::_('index.php?option=com_mailto&tmpl=component&link='.MailToHelper::addLink( $link ));

		?>
		<li class="email-icon">
			<a href="javascript:void(0);" onclick="javascript:window.open('<?php echo $url;?>','emailwin','width=400,height=350,menubar=yes,resizable=yes'); return false;" title="<?php echo JText::_( 'EMAIL' ); ?>">
				<?php echo JHTML::_('image', 'media/system/images/emailButton.png', JText::_('EMAIL'), array("title"=>JText::_('EMAIL')),false);?>
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
<div class="contentpaneopen  jeventpage<?php echo $params->get( 'pageclass_sfx' );  ?>" id="jevents_body">
<?php
}
