<?php

/**
 * JEvents Locations Component for Joomla 1.5.x
 *
 * @version     $Id: jevinfo.php 1331 2010-10-19 12:35:49Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\FormHelper;
use Joomla\String\StringHelper;
use Joomla\CMS\HTML\HTMLHelper;

FormHelper::loadFieldClass('spacer');

// Must load admin language files
$lang = Factory::getLanguage();
$lang->load("com_jevents", JPATH_ADMINISTRATOR);

/**
 * JEVMenu Field class for the JEvents Component
 *
 * @package        JEvents.fields
 * @subpackage     com_banners
 * @since          1.6
 */
class JFormFieldJEVInfo extends JFormFieldSpacer
{

	/**
	 * The form field type.s
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected
		$type = 'JEVInfo';

	/**
	 * Method to get the field options.
	 *
	 * @return    array    The field option objects.
	 * @since    1.6
	 */
	public
	function getInput()
	{

        $app    = Factory::getApplication();
        $input = $app->input;

        // Trap to stop the config from being editing from the categories page
        // Updated to redirect to the correct edit page, Joomla 3.x Config actually loads this page when configuration components.
        // Only do the redirect in the backend since in the frontend module editing uses com_config (go figure!!!)
        if ($input->getString("option") == "com_config" && $app->isClient('administrator'))
        {
            $redirect_url = "index.php?option=com_jevents&task=params.edit"; // get rid of any ampersands
            $app->redirect($redirect_url); //redirect
            exit();
        }

        // Must load admin language files
		$lang = Factory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		$node         = $this->element;
		$value        = $this->value;
		$name         = $this->name;
		$control_name = $this->type;

		$help = $node['help'];

		if ((!is_null($help)) && (version_compare(JVERSION, '1.6.0', ">=")))
		{
			if (is_object($help))
				$help = (string) $help;
			$help = ((isset($help)) && (StringHelper::strlen($help) <= 0)) ? null : $help;
		}
		if (!is_null($help))
		{
			$parts = explode(",", $value);
			$helps = explode(",", $help);
			foreach ($parts as $key => $valuepart)
			{
				$help = $helps[$key];
				list($helpfile, $varname, $part) = explode("::", $help);
				$lang    = Factory::getLanguage();
				$langtag = $lang->getTag();
				if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/help/' . $langtag . '/' . $helpfile))
				{
					$jeventHelpPopup = JPATH_COMPONENT_ADMINISTRATOR . '/help/' . $langtag . '/' . $helpfile;
				}
				else
				{
					$jeventHelpPopup = JPATH_COMPONENT_ADMINISTRATOR . '/help/en-GB/' . $helpfile;
				}
				if (!file_exists($jeventHelpPopup))
				{
					return "";
				}
				include($jeventHelpPopup);
				$help        = $this->help($$varname, $part);
				$parts[$key] = Text::_($valuepart) . $help;
			}
			$value = implode(", ", $parts);
		}

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		return "<strong style='color:#993300' id='" . $this->id . "' >" . Text::_($value) . "</strong>";

	}

	/**
	 * Creates a help icon with link to help information as onclick event
	 *
	 * if $help is url, link opens a new window with target url
	 * if $help is text, text is shown in a sticky overlib window with close button
	 *
	 * @static
	 *
	 * @param    $help        string    help text (html text or url to target)
	 * @param    $caption     string    caption of overlib window
	 *
	 * @return                string    html sting
	 */
	public
	function help($help = 'help text', $caption = '')
	{

		$compath = Uri::root() . 'administrator/components/' . JEV_COM_COMPONENT;
		$imgpath = $compath . '/assets/images';

		if (empty($caption))
			$caption = '&nbsp;';

		static $counthelps = 0;
		$counthelps++;

		if (StringHelper::substr($help, 0, 7) == 'http://' || StringHelper::substr($help, 0, 8) == 'https://')
		{
			//help text is url, open new window
			$onclick_cmd = "window.open(\"$help\", \"help\", \"height=700,width=800,resizable=yes,scrollbars\");return false";
		}
		else
		{
			// help text is plain text with html tags
			// prepare text as overlib parameter
			// escape ", replace new line by space
			//$help = htmlspecialchars($help, ENT_QUOTES);
			//$help = str_replace('&quot;', '\&quot;', $help);
			$help = addslashes(str_replace("\n", " ", $help));

			$onclick_cmd = "SqueezeBox.initialize({});SqueezeBox.setOptions(SqueezeBox.presets,{'handler': 'iframe','size': {'x': 400, 'y': 500},'closeWithOverlay': 0, 'onOpen' : function(){SqueezeBox.overlay['removeEvent']('click', SqueezeBox.bound.close)}});SqueezeBox.setContent('clone', $('helpdiv" . $counthelps . "'));";
		}

		// RSH 10/11/10 - Added float:none for 1.6 compatiblity - The default template was floating images to the left
		$str = '<img border="0" class="jev_help" alt="' . Text::_('JEV_HELP') . '"'
			. ' title="' . Text::_('JEV_HELP') . '"'
			. ' src="' . $imgpath . '/help_ques_inact.gif"'
			//. ' onmouseover="this.src="' . $imgpath . '/help_ques.gif'.'" '
			//. ' onmouseout="this.src="' . $imgpath . '/help_ques_inact.gif'.'" '
			. ' onclick="' . $onclick_cmd . '" /><div class="jev_none"><div id="helpdiv' . $counthelps . '" >' . $help . '</div></div>';

		return $str;

	}

}
