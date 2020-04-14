<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;

function DefaultPaginationForm($total, $limitstart, $limit, $keyword = "")
{

	jimport('joomla.html.pagination');

	$input = Factory::getApplication()->input;

	$pageNav = new \Joomla\CMS\Pagination\Pagination($total, $limitstart, $limit);
	if ($keyword != "" && method_exists($pageNav, "setAdditionalUrlParam"))
	{
		$pageNav->setAdditionalUrlParam("keyword", urlencode($keyword));
		$pageNav->setAdditionalUrlParam("showpast", $input->getInt("showpast", 0));
	}
	$Itemid = $input->getInt("Itemid");
	$task   = $input->get("jevtask", null, null);
	// include catids to make sure not lost when category is pre-selected
	$catids = $input->getString("catids", $input->getString("category_fv", ""));
	if (\Joomla\String\StringHelper::strlen($catids) > 0)
	{
		$catids = explode("|", $catids);
		$catids = ArrayHelper::toInteger($catids);
		$catids = "&catids=" . implode("|", $catids);
	}
	$year = "";
	if ($input->getInt("year", 0) > 0)
	{
		$year = "&year=" . $input->getInt("year", 0);
	}
	$month = "";
	if ($input->getInt("month", 0) > 0)
	{
		$month = "&month=" . $input->getInt("month", 0);
	}
	if ($keyword != "")
	{
		$keyword = "&keyword=" . urlencode($keyword) . "&showpast=" . $input->getInt("showpast", 0);
	}
	$link = Route::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=$Itemid&task=$task$catids$year$month$keyword");
	?>
	<div class="jev_pagination">
		<form action="<?php echo $link; ?>" method="post" name="adminForm" id="adminForm">
			<?php
			if ($task !== "crawler.listevents")
			{
				echo '<label class="sr-only" for="limit">' . Text::_("JEV_PAGINATION_LIMIT_LBL") . '</label>';
				echo $pageNav->getListFooter();
			}
			else
			{
				// Allow to receive a null layout
				$layoutId = 'pagination.crawlerlinks';

				$app = Factory::getApplication();

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

				echo LayoutHelper::render($layoutId, array('list' => $list, 'options' => $options));

			}
			?>
		</form>
	</div>
	<?php
}

