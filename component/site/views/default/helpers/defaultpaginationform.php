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
	$catids = JRequest::getString("catids",JRequest::getString("category_fv",""));
	if (strlen($catids)>0){
		$catids = explode("|",$catids);
		JArrayHelper::toInteger($catids);
		$catids = "&catids=".implode("|",$catids);
	}	
	$link = JRoute::_("index.php?option=".JEV_COM_COMPONENT."&Itemid=$Itemid&task=$task$catids");
	?>
	<div class="jev_pagination">
	<form action="<?php echo $link;?>" method="post" xxx="1" name="adminForm" id="adminForm">
	<?php
	if ($task!="crawler.listevents" || version_compare(JVERSION, "3.0.0", 'lt') ){
	echo $pageNav->getListFooter();
	}
	else {
		// Allow to receive a null layout
		$layoutId =  'pagination.crawlerlinks' ;

		$app = JFactory::getApplication();

		$list = array(
			'prefix'       => $pageNav->prefix,
			'limit'        => $pageNav->limit,
			'limitstart'   => $pageNav->limitstart,
			'total'        => $pageNav->total,
			'limitfield'   => $pageNav->getLimitBox(),
			'pagescounter' => $pageNav->getPagesCounter(),
			'pages'        => $pageNav->getPaginationPages() 
		);

		$options = array();
		
		echo  JLayoutHelper::render($layoutId, array('list' => $list, 'options' => $options));

	}
	?>
	</form>
	</div>
	<?php
}

