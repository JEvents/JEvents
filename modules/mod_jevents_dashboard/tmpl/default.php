<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

if (!defined("GSLMSIE10"))
{
	define("GSLMSIE10", 0);
}

$lang         = Factory::getLanguage();
$lang->load("com_jevents", JPATH_ADMINISTRATOR);

include_once JPATH_ADMINISTRATOR . "/components/com_jevents/helpers/gslhelper.php";

$links = GslHelper::getLeftIconLinks();

//use Joomla\CMS\Layout\LayoutHelper;

//echo LayoutHelper::render('gslframework.header', null, JPATH_ADMINISTRATOR. "/components/com_jevents/layouts" );

?>
<ul class="nav flex-column">
<?php
foreach ($links as $link)
{
	$icons = array('calendar' => 'icon-calendar', 'album' => 'icon-folder-open', 'file-edit' => 'icon-pencil', 'file-text' => 'icon-info', 'paint-bucket' => 'icon-brush',
	               'hashtag' => 'fa-hashtag', 'credit-card' => 'fa-credit-card', 'settings' => 'icon-cogs', 'thumbnails' => 'fa-file-image',
	               'cart' => 'icon-cart', 'code' => 'icon-code', 'joomla' => 'icon-joomla', 'user' => 'icon-users', 'location' => 'icon-location',
	               'calendars' => 'icon-calendar-3');
	$missingicon = "";
	$icon = "";
	if (isset($link->iconSrc) && strpos($link->iconSrc, 'yoursites'))
	{
		continue;
	}
	if (isset($icons[$link->icon]))
	{
		if ($link->icon == 'joomla')
		{
			continue;
		}
		$icon = $icons[$link->icon];
	}
	else
	{
	//	$missingicon = " : " . $link->icon;
	}
	if (isset($link->sublinks) && count($link->sublinks) > 0)
	{
		?>
		<li class="nav-item d-flex">
			<a class="nav-link" href="<?php echo $link->link;?>" aria-label="<?php echo htmlspecialchars($link->label);?>">
				<span class="<?php echo $icon;?> icon-fw" aria-hidden="true"></span>
				<span class="nav-item-title"><?php echo $link->label;?><?php echo $missingicon;?></span>
			</a>
			<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
				<span class="icon-plus icon-fw" aria-hidden="true"></span>
			</a>
			<ul class="dropdown-menu">
				<?php
				foreach ($link->sublinks as $sublink)
				{
					$missingicon = "";
					$icon = "";

					if (isset($icons[$sublink->icon]))
					{
						$icon = $icons[$sublink->icon];
					}
					else
					{
							$missingicon = " : " . $sublink->icon;
					}

					?>
				<li class="">
					<a class="dropdown-item" href="<?php echo $sublink->link;?>" aria-label="<?php echo htmlspecialchars($sublink->label);?>">
						<span class="<?php echo $icon;?> icon-fw" aria-hidden="true"></span>
						<span class="nav-item-title"><?php echo $sublink->label;?><?php echo $missingicon;?></span>
					</a>
				</li>
					<?php
				}
				?>
			</ul>
		</li>
		<?php
	}
	else
	{
		?>
		<li class="nav-item">
			<a class="nav-link" href="<?php echo $link->link;?>" aria-label="<?php echo htmlspecialchars($link->label);?>">
				<span class="<?php echo $icon;?> icon-fw" aria-hidden="true"></span>
				<span class="nav-item-title"><?php echo $link->label;?><?php echo $missingicon;?></span>
			</a>
		</li>
		<?php
	}

}
?>
</ul>
<?php
//echo LayoutHelper::render('gslframework.footer', null, JPATH_ADMINISTRATOR. "/components/com_jevents/layouts" );
