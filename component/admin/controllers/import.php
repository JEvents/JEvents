<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: icals.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd,2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

class ImportController extends Joomla\CMS\MVC\Controller\FormController
{

	var $_debug = false;
	var $queryModel = null;
	var $dataModel = null;

	/**
	 * Controler for the Ical Functions
	 *
	 * @param array        configuration
	 */
	function __construct($config = array())
	{

		parent::__construct($config);
		$this->registerTask('run', 'runImport');
		$this->registerDefaultTask("import");

		$cfg          = JEVConfig::getInstance();
		$this->_debug = $cfg->get('jev_debug', 0);

		$this->dataModel  = new JEventsDataModel("JEventsAdminDBModel");
		$this->queryModel = new JEventsDBModel($this->dataModel);

	}

	public function import() {

		$document   = Factory::getDocument();
		$document->setTitle(Text::_('COM_JEVENTS_IMPORT_TITLE'));

		$this->view = $this->getView("import", "html");
		// Set the layout
		$this->view->setLayout('import');
		$this->view->display();
	}


}
