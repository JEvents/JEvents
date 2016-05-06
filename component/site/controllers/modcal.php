<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: modcal.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

class ModCalController extends JControllerLegacy   {

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

		$cfg = JEVConfig::getInstance();
		$theme = ucfirst(JEV_CommonFunctions::getJEventsViewName());
		JLoader::register('JEvents'.ucfirst($theme).'View',JEV_VIEWS."/".$theme."/abstract/abstract.php");

		include_once(JEV_LIBS."/modfunctions.php");
		if (!isset($this->_basePath)){
			$this->_basePath = $this->basePath;
			$this->_task = $this->task;
		}
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

		$user = JFactory::getUser();
		$query = "SELECT id, params"
		. "\n FROM #__modules AS m"
		. "\n WHERE m.published = 1"
		. "\n AND m.id = ". $modid
		. "\n AND m.access IN (" .  JEVHelper::getAid($user, 'string') . ")"
		. "\n AND m.client_id != 1";
		$db	= JFactory::getDBO();
		$db->setQuery( $query );
		$modules = $db->loadObjectList();
		if (count($modules)<=0){
			if (!$modid<=0){
				echo "<script>alert('bad mod id');</script>";
				return;
			}
		}
		$params = new JRegistry( $modules[0]->params );

		$cfg = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		$comptheme = $params->get("com_calViewName","global");
		$theme = ($comptheme=="global")?$theme : $comptheme;
		$modtheme = $params->get("com_calViewName", $theme);
		if ($modtheme=="" || $modtheme=="global" ){
			$modtheme=$theme;
		}
		$theme=$modtheme;

		//require(JModuleHelper::getLayoutPath('mod_jevents_cal',$theme.'/'."calendar"));
		require_once (JPATH_SITE.'/modules/mod_jevents_cal/helper.php');
		$jevhelper = new modJeventsCalHelper();
		$viewclass = $jevhelper->getViewClass($theme, 'mod_jevents_cal',$theme.'/'."calendar", $params);
		
		$modview = new $viewclass($params, $modid);
		$modview->jevlayout = $theme;
		$content = $modview->getAjaxCal($modid,$month,$year);
		$content = str_replace("<script style='text/javascript'>xyz=1;", "XYZ", $content);
		$content = str_replace("zyx=1;</script>", "ZYX", $content);
                // ungreedy match 
		preg_match("/XYZ(.*)ZYX/sU", $content, $match);
		$script = "";
		if (isset($match[1])){
			$script = $match[1];
			$content = str_replace($match[0],"", $content);
		}
		$json = array("data" => $content, "modid"=>$modid, "script"=>$script);
		ob_end_clean();
		ob_end_flush();
		if (JRequest::getCmd("callback", 0)){
			echo JRequest::getCmd("callback", 0)."(". json_encode($json),");";
			exit();
		}
		else if (JRequest::getInt("json")==1){
			echo json_encode($json);
			exit();
		}
		else {
		?>
		<script type="text/javascript">
		var doitdone = false;
		function doit(){
			if (doitdone) return;
			doitdone = true;
			var sillydiv=document.getElementById('silly');
			parent.navLoaded(sillydiv,<?php echo $modid;?>);
		}
		window.onload=doit;
		</script>
		<?php
		echo "<div id='silly'>";
		echo $modview->getAjaxCal($modid,$month,$year);
		echo "</div>";
		?>
		<script type="text/javascript">
		doit();
		</script>
		<?php
		}
	}



	function getViewName(){
		$cfg = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		return $theme;
	}

	/**
		* Pseudo Constructor
		*
		*/		
	function setup(&$params, $modid) {

		$this->modid = $modid;

		
		$user = JFactory::getUser();

		$cfg = JEVConfig::getInstance();
		$db	= JFactory::getDBO();

		$this->datamodel =new JEventsDataModel();

		// component config object
		$jevents_config		= JEVConfig::getInstance();

		$this->modparams	= & $params;
		$this->aid			= JEVHelper::getAid($user, 'string');   // RSH modified getAid to handle different return types 10/26/10
		$tmplang			= JFactory::getLanguage();

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

