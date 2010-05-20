<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultPaginationSearchForm($total, $limitstart, $limit){
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);
	?>
	<div class="jev_pagination">
	<?php
	echo $pageNav->getListFooter(); 
	?>
	</div>
	<?php
}

