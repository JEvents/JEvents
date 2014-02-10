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

	$dispatcher	= JDispatcher::getInstance();
	$params =new JRegistry(null);

	if (isset($row)) {
            $customresults = $dispatcher->trigger( 'onDisplayCustomFields', array( &$row) );
			if (!$this->loadedFromTemplate('icalevent.detail_body', $row, $mask)){
	                $jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
            ?>
            <div class="event_details">
	<h2 ><?php echo $row->title(); ?></h2>
					<?php
					if (($jevparams->get("showicalicon",0) &&  !$jevparams->get("disableicalexport",0)) || ($row->canUserEdit() && !( $mask & MASK_POPUP ))){
						?>
<ul class="actions">
					<?php
		                if ($jevparams->get("showicalicon",0) &&  !$jevparams->get("disableicalexport",0) ){
						?>
	<li class="ical-icon">
					<?php
					JEVHelper::script( 'view_detail.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );
					?>
		<a href="javascript:void(0)" onclick='clickIcalButton()' title="<?php echo JText::_('JEV_SAVEICAL');?>">
			<img src="<?php echo JURI::root().'components/'.JEV_COM_COMPONENT.'/assets/images/jevents_event_sml.png'?>" align="middle" name="image"  alt="<?php echo JText::_('JEV_SAVEICAL');?>" style="height:24px;"/>
		</a>
		<div style="position:relative;">
		<?php
		$this->eventIcalDialog($row, $mask);
		?>
		</div>
	</li>
						<?php
						}
						if( $row->canUserEdit() && !( $mask & MASK_POPUP )) {
								JEVHelper::script( 'view_detail.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );
								?>
	<li class="edit-icon">
		<a href="javascript:void(0)" onclick='clickEditButton()' title="<?php echo JText::_('JEV_E_EDIT');?>">
			<?php echo JEVHelper::imagesite( 'edit.png',JText::_('JEV_E_EDIT'));?>
		</a>
		<div style="position:relative;">
		<?php
		$this->eventManagementDialog($row, $mask);
		?>
		</div>
	</li>
						<?php
						}
					}
				?>
</ul>
		<div class="event_details_m">
				<?php
				$hastd = false;
				if( $cfg->get('com_repeatview') == '1' ){
					echo '<div class="repeat" >';
					echo $row->repeatSummary();
					echo $row->previousnextLinks();
					echo "</div>";
					$hastd = true;
				}
				if( $cfg->get('com_byview') == '1' ){
					echo '<div class="contact" >';
					echo JText::_('JEV_BY') . '&nbsp;' . $row->contactlink();
					echo "</div>";
					$hastd = true;
				}
				if( $cfg->get('com_hitsview') == '1' ){
					echo '<div class="hits" >';
					echo JText::_('JEV_EVENT_HITS') . ' : ' . $row->hits();
					echo "</td>";
					$hastd = true;
				}

				?>
		</div>
                <?php echo $row->content(); ?>
                <?php
                if ($row->hasLocation() || $row->hasContactInfo()) { ?>
					<div class="ev_detail location" >
						<?php
						if( $row->hasLocation() ){
							echo "<strong>".JText::_('JEV_EVENT_ADRESSE')." : </strong>". $row->location();
						}

						if( $row->hasContactInfo()){
							if(  $row->hasLocation()){
								echo "<br/>";
							}
							echo "<strong>".JText::_('JEV_EVENT_CONTACT')." : </strong>". $row->contact_info();
						} ?>
					</div>
                    <?php
                }

                if( $row->hasExtraInfo()){ ?>
                        <div class="ev_detail extrainfo" ><?php echo $row->extra_info(); ?></div>
                    <?php
                } ?>
	            <?php
	            if (count($customresults)>0){
	            	foreach ($customresults as $result) {
	            		if (is_string($result) && strlen($result)>0){
	            			echo "<div>".$result."</div>";
	            		}	            		
	            	}
	            }
				?>
                
            <?php
		} // end if not loaded from template
            $results = $dispatcher->trigger( 'onAfterDisplayContent', array( &$row, &$params, $page ) );
            echo trim( implode( "\n", $results ) );

        } else { ?>
			<h2>
                <?php echo JText::_('JEV_REP_NOEVENTSELECTED'); ?>
			</h2>
            <?php
        }

		if(!($mask & MASK_BACKTOLIST)) { ?>
    		<p align="center">
    			<a href="javascript:window.history.go(-1);" class="jev_back" title="<?php echo JText::_('JEV_BACK'); ?>"><?php echo JText::_('JEV_BACK'); ?></a>
    		</p>
    		</div>
    		<?php
		}
	

}
