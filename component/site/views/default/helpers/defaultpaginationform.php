<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultPaginationForm($total, $limitstart, $limit, $keyword=""){
	jimport('joomla.html.pagination');

	$jinput = JFactory::getApplication()->input;

	$pageNav = new JPagination($total, $limitstart, $limit);
	if ($keyword !="" && method_exists($pageNav,"setAdditionalUrlParam")){		
		$pageNav->setAdditionalUrlParam("keyword", urlencode($keyword));
		$pageNav->setAdditionalUrlParam("showpast", $jinput->getInt("showpast", 0));
	}
	$Itemid = $jinput->getInt("Itemid");
	$task = $jinput->get("jevtask", null, null);
	// include catids to make sure not lost when category is pre-selected
	$catids = $jinput->getString("catids", $jinput->getString("category_fv", ""));
	if (JString::strlen($catids)>0){
		$catids = explode("|",$catids);
		JArrayHelper::toInteger($catids);
		$catids = "&catids=".implode("|",$catids);
	}
	$year = "";
	if ($jinput->getInt("year",0)>0){
		$year = "&year=".$jinput->getInt("year",0);
	}
	$month = "";
	if ($jinput->getInt("month",0)>0){
		$month = "&month=".$jinput->getInt("month",0);
	}
	if ($keyword !=""){
		$keyword = "&keyword=".urlencode($keyword)."&showpast=".$jinput->getInt("showpast",0);
	}
	$link = JRoute::_("index.php?option=".JEV_COM_COMPONENT."&Itemid=$Itemid&task=$task$catids$year$month$keyword");
	?>
	<div class="jev_pagination">
	<form action="<?php echo $link;?>" method="post" name="adminForm" id="adminForm">
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

