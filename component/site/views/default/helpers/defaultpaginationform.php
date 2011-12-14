<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultPaginationForm($total, $limitstart, $limit, $keyword=""){
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);
	if ($keyword !="" && method_exists($pageNav,"setAdditionalUrlParam")){		
		$pageNav->setAdditionalUrlParam("keyword", urlencode($keyword));
		$pageNav->setAdditionalUrlParam("showpast",JRequest::getInt("showpast",0));
	}
	$Itemid = JRequest::getInt("Itemid");
	$task = JRequest::getVar("jevtask");
	$link = JRoute::_("index.php?option=".JEV_COM_COMPONENT."&Itemid=$Itemid&task=$task");
	?>
	<div class="jev_pagination">
	<form action="<?php echo $link;?>" method="post">
	<?php
	// TODO add in catids so that changing it doesn't look the data
	echo $pageNav->getListFooter(); 
	?>
	</form>
	</div>
	<?php
}

