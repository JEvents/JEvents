<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$data = $displayData;

// Load the form filters
$filters = $data['view']->filterForm->getGroup('filter');
$customfilters =  $data['view']->filters;
if ($filters)
{
    foreach ($filters as $fieldName => $field)
    {
        if ($fieldName !== 'filter_search')
        {
            $dataShowOn = '';
            if ($field->showon)
            {
                JHtml::_('jquery.framework');
                JHtml::_('script', 'jui/cms.js', array('version' => 'auto', 'relative' => true));
                $dataShowOn = " data-showon='" . json_encode(JFormHelper::parseShowOnConditions($field->showon, $field->formControl, $field->group)) . "'";
            }
?><div class="js-stools-field-filter"<?php echo $dataShowOn; ?>><?php
	        $customFilterName = str_replace('filter_', '', $fieldName);
            if (isset($customfilters[$customFilterName]))
            {
                echo $customfilters[$customFilterName];
            }
            else
            {
                echo $field->input;
            }
?></div><?php
        }
    }
}