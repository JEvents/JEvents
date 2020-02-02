<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;

$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

if (is_array($data['options']))
{
	$data['options'] = new Registry($data['options']);
}

// Options
$filterButton = $data['options']->get('filterButton', true);
$searchButton = $data['options']->get('searchButton', true);

$filters = $data['view']->filterForm->getGroup('filter');
?>

<?php if (!empty($filters['filter_search'])) : ?>
	<?php if ($searchButton) : ?>
		<label for="filter_search" class="gsl-hidden">
			<?php if (isset($filters['filter_search']->label)) : ?>
				<?php echo Text::_($filters['filter_search']->label); ?>
			<?php else : ?>
				<?php echo Text::_('JSEARCH_FILTER'); ?>
			<?php endif; ?>
		</label>
			<?php
            $search_input = $filters['filter_search']->input;
			$tooltip = ' data-yspoptitle = "' . Text::_('JSEARCH_FILTER', true). '"'
				. '  data-yspopcontent = "' . Text::_($filters['filter_search']->description, true) . '" ';
			if ($filters['filter_search']->description) :
				$search_input = str_replace('<input ', '<input class="hasYsPopover gsl-input" ' . $tooltip . ' ', $search_input);
            endif;
			echo trim($search_input);
			?><button type="submit" class="gsl-button ys-tooltip"
                    title="<?php echo HTMLHelper::_('tooltipText', 'JSEARCH_FILTER_SUBMIT'); ?>"
                    aria-label="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
				<span gsl-icon="icon: search" aria-hidden="true"></span>
			</button>
        <?php if ($filterButton) : ?>
            <button type="button" class=" gsl-button ys-tooltip  js-stools-btn-filter"
                        title="<?php echo HTMLHelper::_('tooltipText', 'JSEARCH_TOOLS_DESC'); ?>">
					<?php echo Text::_('JSEARCH_TOOLS');?> <span gsl-icon="icon: triangle-down"></span>
				</button>
		<?php endif; ?>
			<button type="button" class="gsl-button ys-tooltip js-stools-btn-clear"
                    title="<?php echo HTMLHelper::_('tooltipText', 'JSEARCH_FILTER_CLEAR'); ?>">
				<?php echo Text::_('JSEARCH_FILTER_CLEAR');?>
			</button>
	<?php endif; ?>
<?php endif;
