<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class com_jeventsInstallerScript
{

	public function preflight($action, $adapter)
	{
		return true;
	}

	public function update()
	{
		return true;
	}

	public function install($adapter)
	{
		return true;
	}

	public function postflight($action, $adapter)
	{
		?>
			<script  type="text/javascript" language="javascript">
				document.location.replace("<?php echo JURI::root();?>administrator/index.php?option=com_jevents");
			</script>
		<?php
	}

	public function uninstall($adapter)
	{
		return true;
	}

}
