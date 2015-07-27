<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = JEVConfig::getInstance();

$view =  $this->getViewName();
$this->dataModel = new JEventsDataModel("JEventsAdminDBModel");
$this->queryModel = new JEventsDBModel($this->dataModel);

JFactory::getDocument()->addStyleDeclaration("#main {min-height:auto;}");

$action = JFactory::getApplication()->isAdmin()?"index.php":JURI::root()."index.php?option=".JEV_COM_COMPONENT."&Itemid=".JEVHelper::getItemid();

?>
<div id="jevents">
<div class="p10px jevbootstrap">

<script type="text/javascript" >
function submitbutton() {
	var form = document.ical;
	
	// do field validation
	if (form.upload.value == "" && form.uploadURL.value == "" ){
		alert( "<?php echo JText::_( 'JEV_MISSING_FILE_AND_URL_SELECTION',true ); ?>" );
	}	
	else if (form.catid && form.catid.value==0 && form.catid.options && form.catid.options.length){
		alert ( '<?php echo JText::_('JEV_SELECT_CATEGORY',true) ; ?>' );
	}
	else if (form.icsid.value == "0"){
		alert( "<?php echo JText::_('JEV_MISSING_ICAL_SELECTION' ,true); ?>" );
	}
	else {
		submitform();
		return true;
	}
	return false;
}
</script>

<form name="ical" method="post" accept-charset="UTF-8" enctype="multipart/form-data" onsubmit="return submitbutton()" class="adminform">

	<div>
		<strong><?php echo JText::_("JEV_FROM_FILE");?></strong><br/>
		<input class="inputbox" type="file" name="upload" id="upload" size="30" />
	</div>
	<br/>
	<div>
		<strong><?php echo JText::_("JEV_FROM_URL");?></strong><br/>
		<input class="inputbox" type="text" name="uploadURL" id="uploadURL" size="30" />
	</div>

	<?php 	if ($this->clistChoice){?>
			<script type="text/javascript" >
			function preselectCategory(select){
				var lookup = new Array();
				lookup[0]=0;
				<?php
				foreach ($this->nativeCals as $nc) {
					echo 'lookup['.$nc->ics_id.']='.$nc->catid.';';
				}
				?>
				document.ical['catid'].value=lookup[select.value];
			}
			</script>
	        <strong><?php  echo JText::_("Select Ical (from raw icals)"); ?></strong><br/>
			<?php  
	    }
		if ($this->clist){
			echo $this->clist."<Br/>";
		} ?>
	  <strong><?php  echo JText::_( 'SELECT_CATEGORY' ); ?></strong><br/>
    <?php    echo JEventsHTML::buildCategorySelect(0, '', $this->dataModel->accessibleCategoryList(), false, true,0,'catid',JEV_COM_COMPONENT, $this->excats); ?><br/>
	<br/>
	<div>
		<strong><?php echo JText::_('JEV_IGNORE_EMBEDDED_CATEGORIES'); ?></strong>
		 <label for="ignoreembedcat0"  style="display:inline;">
			<input id="ignoreembedcat0" type="radio" value="0" name="ignoreembedcat" checked="checked"/>
			<?php echo JText::_( 'JEV_NO' ); ?>
		 </label>
		 <label for="ignoreembedcat1"  style="display:inline;">
			<input id="ignoreembedcat1" type="radio" value="1" name="ignoreembedcat" />
			<?php echo JText::_( 'JEV_YES' ); ?>
		 </label>
	 </div>
	 <br/>
	 <br/>
    
     <input type="submit" name="submit" value="<?php echo JText::_('JEV_IMPORT', true)?>" />

     <input type="hidden" name="task" value="icals.importdata" />
     <input type="hidden" name="option" value="com_jevents" />
	<?php echo JHTML::_( 'form.token' ); ?>          
</form>
</div>
</div>
<?php
/*
// Load Bootstrap
JevHtmlBootstrap::framework();

//JHtml::_('behavior.formvalidation');
$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
if ($params->get("bootstrapchosen", 1))
{
	JHtml::_('formbehavior.chosen', '#jevents select:not(.notchosen)');
}
if ($params->get("bootstrapcss", 1)==1)
{
	// This version of bootstrap has maximum compatability with JEvents due to enhanced namespacing
	JHTML::stylesheet("com_jevents/bootstrap.css", array(), true);
}
else if ($params->get("bootstrapcss", 1)==2)
{
	JHtmlBootstrap::loadCss();
}

*/