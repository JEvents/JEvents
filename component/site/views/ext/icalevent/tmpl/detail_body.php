<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	= & JEVConfig::getInstance();

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
	JFactory::getDocument()->SetTitle( $row->title() );

	$mask = $this->data['mask'];
	$page = 0;

	
	$cfg	 = & JEVConfig::getInstance();	

	$dispatcher	=& JDispatcher::getInstance();
	$params =new JParameter(null);

	if (isset($row)) {
        $customresults = $dispatcher->trigger( 'onDisplayCustomFields', array( &$row) );
		if (!$this->loadedFromTemplate('icalevent.detail_body', $row, $mask)){
            ?>
            <!-- <div name="events">  -->
            <table class="contentpaneopen" border="0">
                <tr class="headingrow">
                    <td  width="100%" class="contentheading"><?php echo $row->title(); ?></td>
	                <?php
	                $jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
	                if ($jevparams->get("showicalicon",0) &&  !$jevparams->get("disableicalexport",0) ){
	                ?>
	                <td  width="20" class="buttonheading" align="right">
						<?php
						JEVHelper::script( 'view_detail.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );
						?>
						<a href="javascript:void(0)" onclick='clickIcalButton()' title="<?php echo JText::_('JEV_SAVEICAL');?>">
							<img src="<?php echo JURI::root().'administrator/components/'.JEV_COM_COMPONENT.'/assets/images/jevents_event_sml.png'?>" align="middle" name="image"  alt="<?php echo JText::_('JEV_SAVEICAL');?>" style="height:24px;"/>
						</a>
					</td>
					<?php
	                }

					if( $row->canUserEdit() && !( $mask & MASK_POPUP )) {
							JEVHelper::script( 'view_detail.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );
                        	?>
                            <td  width="20" class="buttonheading" align="right">
                            <a href="javascript:void(0)" onclick='clickEditButton()' title="<?php echo JText::_('JEV_E_EDIT');?>">
                            	<?php echo JEVHelper::imagesite( 'edit.png',JText::_('JEV_E_EDIT'));?>
                            </a>
                            </td>
                            <?php
					}
						?>
                </tr>
                <tr class="dialogs">
                    <td align="left" valign="top" colspan="3">
                    <div style="position:relative;">
                    <?php
                    $this->eventIcalDialog($row, $mask);
                    ?>
                    </div>
                    </td>
                    <td align="left" valign="top">
                    <div style="position:relative;">
                    <?php
                    $this->eventManagementDialog($row, $mask);
                    ?>
                    </div>
                    </td>
                </tr>
                <tr>
                    <td align="left" valign="top" colspan="4">
                        <table width="100%" border="0">
                            <tr>
	                            <?php
	                            $hastd = false;
	                            if( $cfg->get('com_repeatview') == '1' ){ 
	                                echo '<td class="ev_detail repeat" >';
	                                echo $row->repeatSummary();
	                                echo $row->previousnextLinks();
	                                echo "</td>";
	                                $hastd = true;
	                            } 
	                            if( $cfg->get('com_byview') == '1' ){
	                                echo '<td class="ev_detail contact" >';
									echo JText::_('JEV_BY') . '&nbsp;' . $row->contactlink();
	                                echo "</td>";
	                                $hastd = true;
	                            } 
	                            if( $cfg->get('com_hitsview') == '1' ){
	                            	echo '<td class="ev_detail hits" >';
	                                echo JText::_('JEV_EVENT_HITS') . ' : ' . $row->hits();
	                                echo "</td>";
	                                $hastd = true;
	                            } 
	                            if (!$hastd){
	                            	echo "<td/>";
	                            }
	                            ?>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr align="left" valign="top">
                    <td colspan="4"><?php echo $row->content(); ?></td>
                </tr>
                <?php
                if ($row->hasLocation() || $row->hasContactInfo()) { ?>
                    <tr>
                        <td class="ev_detail" align="left" valign="top" colspan="4">
                            <?php
                            if( $row->hasLocation() ){
                            	echo "<b>".JText::_('JEV_EVENT_ADRESSE')." : </b>". $row->location();
                            }

                            if( $row->hasContactInfo()){
                            	if(  $row->hasLocation()){
                            		echo "<br/>";
                            	}
                            	echo "<b>".JText::_('JEV_EVENT_CONTACT')." : </b>". $row->contact_info();
                            } ?>
                        </td>
                    </tr>
                    <?php
                }

                if( $row->hasExtraInfo()){ ?>
                    <tr>
                        <td class="ev_detail" align="left" valign="top" colspan="4"><?php echo $row->extra_info(); ?></td>
                    </tr>
                    <?php
                } ?>
	            <?php
	            if (count($customresults)>0){
	            	foreach ($customresults as $result) {
	            		if (is_string($result) && strlen($result)>0){
	            			echo "<tr><td>".$result."</td></tr>";
	            		}	            		
	            	}
	            }
				?>
                
            </table>
            <!--  </div>  -->
            <?php
		} // end if not loaded from template
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
    			<a href="javascript:window.history.go(-1);" title="<?php echo JText::_('JEV_BACK'); ?>"><?php echo JText::_('JEV_BACK'); ?></a>
    		</p>
    		<?php
		}
	

}
