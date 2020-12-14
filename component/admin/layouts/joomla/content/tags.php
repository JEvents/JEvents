<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

if (GSLMSIE10)
{
	include (JPATH_SITE . "/layouts/joomla/content/" .  basename(__FILE__));
	return;
}

use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');

$authorised = Factory::getUser()->getAuthorisedViewLevels();

?>
<?php if (!empty($displayData)) : ?>
    <ul class="tags gsl-inline gsl-list gsl-button-group">
		<?php foreach ($displayData as $i => $tag) : ?>
			<?php if (in_array($tag->access, $authorised)) : ?>
				<?php $tagParams = new Registry($tag->params); ?>
				<?php $link_class = $tagParams->get('tag_link_class', '') . ' gsl-button gsl-button-xsmall gsl-button-primary'; ?>
                <li class="tag-<?php echo $tag->tag_id; ?> tag-list<?php echo $i; ?>" itemprop="keywords">
                    <a href="<?php echo Route::_("index.php?option=com_jevents&view=sites&filter[tag][]=$tag->tag_id"); ?>" class="<?php echo $link_class; ?>">
						<?php echo $this->escape($tag->title); ?>
                    </a>
                </li>
			<?php endif; ?>
		<?php endforeach; ?>
    </ul>
<?php endif; ?>