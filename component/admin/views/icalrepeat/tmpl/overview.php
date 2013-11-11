<?php 
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: overview.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access'); 

global   $task;
$db	= JFactory::getDBO();
$user = JFactory::getUser();
JHTML::_('behavior.tooltip');


$pathIMG = JURI::Root() . 'administrator/images/';
$pathJeventsIMG = JURI::Root() . "administrator/components/".JEV_COM_COMPONENT."/images/"; ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
			<?php else : ?>
			<div id="j-main-container">
	<?php endif; ?>
	
	<table cellpadding="4" cellspacing="0" border="0" width="100%">
		<tr>
			<td width="100%">
				&nbsp;
			</td>
		</tr>
	</table>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist table table-striped">
		<tr>
			<th width="20" nowrap="nowrap">
		            <input type="checkbox" name="toggle" value="" onclick="<?php echo JevJoomlaVersion::isCompatible("3.0")?"Joomla.checkAll(this)":"checkAll(".count( $this->icalrows ).")"; ?>" />
			</th>
			<th class="title" width="60%" nowrap="nowrap"><?php echo JText::_('JEV_ICAL_SUMMARY'); ?></th>
			<th width="40%" nowrap="nowrap"><?php echo "Repeat Date/Time"; ?></th>
		</tr>

        <?php
        $k 	= 0;
        $nullDate 	= $db->getNullDate();

        for( $i=0, $n=count( $this->icalrows ); $i < $n; $i++ ){
        	$row = &$this->icalrows[$i]; ?>
            <tr class="row<?php echo $k; ?>">
            	<td width="20">
		   <?php echo JHtml::_('grid.id', $i, $row->rp_id()); ?>
            	</td>
              	<td width="30%">
              		<a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','icalrepeat.edit')" title="<?php echo JText::_('JEV_CLICK_TO_EDIT'); ?>"><?php echo $row->title(); ?></a>
              	</td>
              	<td width="40%">
              	<?php
              	$times = '<table style="border: 1px solid #666666; width:100%;">';
              	$times .= '<tr><td>Start : '. $row->publish_up().'</td></tr>';
              	$times .= '<tr><td>End : ' . $row->publish_down(). '</td></tr>';
              	$times .="</table>";
              	echo $times;
				?>
              	</td>
            </tr>
            <?php
            $k = 1 - $k;
        } ?>
    	<tr>
    		<th align="center" colspan="9"><?php echo $this->pageNav->getListFooter(); ?></th>
    	</tr>
    </table>
    <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
    <input type="hidden" name="cid[]" value="<?php echo $this->evid;?>" />
    <input type="hidden" name="evid" value="<?php echo $this->evid;?>" />
    <input type="hidden" name="task" value="icalrepeat.list" />
    <input type="hidden" name="boxchecked" value="0" />
			</div>
</form>

<br />
<table cellspacing="0" cellpadding="4" border="0" align="center">
	<tr align="center">
		<td>
			<img src="<?php echo $pathIMG; ?>publish_y.png" width="12" height="12"  alt="<?php echo JText::_('JEV_TIT_PENDING'); ?>" title="<?php echo JText::_('JEV_TIT_PENDING'); ?>" />
		</td>
		<td>
			<?php echo JText::_('JEV_PUB_BUT_COMING'); ?>
			&nbsp;|
		</td>
		<td>
			<img src="<?php echo $pathIMG; ?>publish_g.png" width="12" height="12"  alt="Visible" />
		</td>
		<td>
			<?php echo JText::_('JEV_PUB_ACTUAL'); ?>
			&nbsp;|
		</td>
		<td>
			<img src="<?php echo $pathIMG; ?>publish_r.png" width="12" height="12"  alt="Finished" />
		</td>
		<td>
			<?php echo JText::_('JEV_PUB_FINISHED'); ?>
			&nbsp;|
		</td>
		<td>
			<img src="<?php echo $pathIMG; ?>publish_x.png" width="12" height="12"  alt="Finished" />
		</td>
		<td><?php echo JText::_('JEV_NOT_PUBLISHED'); ?></td>
	</tr>
	<tr>
		<td colspan="8" align="center"><?php echo JText::_('JEV_CLICK_TO_CHANGE_STATUS'); ?></td>
	</tr>
</table>
<?php		

