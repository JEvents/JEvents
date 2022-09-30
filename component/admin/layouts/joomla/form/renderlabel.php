<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   (C) 2014 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

extract($displayData);

/**
 * Layout variables
 * ---------------------
 * 	$text         : (string)  The label text
 * 	$description  : (string)  An optional description to use in a tooltip
 * 	$for          : (string)  The id of the input this label is for
 * 	$required     : (boolean) True if a required field
 * 	$classes      : (array)   A list of classes
 * 	$position     : (string)  The tooltip position. Bottom for alias
 */

$classes = array_filter((array) $classes);

$id = $for . '-lbl';
$title = '';

if (!empty($description))
{
	if ($text && $text !== $description)
	{
	//	JHtml::_('bootstrap.popover');
        JLoader::register('JevModal',JPATH_LIBRARIES."/jevents/jevmodal/jevmodal.php");
		JevModal::popover('.hasYsPopover', array("trigger" => "hover focus", "placement" => "top", "container" => "#jevents_body, body", "delay" => array("show" => 150, "hide" => 150)));

		$classes[] = 'hasYsPopover';
		$title     = ' data-yspoptitle="' . htmlspecialchars(trim($text, ':')) . '"'
			. ' data-yspopcontent="'. htmlspecialchars($description) . '"';

		if (version_compare(JVERSION, '4.0', 'lt'))
		{
			$title     .= ' data-original-title="' . htmlspecialchars(trim($text, ':')) . '"'
				. ' data-content="'. htmlspecialchars($description) . '"';
		}

		if (!$position && JFactory::getLanguage()->isRtl())
		{
			$position = ' data-placement="left" ';
		}
	}
	else
	{
		JevModal::popover('.hasYsTooltip', array("trigger" => "hover focus", "placement" => "top", "container" => "#jevents_body, body", "delay" => array("show" => 150, "hide" => 150)));

		$classes[] = 'hasYsTooltip';
		$title     = ' data-yspopcontent="' .htmlspecialchars($description . $text) . '"';
	}
}

if ($required)
{
	$classes[] = 'required';
}

?>
<label id="<?php echo $id; ?>" for="<?php echo $for; ?>"<?php if (!empty($classes)) echo ' class="' . implode(' ', $classes) . '"'; ?><?php echo $title; ?><?php echo $position; ?>>
	<?php echo $text; ?><?php if ($required) : ?><span class="star">&#160;*</span><?php endif; ?>
</label>

<?php
/*
$classes = array_filter((array) $classes);

$id = $for . '-lbl';
$title = '';

if (!empty($description))
{
	if ($text && $text !== $description)
	{
		JevModal::popover('.hasPopover', array("trigger" => "hover focus", "placement" => "top", "container" => "#jevents_body", "delay" => array("show" => 150, "hide" => 150)));

		$classes[] = 'hasPopover';
		$title     = ' data-yspoptitle="' . htmlspecialchars(trim($text, ':')) . '"'
			. ' data-yspopcontent="'. htmlspecialchars($description) . '"';

		if (!$position && JFactory::getLanguage()->isRtl())
		{
			$position = ' data-placement="left" ';
		}
	}
	else
	{
		JevModal::popover('.hasPopover', array("trigger" => "hover focus", "placement" => "top", "container" => "#jevents_body", "delay" => array("show" => 150, "hide" => 150)));

		$classes[] = 'hasTooltip';
		$title     = ' data-yspoptitle="' . JHtml::_('tooltipText', trim($text, ':'), $description, 0) . '"';
	}
}

if ($required)
{
	$classes[] = 'required';
}

?>
<label id="<?php echo $id; ?>" for="<?php echo $for; ?>"<?php if (!empty($classes)) echo ' class="' . implode(' ', $classes) . '"'; ?><?php echo $title; ?><?php echo $position; ?>>
	<?php echo $text; ?><?php if ($required) : ?><span class="star">&#160;*</span><?php endif; ?>
</label>
*/