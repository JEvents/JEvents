<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	= JEVConfig::getInstance();

if( 0 == $this->evid) {
	$Itemid = JRequest::getInt("Itemid");
	JFactory::getApplication()->redirect( JRoute::_('index.php?option=' . JEV_COM_COMPONENT. "&task=day.listevents&year=$this->year&month=$this->month&day=$this->day&Itemid=$Itemid",false));
	return;
}

if (is_null($this->data)){
	
	JFactory::getApplication()->redirect(JRoute::_("index.php?option=".JEV_COM_COMPONENT."&Itemid=$this->Itemid",false), JText::_("JEV_SORRY_UPDATED"));
}

if( array_key_exists('row',$this->data) ){
	$row=$this->data['row'];

	// Dynamic Page Title
	$this->setPageTitle($row->title());

	$mask = $this->data['mask'];
	$page = 0;

	
	$cfg	 = JEVConfig::getInstance();	

	$dispatcher	= JEventDispatcher::getInstance();
	$params =new JRegistry(null);

	if (isset($row)) {
        $customresults = $dispatcher->trigger( 'onDisplayCustomFields', array( &$row) );
		$this->loadedFromTemplate('icalevent.detail_body', $row, $mask);
            $results = $dispatcher->trigger( 'onAfterDisplayContent', array( &$row, &$params, $page ) );
            echo trim( implode( "\n", $results ) );

        } else { ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="contentheading"  align="left" valign="top"><?php echo JText::_('JEV_REP_NOEVENTSELECTED'); ?></td>
                </tr>
            </table>
            <?php
        }

		if(!($mask & MASK_BACKTOLIST)) { ?>
    		<p align="center">
    			<a href="javascript:window.history.go(-1);" class="jev_back btn" title="<?php echo JText::_('JEV_BACK'); ?>"><?php echo JText::_('JEV_BACK'); ?></a>
    		</p>
    		<?php
		}
	

}
