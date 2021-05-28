<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\String\StringHelper;

$cfg    = JEVConfig::getInstance();
$app    = Factory::getApplication();
$input  = $app->input;

if (0 == $this->evid)
{

	$Itemid = $input->getInt('Itemid');
	$app->redirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$this->year&month=$this->month&day=$this->day&Itemid=$Itemid", false));

	return;
}

if (is_null($this->data))
{

	Factory::getApplication()->enqueueMessage(Text::_("JEV_SORRY_UPDATED"), 'warning');
	Factory::getApplication()->redirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=$this->Itemid", false));
}

if (array_key_exists('row', $this->data))
{
	$row = $this->data['row'];

	$mask = $this->data['mask'];
	$page = 0;

	$cfg = JEVConfig::getInstance();

	$params     = new JevRegistry(null);

	if (isset($row))
	{
		$customresults = $app->triggerEvent('onDisplayCustomFields', array(&$row));

		// Dynamic Page Title
		$this->setPageTitle($row->title());

		$templated = $this->loadedFromTemplate('icalevent.detail_body', $row, $mask);
		if (!$templated && count($customresults) > 0)
		{
			?>
			<div class="jev_evdt">
				<?php
				foreach ($customresults as $result)
				{
					if (is_string($result) && StringHelper::strlen($result) > 0)
					{
						echo "<div>" . $result . "</div>";
					}
				}
				?>
			</div>
			<?php
		}
		$results = $app->triggerEvent('onAfterDisplayContent', array(&$row, &$params, $page));
		echo trim(implode("\n", $results));
	}
	else
	{ ?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td class="contentheading" align="left"
				    valign="top"><?php echo Text::_('JEV_REP_NOEVENTSELECTED'); ?></td>
			</tr>
		</table>
		<?php
	}
	/*
			if(!($mask & MASK_BACKTOLIST)) { ?>
				<p align="center">
					<a href="javascript:window.history.go(-1);" class="jev_back btn" title="<?php echo Text::_('JEV_BACK'); ?>"><?php echo Text::_('JEV_BACK'); ?></a>
				</p>
				<?php
			}
	*/

}
