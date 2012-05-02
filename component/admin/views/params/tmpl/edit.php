<?php 
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: edit.php 1343 2010-10-20 14:31:24Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');
$version = JEventsVersion::getInstance();

?>
	<form action="index.php" method="post" name="adminForm" autocomplete="off" id="adminForm">

		<fieldset class='jevconfig'>
			<legend>
				<?php echo JText::_( 'JEV_EVENTS_CONFIG' );?>
			</legend>
			<div style="float:right;margin-top:-20px;background-color:#ffffff;padding:2px;">
			[<?php	echo $version->getShortVersion();	?>&nbsp;<a href='<?php echo $version->getURL();?>'><?php	echo JText::_('JEV_CHECK_VERSION');	?> </a>]
			</div>
			<?php
			$names = array();
			$groups = $this->params->getGroups();
			if (count($groups)>0){
				jimport('joomla.html.pane');
				$tabs = & JPane::getInstance('tabs');
				echo $tabs->startPane( 'configs' );
				$strings=array();
				$tips=array();
				foreach ($groups as $group=>$count) {
					if ($group!="_default" && $count>0){
						echo $tabs->startPanel( JText::_($group), 'config_'.str_replace(" ","_",$group));
						echo $this->params->render('params',$group);
						echo $tabs->endPanel();
					}
				}

				// Now get layout specific parameters
				foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile) {
					$config = JPATH_SITE . "/components/".JEV_COM_COMPONENT."/views/".$viewfile."/config.xml";
					if (file_exists($config)){
						$viewparams = new JParameter( $this->params->toString(), $config );
						echo $tabs->startPanel( JText::_(ucfirst($viewfile)), 'config_'.str_replace(" ","_",$viewfile));
						echo $viewparams->render();
						echo $tabs->endPanel();
					}
				}

				echo $tabs->endPane();
			}
			else {
				echo $this->params->render();
			}
		?>
	
		<div class="clr"></div>
	        
		</fieldset>

		<input type="hidden" name="id" value="<?php echo $this->component->id;?>" />
		<input type="hidden" name="component" value="<?php echo $this->component->option;?>" />

		<input type="hidden" name="controller" value="component" />
		<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>