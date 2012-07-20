<?php 
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: edit.php 2768 2011-10-14 08:43:42Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die('Restricted access');

if ($this->item->name == "month.calendar_cell" || $this->item->name == "month.calendar_tip" )  {
	$editor =& JFactory::getEditor("none");
}
else {
	$editor =& JFactory::getEditor();
}

if (strpos($this->item->name, "com_")===0){
	$lang = JFactory::getLanguage();
	$parts = explode(".",$this->item->name);
	$lang->load($parts[0]);
}


if ($this->item->value=="" && file_exists(dirname(__FILE__).DS.$this->item->name.".html")) $this->item->value = file_get_contents(dirname(__FILE__).DS.$this->item->name.".html");
$this->replaceLabels($this->item->value);

?>		
<div id="jevents">
<form action="index.php" method="post" name="adminForm" >
<table width="90%" border="0" cellpadding="2" cellspacing="2" class="adminform" >
<tr>
<td>
		<input type="hidden" name="name" value="<?php echo $this->item->name;?>">
		
		<script type="text/javascript" language="Javascript">
			<?php if (JVersion::isCompatible("1.6.0")) {
				echo "Joomla.submitbutton = function (pressbutton) {\n";
			}
			else {
				echo "function submitbutton(pressbutton) {\n";
			}
			?>
			var form = document.adminForm;
			<?php echo $editor->getContent( 'value' ); ?>
			<?php
			// in case editor is toggled off - needed for TinyMCE
			echo $editor->save( 'value' );
			?>
			submitform(pressbutton);
		}

		</script>
        <div class="adminform" align="left">
       	<div style="margin-bottom:20px;">
	        <table cellpadding="5" cellspacing="0" border="0" >
    			<tr>
                	<td align="left"><?php echo JText::_( 'TITLE' ); ?>:</td>
                    <td colspan="2">
                    	<?php echo htmlspecialchars(JText::_($this->item->title), ENT_QUOTES, 'UTF-8'); ?>
                    	<!--<input class="inputbox" type="text" name="title" size="50" maxlength="100" value="<?php echo htmlspecialchars( $this->item->title, ENT_QUOTES, 'UTF-8'); ?>" />//-->
                    </td>
      			</tr>
    			<tr>
                	<td align="left"><?php echo JText::_( 'NAME' ); ?>:</td>
                    <td colspan="2">
                    	<?php echo htmlspecialchars( $this->item->name, ENT_QUOTES, 'UTF-8'); ?>
                    </td>
      			</tr>
                 <tr>
                 	<td valign="top" align="left">
                    <?php echo JText::_( 'JEV_LAYOUT' ); ?>
                    </td>
                    <td >
                    <?php
                    // parameters : areaname, content, hidden field, width, height, rows, cols
                    echo $editor->display( 'value',  htmlspecialchars( $this->item->value, ENT_QUOTES, 'UTF-8'), 700, 450, '70', '15', false) ;
                    ?>
                    </td>
                    <td valign="top">
		                 <?php
				$pattern = "#.*([0-9]*).*#";
				$name  = preg_replace("#\.[0-9]#","",$this->item->name);
		                 echo $this->loadTemplate($name);
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
<input type="hidden" name="task" value="defaults.edit" />
<input type="hidden" name="act" value="" />
<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
</form>
</div>