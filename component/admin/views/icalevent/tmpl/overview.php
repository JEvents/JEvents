<?php 
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: overview.php 1676 2010-01-20 02:50:34Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

$db	=& JFactory::getDBO();
$user =& JFactory::getUser();

// get configuration object
$cfg = & JEVConfig::getInstance();
$this->_largeDataSet = $cfg->get('largeDataSet', 0 );

$pathIMG = JURI::root() . 'administrator/images/'; ?>

<form action="index.php" method="post" name="adminForm">
	<table cellpadding="4" cellspacing="0" border="0" >
		<tr>
			<?php if (!$this->_largeDataSet) { ?>
			<td align="right" width="100%"><?php echo JText::_('JEV_HIDE_OLD_EVENTS');?> </td>
			<td align="right"><?php echo $this->plist;?></td>
			<?php } ?>
			<td align="right"><?php echo $this->clist;?> </td>
			<td align="right"><?php echo $this->icsList;?> </td>
			<td align="right"><?php echo $this->statelist;?> </td>
			<td align="right"><?php echo $this->userlist;?> </td>
			<td><?php echo JText::_('JEV_SEARCH'); ?>&nbsp;</td>
			<td>
				<input type="text" name="search" value="<?php echo $this->search; ?>" class="inputbox" onChange="document.adminForm.submit();" />
			</td>
		</tr>
	</table>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<tr>
			<th width="20" nowrap="nowrap">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows ); ?>);" />
			</th>
			<th class="title" width="50%" nowrap="nowrap"><?php echo JText::_('JEV_ICAL_SUMMARY'); ?></th>
			<th width="10%" nowrap="nowrap"><?php echo JText::_("Repeats"); ?></th>
			<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_EVENT_CREATOR'); ?></th>
			<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_PUBLISHED'); ?></th>
			<th width="20%" nowrap="nowrap"><?php echo JText::_('JEV_TIME_SHEET'); ?></th>
			<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_ACCESS'); ?></th>
		</tr>

        <?php
        $k 			= 0;
        $nullDate 	= $db->getNullDate();

        for( $i=0, $n=count( $this->rows ); $i < $n; $i++ ){
        	$row = &$this->rows[$i]; ?>
            <tr class="row<?php echo $k; ?>">
            	<td width="20" style="background-color:<?php echo JEV_CommonFunctions::setColor($row);?>">
                    <input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id(); ?>" onclick="isChecked(this.checked);" />
            	</td>
              	<td >
              		<a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','icalevent.edit')" title="<?php echo JText::_('JEV_CLICK_TO_EDIT'); ?>"><?php echo $row->title(); ?></a>
              	</td>
              	<td align="center">
              	<?php
              	if ($row->hasrepetition()){
              	?>
          	    	<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','icalrepeat.list')"><img src="<?php echo $pathIMG . "copy_f2.png"; ?>" width="12" height="12" border="0" alt="" /></a>    
          	    <?php }?>
              	</td>
              	<td align="center"><?php echo $row->creatorName();?></td>
              	<td align="center">
              	<?php  
              	if (!$row->state()){
              		$img = 'publish_x.png';
              	}
              	else {
              		$now = JFactory::getDate();
              		$now = $now->toMySQL();
              		if ( $now <= $row->publish_up() ) {
              			// Published and in the future
              			$img = 'publish_y.png';
              		}
              		else if(  $now <= $row->publish_down() || $row->publish_down() == $nullDate  ) {
              			// Current
              			$img = 'publish_g.png';
              		}
              		else if ( $now > $row->publish_down()) {
              			// Expired
              			$img = 'publish_r.png';
              		}
              	}
              	?>
              	<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $row->state() ? 'icalevent.unpublish' : 'icalevent.publish'; ?>')"><img src="<?php echo $pathIMG . $img; ?>" width="16" height="16" border="0" alt="" /></a>
              	</td>
              	<td >
              	<?php
              	if ($this->_largeDataSet){
					echo JText::_('JEV_FROM') . ' : '. $row->publish_up();
              	}
              	else {
              		$times = '<table style="border: 1px solid #666666; width:100%;">';
              		$times .= '<tr><td>' . JText::_('JEV_FROM') . ' : '. ($row->alldayevent()?substr($row->publish_up(),0,10):$row->publish_up()).'</td></tr>';
              		$times .= '<tr><td>' . JText::_('JEV_TO') . ' : ' . (($row->noendtime() || $row->alldayevent())?substr($row->publish_down(),0,10):$row->publish_down()). '</td></tr>';
              		$times .="</table>";
              		echo $times;
              	}
				?>
              	</td>
              	<td align="center"><?php echo $row->_groupname;?></td>
            </tr>
            <?php
            $k = 1 - $k;
        } ?>
    	<tr>
    		<th align="center" colspan="9"><?php echo $this->pageNav->getListFooter(); ?></th>
    	</tr>
    </table>
    <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
    <input type="hidden" name="task" value="icalevent.list" />
    <input type="hidden" name="boxchecked" value="0" />
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

