<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

if (JVersion::isCompatible("1.6.0")) return;
// Redirect to CPanel
?>
<script  type="text/javascript" language="javascript">
	document.location.replace("<?php echo JURI::root();?>administrator/index.php?option=com_jevents");
</script>
