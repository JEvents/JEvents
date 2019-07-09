<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

extract($displayData);

?>
<td>
	<?php echo $extension->extension_id > 0 ? $extension->extension_id : $extension->slug; ?>
    <br>
	<?php echo $extension->id; ?>
	<?php
	$params = JComponentHelper::getParams("com_yoursites");

	if ($params->get("xdebug", 0) && !empty($extension->slug) && $extension->params->get('update') && !empty($extension->availableversion))
	{
		$packageURL    = $extension->params->get('update')->packageurl;
		$oldPackageURL = str_replace($extension->availableversion, $extension->currentversion, $packageURL);
		?>
        <br>
        <a href="<?php echo $this->escape($oldPackageURL); ?>">
			<?php echo $this->escape($oldPackageURL); ?>
        </a>
        <br>
        <a href="<?php echo $this->escape($packageURL); ?>">
			<?php echo $this->escape($packageURL); ?>
        </a>
		<?php
	}
	?>
</td>
