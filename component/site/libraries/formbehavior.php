<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Utility class for form related behaviors
 *
 * @package     Joomla.Libraries
 * @subpackage  HTML
 * @since       3.0
 */
abstract class JevHtmlFormbehavior
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Method to load the AJAX Chosen library
	 *
	 * If debugging mode is on an uncompressed version of AJAX Chosen is included for easier debugging.
	 *
	 * @param   JevRegistry $options Options in a JevRegistry object
	 * @param   mixed     $debug   Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function ajaxchosen(JevRegistry $options, $debug = null)
	{

		// Retrieve options/defaults
		$selector       = $options->get('selector', '.tagfield');
		$type           = $options->get('type', 'GET');
		$url            = $options->get('url', null);
		$dataType       = $options->get('dataType', 'json');
		$jsonTermKey    = $options->get('jsonTermKey', 'term');
		$afterTypeDelay = $options->get('afterTypeDelay', '500');
		$minTermLength  = $options->get('minTermLength', '3');

		Text::script('JGLOBAL_KEEP_TYPING');
		Text::script('JGLOBAL_LOOKING_FOR');

		// Ajax URL is mandatory
		if (!empty($url))
		{
			if (isset(static::$loaded[__METHOD__][$selector]))
			{
				return;
			}

			// Include jQuery
			HTMLHelper::_('jquery.framework');

			// Requires chosen to work
			static::chosen($selector, $debug);

			HTMLHelper::_('script', 'jui/ajax-chosen.min.js', false, true, false, false, $debug);
			Factory::getDocument()->addScriptDeclaration("
				(function($){
					$(document).ready(function () {
						$('" . $selector . "').ajaxChosen({
							type: '" . $type . "',
							url: '" . $url . "',
							dataType: '" . $dataType . "',
							jsonTermKey: '" . $jsonTermKey . "',
							afterTypeDelay: '" . $afterTypeDelay . "',
							minTermLength: '" . $minTermLength . "'
						}, function (data) {
							var results = [];

							$.each(data, function (i, val) {
								results.push({ value: val.value, text: val.text });
							});

							return results;
						});
					});
				})(jQuery);
				"
			);

			static::$loaded[__METHOD__][$selector] = true;
		}

		return;
	}

	/**
	 * Method to load the Chosen JavaScript framework and supporting CSS into the document head
	 *
	 * If debugging mode is on an uncompressed version of Chosen is included for easier debugging.
	 *
	 * @param   string $selector Class for Chosen elements.
	 * @param   mixed  $debug    Is debugging mode on? [optional]
	 * @param   array  $options  the possible Chosen options as name => value [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function chosen($selector = '.advancedSelect', $debug = null, $options = array())
	{

		if (isset(static::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		// Include jQuery
		//HTMLHelper::_('jquery.framework');

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = Factory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}

		// Default settings
		$options['disable_search_threshold']  = isset($options['disable_search_threshold']) ? $options['disable_search_threshold'] : 10;
		$options['allow_single_deselect']     = isset($options['allow_single_deselect']) ? $options['allow_single_deselect'] : true;
		$options['placeholder_text_multiple'] = isset($options['placeholder_text_multiple']) ? $options['placeholder_text_multiple'] : Text::_('JGLOBAL_SELECT_SOME_OPTIONS');
		$options['placeholder_text_single']   = isset($options['placeholder_text_single']) ? $options['placeholder_text_single'] : Text::_('JGLOBAL_SELECT_AN_OPTION');
		$options['no_results_text']           = isset($options['no_results_text']) ? $options['no_results_text'] : Text::_('JGLOBAL_SELECT_NO_RESULTS_MATCH');

		// Options array to json options string
		$options_str = json_encode($options, ($debug && defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : false));

		JEVHelper::script("components/com_jevents/assets/js/chosen.jquery.js");
		HTMLHelper::stylesheet("components/com_jevents/assets/css/chosen.css");

		//HTMLHelper::_('script', 'jui/chosen.jquery.min.js', false, true, false, false, $debug);
		//HTMLHelper::_('stylesheet', 'jui/chosen.css', false, true);
		Factory::getDocument()->addScriptDeclaration("
				jQuery(document).ready(function (){
					jQuery('" . $selector . "').chosen(" . $options_str . ");
				});
			"
		);

		static::$loaded[__METHOD__][$selector] = true;

		return;
	}
}
