<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Router\Router;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\String\StringHelper;

function DefaultViewEventCatRowNew($view, $row, $args = "")
{

	$input = Factory::getApplication()->input;

	// I choost not to use $row->fgcolor()
	$fgcolor = "inherit";

	$router         = Router::getInstance("site");
	$vars           = $router->getVars();
	$vars["catids"] = $row->catid();

	if (array_key_exists("Itemid", $vars) && is_null($vars["Itemid"]))
	{
		$vars["Itemid"] = $input->getInt("Itemid", 0);
	}
	$eventlink = "index.php?";
	foreach ($vars as $key => $val)
	{
		$eventlink .= $key . "=" . $val . "&";
	}
	$eventlink = StringHelper::substr($eventlink, 0, StringHelper::strlen($eventlink) - 1);
	$eventlink = Route::_($eventlink);
	?>
	<a class="ev_link_cat" href="<?php echo $eventlink; ?>" style="color:<?php echo $fgcolor; ?>;"
	   title="<?php echo JEventsHTML::special($row->catname()); ?>"><?php echo $row->catname(); ?></a>
	<?php
}