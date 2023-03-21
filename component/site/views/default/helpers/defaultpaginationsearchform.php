<?php
defined('_JEXEC') or die('Restricted access');

function DefaultPaginationSearchForm($total, $limitstart, $limit)
{

	jimport('joomla.html.pagination');
	$pageNav = new \Joomla\CMS\Pagination\Pagination($total, $limitstart, $limit);
	?>
	<div class="jev_pagination">
		<?php
		echo $pageNav->getPaginationLinks('joomla.pagination.links', array('showLimitBox' => true, 'showPagesLinks'=> true, 'showLimitStart' => true));
		?>
	</div>
	<?php
}

