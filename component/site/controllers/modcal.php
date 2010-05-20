<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: modcal.php 1572 2009-09-23 08:30:47Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

class ModCalController extends JController   {

	var $modid = null;

	/* parameters form module or component */
	var $displayLastMonth		= null;
	var $disp_lastMonthDays		= null;
	var $disp_lastMonth			= null;

	var $displayNextMonth		= null;
	var $disp_nextMonthDays		= null;
	var $disp_nextMonth			= null;

	var $linkCloaking			= null;

	/* component only parameter */
	var $com_starday			= null;

	/* module only parameters */
	var $inc_ec_css				= null;
	var $minical_showlink		= null;
	var $minical_prevyear		= null;
	var $minical_prevmonth		= null;
	var $minical_actmonth		= null;
	var $minical_actyear		= null;
	var $minical_nextmonth		= null;
	var $minical_nextyear		= null;

	/* class variables */
	var $catidsOut				= null;
	var $modcatids				= null;
	var $catidList				= "";
	var $aid					= null;
	var $lang					= null;
	var $myItemid				= 0;
	var $cat 					= "";

	/* modules parameter object */
	var $modparams				= null;

	// data model for module
	var $datamodel				= null;

	function __construct($config = array())
	{
		if (!isset($config['base_path'])){
			$config['base_path']=JEV_PATH;
		}
		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask( 'calendar' );

		$cfg = & JEVConfig::getInstance();
		$theme = ucfirst(JEV_CommonFunctions::getJEventsViewName());
		JLoader::register('JEvents'.ucfirst($theme).'View',JEV_VIEWS."/".$theme."/abstract/abstract.php");

		include_once(JEV_LIBS."/modfunctions.php");
	}

	function ajax() {
		$modid = intval((JRequest::getVar('modid', 0)));
		if ($modid<=0){
			echo "<script>alert('bad mod id');</script>";
			return;
		}

		// load language constants
		JEVHelper::loadLanguage('modcal');

		list($year,$month,$day) = JEVHelper::getYMD();

		$user =& JFactory::getUser();
		$query = "SELECT id, params"
		. "\n FROM #__modules AS m"
		. "\n WHERE m.published = 1"
		. "\n AND m.id = ". $modid
		. "\n AND m.access <= ". (int) $user->aid
		. "\n AND m.client_id != 1";
		$db	=& JFactory::getDBO();
		$db->setQuery( $query );
		$modules = $db->loadObjectList();
		if (count($modules)<=0){
			if (!$modid<=0){
				echo "<script>alert('bad mod id');</script>";
				return;
			}
		}
		$params = new JParameter( $modules[0]->params );

		$cfg = & JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		require(JModuleHelper::getLayoutPath('mod_jevents_cal',$theme.DS."calendar"));
		
		$viewclass = ucfirst($theme)."ModCalView";
		$modview = new $viewclass($params, $modid);
			
		?>
		<script language="javascript">
		function doit(){
			var sillydiv=document.getElementById('silly');
			parent.navLoaded(sillydiv,<?php echo $modid;?>);
		}
		window.onload=doit;
		</script>
		<?php
		echo "<div id='silly'>";
		echo $modview->getAjaxCal($modid,$month,$year);
		echo "</div>";
	}



	function getViewName(){
		$cfg = & JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		return $theme;
	}

	/**
		* Pseudo Constructor
		*
		*/		
	function setup(&$params, $modid) {

		$this->modid = $modid;

		global $mainframe;
		$user =& JFactory::getUser();

		$cfg = & JEVConfig::getInstance();
		$db	=& JFactory::getDBO();

		$this->datamodel =new JEventsDataModel();

		// component config object
		$jevents_config		= & JEVConfig::getInstance();

		$this->modparams	= & $params;
		$this->aid			= $user->aid;
		$tmplang			=& JFactory::getLanguage();

		// get params exclusive to module
		$this->inc_ec_css			= $this->modparams->get('inc_ec_css', 0);
		$this->minical_showlink		= $this->modparams->get('minical_showlink', 1);;
		$this->minical_prevyear		= $this->modparams->get('minical_prevyear', 1);;
		$this->minical_prevmonth	= $this->modparams->get('minical_prevmonth', 1);;
		$this->minical_actmonth		= $this->modparams->get('minical_actmonth', 1);;
		$this->minical_actmonth		= $this->modparams->get('minical_actmonth', 1);;
		$this->minical_actyear		= $this->modparams->get('minical_actyear', 1);;
		$this->minical_nextmonth	= $this->modparams->get('minical_nextmonth', 1);;
		$this->minical_nextyear		= $this->modparams->get('minical_nextyear', 1);;

		// get params exclusive to component
		$this->com_starday	= intval($jevents_config->get('com_starday',0));

		// make config object (module or component) current
		if (intval($this->modparams->get('modcal_useLocalParam',  0)) == 1) {
			$myparam	= & $this->modparams;
		} else {
			$myparam	= & $jevents_config;
		}

		// get com_event config parameters for this module
		$this->displayLastMonth		= $myparam->get('modcal_DispLastMonth', 'NO');
		$this->disp_lastMonthDays	= $myparam->get('modcal_DispLastMonthDays', 0);
		$this->linkCloaking			= $myparam->get('modcal_LinkCloaking', 0);

		$t_datenow = JEVHelper::getNow();
		$this->timeWithOffset = $t_datenow->toUnix(true);

		switch($this->displayLastMonth) {
			case 'YES_stop':
				$this->disp_lastMonth = 1;
				break;
			case 'YES_stop_events':
				$this->disp_lastMonth = 2;
				break;
			case 'ALWAYS':
				$this->disp_lastMonthDays = 0;
				$this->disp_lastMonth = 1;
				break;
			case 'ALWAYS_events':
				$this->disp_lastMonthDays = 0;
				$this->disp_lastMonth = 2;
				break;
			case 'NO':
			default:
				$this->disp_lastMonthDays = 0;
				$this->disp_lastMonth = 0;
				break;
		}

		$this->displayNextMonth		= $myparam->get('modcal_DispNextMonth', 'NO');
		$this->disp_nextMonthDays	= $myparam->get('modcal_DispNextMonthDays', 0);

		switch($this->displayNextMonth) {
			case 'YES_stop':
				$this->disp_nextMonth = 1;
				break;
			case 'YES_stop_events':
				$this->disp_nextMonth = 2;
				break;
			case 'ALWAYS':
				$this->disp_nextMonthDays = 0;
				$this->disp_nextMonth = 1;
				break;
			case 'ALWAYS_events':
				$this->disp_nextMonthDays = 0;
				$this->disp_nextMonth = 2;
				break;
			case 'NO':
			default:
				$this->disp_nextMonthDays = 0;
				$this->disp_nextMonth = 0;
				break;
		}

		// find appropriate Itemid and setup catids for datamodel
		$this->myItemid = $this->datamodel->setupModuleCatids($this->modparams);

		$this->cat = $this->datamodel->getCatidsOutLink(true);

		$this->linkpref = 'index.php?option='.JEV_COM_COMPONENT.'&Itemid='.$this->myItemid.$this->cat.'&task=';

	}

}

