<?php 
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit_month.calendar_tip.php 3333 2012-03-12 09:36:35Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

?>
<label><?php echo Text::_("JEV_PLUGIN_INSTRUCTIONS",true);?></label>
<select id="jevdefaults" class="gsl-select" onchange="defaultsEditorPlugin.insert('value','jevdefaults' )" ></select>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
defaultsEditorPlugin.node('#jevdefaults',"<?php echo Text::_("JEV_PLUGIN_SELECT",true);?>","");
// built in group
var optgroup = defaultsEditorPlugin.optgroup('#jevdefaults' , "<?php echo Text::_("JEV_CORE_DATA",true);?>");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_TITLE",true);?>", "TITLE");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_TRUNCTITLE",true);?>", "TRUNCTITLE");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_TTTIME",true);?>", "TTTIME");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_TITLE_LINK",true);?>", "TITLE_LINK");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_REPEATSUMMARY",true);?>", "REPEATSUMMARY");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_PREVIOUSNEXT",true);?>", "PREVIOUSNEXT");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CREATOR_LABEL",true);?>", "CREATOR_LABEL");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CREATOR",true);?>", "CREATOR");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_HITS",true);?>", "HITS");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_DESCRIPTION",true);?>", "DESCRIPTION");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_TRUNCATED_DESCRIPTION",true);?>", "TRUNCATED_DESC:20");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_LOCATION_LABEL",true);?>", "LOCATION_LABEL");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_LOCATION",true);?>", "LOCATION");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_ATTACHMENT",true);?>", "ATTACH");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CONTACT_LABEL",true);?>", "CONTACT_LABEL");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CONTACT",true);?>", "CONTACT");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_EXTRAINFO",true);?>", "EXTRAINFO");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CATEGORY",true);?>", "CATEGORY");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CATEGORY_ID",true);?>", "CATID");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_ALL_CATEGORIES",true);?>", "ALLCATEGORIES");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CATEGORY_LINK",true);?>", "CATEGORYLNK");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CATEGORY_IMAGE",true);?>", "CATEGORYIMG");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CATEGORY_IMAGES",true);?>", "CATEGORYIMGS");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_FGCOLOUR",true);?>", "FGCOLOUR");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_COLOUR",true);?>", "COLOUR");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_OPAQUE_COLOUR",true);?>", "RGBA");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CALENDAR",true);?>", "CALENDAR");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CREATIONDATE",true);?>", "CREATED");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_LINK_RAW", true);?>", "LINK");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_LINKSTART",true);?>", "LINKSTART");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_LINKEND",true);?>", "LINKEND");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_URL",true);?>", "URL");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_EVENT_PRIORITY",true);?>", "PRIORITY");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_EVENT_STARTED",true);?>", "JEVSTARTED");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_EVENT_ENDED",true);?>", "JEVENDED");
defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_AGE",true);?>", "JEVAGE");

<?php
// get list of enabled plugins
$jevplugins = PluginHelper::getPlugin("jevents");
foreach ($jevplugins as $jevplugin){
	if (PluginHelper::importPlugin("jevents", $jevplugin->name)){
		$classname = "plgJevents".ucfirst($jevplugin->name);
		if (is_callable(array($classname,"fieldNameArray"))){
			$lang = Factory::getLanguage();
			$lang->load("plg_jevents_".$jevplugin->name,JPATH_ADMINISTRATOR);
			$fieldNameArray = call_user_func(array($classname,"fieldNameArray"),'list');
			if (!isset($fieldNameArray['labels'])) continue;
			?>
			optgroup = defaultsEditorPlugin.optgroup('#jevdefaults' , '<?php echo $fieldNameArray["group"];?>');
			<?php
			for ($i=0;$i<count($fieldNameArray['labels']);$i++) {
				if ($fieldNameArray['labels'][$i]=="" || $fieldNameArray['labels'][$i]==" Label")  continue;
				?>
				defaultsEditorPlugin.node(optgroup , "<?php echo str_replace(":"," ",$fieldNameArray['labels'][$i]);?>", "<?php echo $fieldNameArray['values'][$i];?>");
				<?php
			}
		}
	}
}
?>
    });
</script>
