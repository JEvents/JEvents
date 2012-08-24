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
	// include catids to make sure not lost when category is pre-selected
	$catids = JRequest::getString("catids","");
	if (strlen($catids)>0){
		$catids = explode("|",$catids);
		JArrayHelper::toInteger($catids);
		$catids = "&catids=".implode("|",$catids);
	}	
	$link = JRoute::_("index.php?option=".JEV_COM_COMPONENT."&Itemid=$Itemid&task=$task$catids");
	?>
	<div class="jev_pagination">
	<form action="<?php echo $link;?>" method="post">
	<?php
	echo $pageNav->getListFooter(); 
	?>
	</form>
	</div>
	<?php
}

