<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit_icalevent.edit_page.php 2091 2011-05-16 09:12:40Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

// Disable for now

?>
<label><?php echo Text::_("JEV_PLUGIN_INSTRUCTIONS",true);?></label>
<select id="jevdefaults" class="gsl-select" onchange="defaultsEditorPlugin.insert('value','jevdefaults' )" ></select>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        defaultsEditorPlugin.node('#jevdefaults',"<?php echo Text::_("JEV_PLUGIN_SELECT",true);?>","");
        // built in group
        var optgroup = defaultsEditorPlugin.optgroup('#jevdefaults' , "<?php echo Text::_("JEV_CORE_DATA",true);?>");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_TAB_START",true);?>", "TABSTART#name");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_END_TABS",true);?>", "TABSEND");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_MESSAGE",true);?>", "MESSAGE");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_TITLE",true);?>", "TITLE");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_TITLE_LABEL",true);?>", "TITLE_LBL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CREATOR",true);?>", "CREATOR");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CREATOR_LABEL",true);?>", "CREATOR_LBL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CATEGORY",true);?>", "CATEGORY");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CATEGORY_LABEL",true);?>", "CATEGORY_LBL");
        //defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_PRIMARY_CATEGORY",true);?>", "PRIMARYCATEGORY");
        //defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_PRIMARY_CATEGORY_LABEL",true);?>", "PRIMARYCATEGORY_LBL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_ICAL",true);?>", "ICAL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_ICAL_LABEL",true);?>", "ICAL_LBL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_ACCESS",true);?>", "ACCESS");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_ACCESS_LABEL",true);?>", "ACCESS_LBL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_STATE",true);?>", "STATE");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_STATE_LABEL",true);?>", "STATE_LBL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_DESCRIPTION",true);?>", "DESCRIPTION");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_DESCRIPTION_LABEL",true);?>", "DESCRIPTION_LBL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_LOCATION",true);?>", "LOCN");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_LOCATION_LABEL",true);?>", "LOCN_LBL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_GEOLON",true);?>", "GEOLON");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_GEOLON_LABEL",true);?>", "GEOLON_LBL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_GEOLAT",true);?>", "GEOLAT");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_GEOLAT_LABEL",true);?>", "GEOLAT_LBL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CONTACT",true);?>", "CONTACT");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CONTACT_LABEL",true);?>", "CONTACT_LBL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_EXTRAINFO",true);?>", "EXTRA");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_EXTRAINFO_LABEL",true);?>", "EXTRA_LBL");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CUSTOMFIELDS",true);?>", "CUSTOMFIELDS");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_FIELD_CALTAB",true);?>", "CALTAB");
		<?php
		$jevparams = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($jevparams->get("showpriority", 0)){
		?>
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_EVENT_PRIORITY",true);?>", "PRIORITY");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_EVENT_PRIORITY_LABEL",true);?>", "PRIORITY_LBL");
		<?php
		}
		if ($jevparams->get("showtimezone", 0)){
		?>
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_EVENT_TIMEZONE",true);?>", "TZID");
        defaultsEditorPlugin.node(optgroup , "<?php echo Text::_("JEV_EVENT_TIMEZONE_LABEL",true);?>", "TZID_LBL");
		<?php
		}
		?>

        window.Joomla.submitbutton = function (pressbutton){

            if(pressbutton == "defaults.apply" || pressbutton == "defaults.save")
            {
				<?php

				$editor =  Editor::getInstance('none');

				?>

				<?php
				$requiredfields = "'CALTAB','TITLE','CATEGORY','ICAL','MESSAGE'";
				if(!empty($this->requiredfields))
				{
					$requiredfields .= ",".$this->requiredfields;
				}
				?>
                var requiredFields = [<?php echo $requiredfields; ?>];
				<?php
				if (version_compare(JVERSION, '4.0', 'lt'))
				{
				?>
                var defaultsLayout = <?php echo $editor->getContent('value'); ?>;
				<?php
				}
				else
				{
				?>
                var defaultsLayout = document.getElementsByName('value')[0].value;
				<?php
				}
				?>
                if(defaultsLayout == '')
                {
                    if( !confirm ('<?php echo Text::_("JEV_LAYOUT_DEFAULTS_EMPTY_ALERT",true);?>'))
                    {
                        return;
                    }
                    else
                    {
                        Joomla.submitform(pressbutton);
                    }
                }
                else
                {
                    var missingFields = [];
                    //We check tabs closing if necessary:
                    var tabStart =  RegExp("\{\{.*:TABSTART#.*\}\}");
                    var tabsEnd =  RegExp("\{\{.*:TABSEND.*\}\}");
                    if (tabStart.test(defaultsLayout) && !tabStart.test(defaultsLayout))
                    {
                        missingFields.push('TABSEND');
                    }
                    //  Native Javascript array
                    requiredFields.forEach(function(requiredField, index){
                        var requiredFieldRE = RegExp("\{\{.*:"+requiredField+"\}\}");
                        if(!requiredFieldRE.test(defaultsLayout))
                        {
                            var options = jQuery('#jevdefaults option');
                            options.each (function(idx, opt){
                                if ((opt.value+"}}").indexOf(":"+requiredField+"}}")>=0){
                                    missingFields.push(opt.value);
                                }
                            })

                        }
                    });
                    if (missingFields.length >0){
                        var message = '<?php echo Text::_("JEV_LAYOUT_MISSING_FIELD",true);?>'+'\n';
                        // native array!
                        missingFields.forEach (function (msg, index){
                            message +=  msg +'\n';
                        });
                        alert(message);
                    }
                    else
                    {
                        Joomla.submitform(pressbutton);
                    }
                }

            }
            else
            {
                Joomla.submitform(pressbutton);
            }
        }

		<?php
		// get list of enabled plugins
		$jevplugins = PluginHelper::getPlugin("jevents");
	    foreach ($jevplugins as $jevplugin){
		    if (PluginHelper::importPlugin("jevents", $jevplugin->name)){
			    // At present only some plugins support secondary tabs and special input formats
			    if (!in_array($jevplugin->name, array("jevcustomfields", "jevrsvppro", "jevpeople", "agendaminutes", "jevfiles", "jevcck", "jevusers", "jevtags", "jevmetatags", "jevanonuser", "jevrsvp", "jevgroupevent", "jevtimelimit")))
			    {
				    continue;
			    }
			    $classname = "plgJevents" . ucfirst($jevplugin->name);
			    if (is_callable(array($classname, "fieldNameArray"))){
				    $lang = Factory::getLanguage();
				    $lang->load("plg_jevents_" . $jevplugin->name, JPATH_ADMINISTRATOR);
				    $fieldNameArray = call_user_func(array($classname, "fieldNameArray"), "edit");
				    if (!isset($fieldNameArray['labels'])) continue;
				    ?>
			        optgroup = defaultsEditorPlugin.optgroup('#jevdefaults', '<?php echo $fieldNameArray["group"];?>');
				    <?php
				    for ($i = 0;$i < count($fieldNameArray['labels']);$i++) {
					    if ($fieldNameArray['labels'][$i] == "" || $fieldNameArray['labels'][$i] == " Label") continue;
					    ?>
				        defaultsEditorPlugin.node(optgroup, "<?php echo str_replace(":", " ", $fieldNameArray['labels'][$i]);?>", "<?php echo $fieldNameArray['values'][$i];?>");
					    <?php
				    }
			    }
		    }
	    }
	    ?>
    });

</script>
