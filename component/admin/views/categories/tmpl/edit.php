<?php 
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: edit.php 2749 2011-10-13 08:54:34Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die('Restricted access');
 
$cfg = & JEVConfig::getInstance();

$editor =& JFactory::getEditor();

include_once(JEV_LIBS."/colorMap.php");

?>		
<div id="jevents">
<form action="index.php" method="post" name="adminForm"  id="adminForm">
<table width="90%" border="0" cellpadding="2" cellspacing="2" class="adminform">
<tr>
<td>
		<input type="hidden" name="cid[]" value="<?php echo $this->cat->id;?>">

		<input type="hidden" name="id" id="id" value="<?php echo $this->cat->id;?>">
		<script type="text/javascript" language="Javascript">
		// Joomla 1.5 only so leave as submitbutton
		function submitbutton(pressbutton) {
			if (pressbutton == 'cancel' || pressbutton == 'categories.list') {
				submitform( pressbutton );
				return;
			}
			var form = document.adminForm;
			<?php echo $editor->getContent( 'description' ); ?>
			// do field validation
			if (form.title.value == "") {
				alert ( "<?php echo html_entity_decode( JText::_('JEV_E_WARNTITLE') ); ?>" );
			}
			else {
				submitform(pressbutton);
			}
		}

		</script>
        <div class="adminform" align="left">
       	<div style="margin-bottom:20px;">
	        <table cellpadding="5" cellspacing="0" border="0" >
    			<tr>
                	<td align="left"><?php echo JText::_('JEV_CATEGORY_TITLE'); ?>:</td>
                    <td >
                    	<input class="inputbox" type="text" name="title" size="50" maxlength="100" value="<?php echo htmlspecialchars( $this->cat->title, ENT_QUOTES, 'UTF-8'); ?>" />
                    </td>
                    <td colspan="2">
                         <table id="pick1064797275" align="right" style="background-color:<?php echo $this->cat->getColor().';color:'.JevMapColor($this->cat->getColor()); ?>;border:solid 1px black;">
                            <tr>
                                <td width="80">
  									<div><?php echo JText::_('JEV_EVENT_COLOR'); ?></div>
									<input type="hidden" id="pick1064797275field" name="color" value="<?php echo $this->cat->getColor();?>"/>
									</td>

									<td  nowrap>
										<a id="colorPickButton" name ="colorPickButton" href="javascript:void(0)"  onclick="document.getElementById('fred').style.visibility='visible';"	  style="visibility:visible;color:<?php echo JevMapColor($this->cat->getColor()); ?>;font-weight:bold;"><?php echo JText::_('JEV_COLOR_PICKER'); ?></a>
										</td>
										<td>
			                    	<div style="position:relative;z-index:9999;">
									<iframe id="fred" frameborder="0" src="<?php echo JURI::root()."administrator/components/".JEV_COM_COMPONENT."/libraries/colours.html?id=fred";?>" style="position:absolute;width:300px!important;height:250px!important;visibility:hidden;z-index:9999;right:0px;top:0px;overflow:visible!important;"></iframe>
									</div>
                                </td>
                            </tr>
                        </table>
					</td>
      			</tr>
                <tr>
                	<td valign="top" align="left"><?php echo JText::_('JEV_CATEGORY_PARENT'); ?></td>
                    <td  >
                    <?php echo $this->plist;?>
                    </td>
                    <?php if (isset($this->glist)) {?>
                    <td align="right"><?php echo JText::_('JEV_EVENT_ACCESSLEVEL'); ?></td>
                    <td align="right"><?php echo $this->glist; ?></td>
                    <?php } 
                    else echo "<td/><td/>\n";?>
                 </tr>
                <tr>
                	<td valign="top" align="left"><?php echo JText::_('JEV_CATEGORY_ADMIN'); ?></td>
                    <td>
                    <?php echo $this->alist;?>
                    </td>
                	<td valign="top" align="left"><?php echo JText::_( 'PUBLISHED' ); ?></td>
                    <td>
                    <?php echo JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $this->cat->published );?>
                    </td>
                 </tr>
                <tr>
                	<td valign="top" align="left"><?php echo JText::_('JEV_BLOCK_OVERLAPS'); ?></td>
                    <td colspan="3">
                    <?php echo JHTML::_('select.booleanlist',  'overlaps', 'class="inputbox"', $this->cat->_catextra->overlaps );?>
                    </td>
                 </tr>
                 <tr>
                 	<td valign="top" align="left">
                    <?php echo JText::_('JEV_DESCRIPTION'); ?>
                    </td>
                    <td style="width:600px;" colspan="3">
                    <?php
                    // parameters : areaname, content, hidden field, width, height, rows, cols
                    echo $editor->display( 'description',  htmlspecialchars( $this->cat->description, ENT_QUOTES, 'UTF-8'), "100%", 250, '70', '10', array("readmore","pagebreak")) ;
                    ?>
                    </td>
                 </tr>
            </table>
		</div>
		</div>




</td>
</tr>  
</table>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="task" value="categories.edit" />
<input type="hidden" name="act" value="" />
<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
</form>
</div>