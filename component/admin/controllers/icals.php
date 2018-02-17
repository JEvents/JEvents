<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: icals.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd,2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controllerform');

use Joomla\Utilities\ArrayHelper;
use Joomla\String\StringHelper;

class AdminIcalsController extends JControllerForm {

	var $_debug = false;
	var $queryModel = null;
	var $dataModel = null;

	/**
	 * Controler for the Ical Functions
	 * @param array		configuration
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask( 'list',  'overview' );
		$this->registerTask( 'new',  'newical' );
		$this->registerTask( 'reload',  'save' );
		$this->registerDefaultTask("overview");

		$cfg = JEVConfig::getInstance();
		$this->_debug = $cfg->get('jev_debug', 0);

		$this->dataModel = new JEventsDataModel("JEventsAdminDBModel");
		$this->queryModel =new JEventsDBModel($this->dataModel);

	}

	/**
	 * List Icals
	 *
	 */
	function overview( )
	{
		// get the view
		$this->view = $this->getView("icals","html");

		$this->_checkValidCategories();

		$option = JEV_COM_COMPONENT;
		$db	= JFactory::getDbo();

		
		$catid		= intval( JFactory::getApplication()->getUserStateFromRequest( "catid{$option}", 'catid', 0 ));
		$limit		= intval( JFactory::getApplication()->getUserStateFromRequest( "viewlistlimit", 'limit', JFactory::getApplication()->getCfg('list_limit',10) ));
		$limitstart = intval( JFactory::getApplication()->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ));
		$search		= JFactory::getApplication()->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search		= $db->escape( trim( strtolower( $search ) ) );
		$where		= array();
                
                // Trap cancelled edit and reset category ID.
                $icsid = intval(JRequest::getVar('icsid',-1));  
                if ($icsid>-1){
                    $catid=0;
                }
		if( $search ){
			$where[] = "LOWER(icsf.label) LIKE '%$search%'";
		}
		if ($catid>0){
			$where[] ="catid = $catid";
		}
		// get the total number of records
		$query = "SELECT count(*)"
		. "\n FROM #__jevents_icsfile AS icsf"
		. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
		;
		$db->setQuery( $query);
		$total = $db->loadResult();
		echo $db->getErrorMsg();

		if( $limitstart > $total ) {
			$limitstart = 0;
		}

		
		$query = "SELECT icsf.*, a.title as _groupname"
		. "\n FROM #__jevents_icsfile as icsf "
		. "\n LEFT JOIN #__viewlevels AS a ON a.id = icsf.access"
		. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
		;
		
		$query .= "\n ORDER BY icsf.isdefault DESC, icsf.label ASC";
		if ($limit>0){
			$query .= "\n LIMIT $limitstart, $limit";
		}

		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		$catData = JEV_CommonFunctions::getCategoryData();

		for ($s=0;$s<count($rows);$s++) {
			$row =& $rows[$s];
			if (array_key_exists($row->catid,$catData)){
				$row->category = $catData[$row->catid]->name;
			}
			else {
				$row->category = "?";
			}
		}

		if( $this->_debug ){
			echo '[DEBUG]<br />';
			echo 'query:';
			echo '<pre>';
			echo $query;
			echo '-----------<br />';
			echo 'option "' . $option . '"<br />';
			echo '</pre>';
			//die( 'userbreak - mic ' );
		}

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		// get list of categories
		$attribs = 'class="inputbox" size="1" onchange="document.adminForm.submit();"';
		$clist = JEventsHTML::buildCategorySelect( $catid, $attribs, null, true,false, 0, 'catid');

		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit  );


		// Set the layout
		$this->view->setLayout('overview');

		$this->view->assign('option',JEV_COM_COMPONENT);
		$this->view->assign('rows',$rows);
		$this->view->assign('clist',$clist);
		$this->view->assign('search',$search);
		$this->view->assign('pageNav',$pageNav);

		$this->view->display();
	}

	function edit ($key = null, $urlVar = null) {
		$user = JFactory::getUser();
		if (!JEVHelper::isAdminUser()){
			$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			$this->redirect();
			return;
		}

		// get the view
		$this->view = $this->getView("icals","html");

		$cid	= JRequest::getVar(	'cid',	array(0) );
		$cid = ArrayHelper::toInteger($cid);
		if (is_array($cid) && count($cid)>0) $editItem=$cid[0];
		else $editItem=0;

		$item =new stdClass();
		if ($editItem!=null){
			$db	= JFactory::getDbo();
			$query = "SELECT * FROM #__jevents_icsfile as ics where ics.ics_id=$editItem";

			$db->setQuery( $query );
			$item = null;
			$item = $db->loadObject();
		}


		// Set the layout
		$this->view->setLayout('edit');

		// for Admin interface only
		
		$this->view->assign('with_unpublished_cat',JFactory::getApplication()->isAdmin());

		$this->view->assign('editItem',$item);
		$this->view->assign('option',JEV_COM_COMPONENT);

		$this->view->display();

	}

    function reloadall(){

		@set_time_limit(1800);

		if (JFactory::getApplication()->isAdmin()){
			$redirect_task="icals.list";
		}
		else
		{

			$redirect_task="day.listevents";
		}

        $query = "SELECT icsf.* FROM #__jevents_icsfile as icsf";
		$db	= JFactory::getDbo();
		$db->setQuery($query);
		$allICS = $db->loadObjectList();

        foreach ($allICS as $currentICS){
	        //only update cals from url
	        if ($currentICS->icaltype=='0' && $currentICS->autorefresh==1){
		        JRequest::setVar('icsid',$currentICS->ics_id);
		        $this->save();
	        }
        }

	    $user = JFactory::getUser();
	    $guest = (int) $user->get('guest');

	    $link = "index.php?option=".JEV_COM_COMPONENT."&task=$redirect_task";
	    $message = JText::_( 'ICS_ALL_FILES_IMPORTED' );

	    if ($guest === 1) {
		    $this->setRedirect( $link);
	    } else {
		    $this->setRedirect( $link, $message);
	    }

		$this->redirect();
    }

	function save($key = null, $urlVar = null){

		// Check for request forgeries
		if (JRequest::getCmd("task") != "icals.reload" && JRequest::getCmd("task") != "icals.reloadall"){
			JSession::checkToken() or jexit( 'Invalid Token' );
		}

		$user = JFactory::getUser();
		$guest = (int) $user->get('guest');

		$authorised = false;
		
		if (JFactory::getApplication()->isAdmin()){
			$redirect_task="icals.list";
		}
		else {
			$redirect_task="day.listevents";
		}

		// clean this up later - this is a quick fix for frontend reloading
		$autorefresh = 0;
		$icsid = intval(JRequest::getVar('icsid',0));
		if ($icsid>0){
			$query = "SELECT icsf.* FROM #__jevents_icsfile as icsf WHERE ics_id=$icsid";
			$db	= JFactory::getDbo();
			$db->setQuery($query);
			$currentICS = $db->loadObjectList();
			if (count($currentICS)>0){
				$currentICS= $currentICS[0];
				if ($currentICS->autorefresh){
					$authorised = true;
					$autorefresh=1;
				}
			}
		}

		if (!($authorised || JEVHelper::isAdminUser($user))) {
			$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=$redirect_task", "Not Authorised - must be super admin" );
			$this->redirect();
			return;
		}
		$cid	= JRequest::getVar(	'cid',	array(0) );
		$cid = ArrayHelper::toInteger($cid);
		if (is_array($cid) && count($cid)>0) {
			$cid=$cid[0];
		} else {
			$cid=0;
		}

		$db	= JFactory::getDbo();

		// include ical files
		

		if ($icsid>0 || $cid!=0){
			$icsid = ($icsid>0)?$icsid:$cid;
			$query = "SELECT icsf.* FROM #__jevents_icsfile as icsf WHERE ics_id=$icsid";
			$db->setQuery($query);
			$currentICS = $db->loadObjectList();
			if (count($currentICS)>0){
				$currentICS= $currentICS[0];
				if ($currentICS->autorefresh){
					$authorised = true;
					$autorefresh=1;
				}
				
			}
			else {
				$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=$redirect_task", "Invalid Ical Details");
				$this->redirect();
			}

			$catid = JRequest::getInt('catid',$currentICS->catid);
			if ($catid<=0 && $currentICS->catid>0){
				$catid = intval($currentICS->catid);
			}
			$access = intval(JRequest::getVar('access',$currentICS->access));
			if ($access<0 && $currentICS->access>=0){
				$access = intval($currentICS->access);
			}
			$icsLabel = JRequest::getVar('icsLabel',$currentICS->label );
			if (($icsLabel=="" || JRequest::getCmd("task") == "icals.reload") && JString::strlen($currentICS->label)>=0){
				$icsLabel = $currentICS->label;
			}
			$isdefault = JRequest::getInt('isdefault',$currentICS->isdefault);
			$overlaps = JRequest::getInt('overlaps',$currentICS->overlaps);
			$autorefresh = JRequest::getInt('autorefresh',$autorefresh);
			$ignoreembedcat = JRequest::getInt('ignoreembedcat',$currentICS->ignoreembedcat);

			// This is a native ical - so we are only updating identifiers etc
			if ($currentICS->icaltype==2){
				$ics = new iCalICSFile($db);
				$ics->load($icsid);
				$ics->catid=$catid;
				$ics->isdefault=$isdefault;
				$ics->overlaps=$overlaps;
				$ics->access=$access;
				$ics->label=$icsLabel;
				// TODO update access and state
				$ics->updateDetails();
				$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=$redirect_task", JText::_( 'ICS_SAVED' ));
				$this->redirect();
			}

			$state = 1;
			if (JString::strlen($currentICS->srcURL)==0) {
				echo "Can only reload URL based subscriptions";
				return;
			}
			$uploadURL = $currentICS->srcURL;

		}
		else {
			$catid = JRequest::getInt('catid',0);
			$ignoreembedcat = JRequest::getInt('ignoreembedcat',0);
			// Should come from the form or existing item
			$access = JRequest::getInt('access',0);
			$state = 1;
			$uploadURL = JRequest::getVar('uploadURL','' );
			$icsLabel = JRequest::getString('icsLabel','' );                        
		}
		if ($catid==0){
			// Paranoia, should not be here, validation is done by java script
			JFactory::getApplication()->enqueueMessage('Fatal Error - ' . JText::_('JEV_E_WARNCAT') , 'error');

			$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=$redirect_task",  JText::_('JEV_E_WARNCAT'));
			$this->redirect();
			return;
		}

		// I need a better check and expiry information etc.
		if (JString::strlen($uploadURL)>0){
			$icsFile = iCalICSFile::newICSFileFromURL($uploadURL,$icsid,$catid,$access,$state,$icsLabel, $autorefresh, $ignoreembedcat);
		}
		else if (isset($_FILES['upload']) && is_array($_FILES['upload']) ) {
			$file 			= $_FILES['upload'];
			if ($file['size']==0 ){//|| !($file['type']=="text/calendar" || $file['type']=="application/octet-stream")){
				JFactory::getApplication()->enqueueMessage(JText::_('JEV_EMPTY_FILE_UPLOAD'), 'warning');
				$icsFile = false;
			}
			else {
				$icsFile = iCalICSFile::newICSFileFromFile($file,$icsid,$catid,$access,$state,$icsLabel);
			}
		}

		$message = '';
		if ($icsFile !== false) {
			// preserve ownership
			if (isset($currentICS) && $currentICS->created_by>0 ){
                            $icsFile->created_by = $currentICS->created_by;
                        }
                        else $icsFile->created_by = JRequest::getInt("created_by",0);

			$icsFileid = $icsFile->store();
			$message = JText::_( 'ICS_FILE_IMPORTED' );
		}
		if (JRequest::getCmd("task") !== "icals.reloadall")
		{
			$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=$redirect_task";

			if ($guest === 1) {
				$this->setRedirect($link);
			} else
			{
				$this->setRedirect($link, $message);
			}
			$this->redirect();
		}
	}

	/**
	 * This just updates the details not the content of the calendar
	 *
	 */
	function savedetails(){
		$user = JFactory::getUser();
		$authorised = false;

		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		if (JFactory::getApplication()->isAdmin()){
			$redirect_task="icals.list";
		}
		else {
			$redirect_task="month.calendar";
		}

		if (!($authorised || JEVHelper::isAdminUser($user))) {
			$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=$redirect_task", "Not Authorised - must be super admin" );
			$this->redirect();
			return;
		}

		$icsid = intval(JRequest::getVar('icsid',0));
		$cid	= JRequest::getVar(	'cid',	array(0) );
		$cid = ArrayHelper::toInteger($cid);
		if (is_array($cid) && count($cid)>0) {
			$cid=$cid[0];
		} else {
			$cid=0;
		}

		$db	= JFactory::getDbo();

		// include ical files
		

		if ($icsid>0 || $cid!=0){
			$icsid = ($icsid>0)?$icsid:$cid;
			$query = "SELECT icsf.* FROM #__jevents_icsfile as icsf WHERE ics_id=$icsid";
			$db->setQuery($query);
			$currentICS = $db->loadObjectList();
			if (count($currentICS)>0){
				$currentICS= $currentICS[0];
			}
			else {
				$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=$redirect_task", "Invalid Ical Details");
				$this->redirect();
			}

			$catid = JRequest::getInt('catid',$currentICS->catid);
			if ($catid<=0 && $currentICS->catid>0){
				$catid = intval($currentICS->catid);
			}
			$access = intval(JRequest::getVar('access',$currentICS->access));
			if ($access<0 && $currentICS->access>=0){
				$access = intval($currentICS->access);
			}
			$state = intval(JRequest::getVar('state',$currentICS->state));
			if ($state<0 && $currentICS->state>=0){
				$state = intval($currentICS->state);
			}
			$icsLabel = JRequest::getVar('icsLabel',$currentICS->label );
			if ($icsLabel=="" && JString::strlen($currentICS->icsLabel)>=0){
				$icsLabel = $currentICS->icsLabel;
			}
			$uploadURL = JRequest::getVar('uploadURL',$currentICS->srcURL );
			if ($uploadURL=="" && JString::strlen($currentICS->srcURL)>=0){
				$uploadURL = $currentICS->srcURL;
			}
			$isdefault = JRequest::getInt('isdefault',$currentICS->isdefault);
			$overlaps = JRequest::getInt('overlaps',$currentICS->overlaps);
			$autorefesh = JRequest::getInt('autorefresh',$currentICS->autorefresh);
			$ignoreembed = JRequest::getInt('ignoreembedcat',$currentICS->ignoreembedcat);

			// We are only updating identifiers etc
			$ics = new iCalICSFile($db);
			$ics->load($icsid);
			$ics->catid=$catid;
			$ics->isdefault=$isdefault;
			$ics->overlaps=$overlaps;
			$ics->created_by=JRequest::getInt("created_by",$currentICS->created_by);
			$ics->state=$state;
			$ics->access=$access;
			$ics->label=$icsLabel;
			$ics->srcURL= $uploadURL;
			$ics->ignoreembedcat= $ignoreembed;
			// TODO update access and state
			$ics->updateDetails();
			$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=$redirect_task", JText::_( 'ICS_SAVED' ));
			$this->redirect();
		}
	}

	function publish(){
		$cid = JRequest::getVar(	'cid',	array(0) );
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleICalPublish($cid,1);
	}

	function unpublish(){
		$cid = JRequest::getVar(	'cid',	array(0) );
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleICalPublish($cid,0);
	}

	function toggleICalPublish($cid,$newstate){
		$user = JFactory::getUser();
		if (!JEVHelper::isAdminUser($user)) {
			$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			$this->redirect();
			return;
		}

		$db	= JFactory::getDbo();
		foreach ($cid as $id) {
			$sql = "UPDATE #__jevents_icsfile SET state=$newstate where ics_id='".$id."'";
			$db->setQuery($sql);
			$db->execute();
		}
		$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=icals.list", JText::_('JEV_ADMIN_ICALSUPDATED'));
		$this->redirect();
	}

	function autorefresh(){
		$cid = JRequest::getVar(	'cid',	array(0) );
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleAutorefresh($cid,1);
	}

	function noautorefresh(){
		$cid = JRequest::getVar(	'cid',	array(0) );
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleAutorefresh($cid,0);
	}

	function toggleAutorefresh($cid,$newstate){
		$user = JFactory::getUser();
		if (!JEVHelper::isAdminUser($user)) {
			$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			$this->redirect();
			return;
		}

		$db	= JFactory::getDbo();
		foreach ($cid as $id) {
			$sql = "UPDATE #__jevents_icsfile SET autorefresh=$newstate where ics_id='".$id."'";
			$db->setQuery($sql);
			$db->execute();
		}
		$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=icals.list", JText::_('JEV_ADMIN_ICALSUPDATED'));
		$this->redirect();
	}

	function isdefault(){
		$cid = JRequest::getVar(	'cid',	array(0) );
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleDefault($cid,1);
	}

	function notdefault(){
		$cid = JRequest::getVar(	'cid',	array(0) );
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleDefault($cid,0);
	}

	function toggleDefault($cid,$newstate){
		$user = JFactory::getUser();
		if (!JEVHelper::isAdminUser($user)) {
			$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			$this->redirect();
			return;
		}

		$db	= JFactory::getDbo();
		// set all to not default first
		$sql = "UPDATE #__jevents_icsfile SET isdefault=0";
		$db->setQuery($sql);
		$db->execute();

		$id = $cid[0];
		$sql = "UPDATE #__jevents_icsfile SET isdefault=$newstate where ics_id='".$id."'";
		$db->setQuery($sql);
		$db->execute();
		$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=icals.list", JText::_('JEV_ADMIN_ICALSUPDATED'));
		$this->redirect();
	}

	/**
 	* create new ICAL from scratch
 	*/
	function newical() {

		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		// include ical files
		$catid = intval(JRequest::getVar('catid',0));
		// Should come from the form or existing item
		$access = JRequest::getInt('access',0);
		$state = 1;
		$icsLabel = JRequest::getVar('icsLabel','');

		if ($catid==0){
			// Paranoia, should not be here, validation is done by java script
			JFactory::getApplication()->enqueueMessage('Fatal Error - ' . JText::_("JEV_E_WARNCAT"), 'error');

			// Set option variable.
			$option = JEV_COM_COMPONENT;
			JFactory::getApplication()->redirect( 'index.php?option=' . $option);
			return;
		}
                        
                // Check for duplicates
                $db = JFactory::getDbo();
                $query = "SELECT icsf.* FROM #__jevents_icsfile as icsf WHERE label=".$db->quote($icsLabel);
                $db->setQuery($query);
                $existing = $db->loadObject();
                if ($existing){ 
                    JFactory::getApplication()->enqueueMessage(JText::_('JEV_DUPLICATE_CALENDAR') , 'error');

                    $this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=icals.edit");
                    $this->redirect();
                    return;

                }
                
		$icsid = 0;
		$icsFile = iCalICSFile::editICalendar($icsid,$catid,$access,$state,$icsLabel);
                $icsFile->created_by = JRequest::getInt("created_by",0);
		$icsFileid = $icsFile->store();

		$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=icals.list", JText::_( 'ICAL_FILE_CREATED' ));
		$this->redirect();
	}


	function delete(){

		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		$cid	= JRequest::getVar(	'cid',	array(0) );
		$cid = ArrayHelper::toInteger($cid);

		$db	= JFactory::getDbo();

		// check this won't create orphan events
		$query = "SELECT ev_id FROM #__jevents_vevent WHERE icsid in (".implode(",",$cid).")";
		$db->setQuery( $query );
		$kids = $db->loadObjectList();
		if (count($kids)>0){
			$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=icals.list", JText::_("DELETE_CREATES_ORPHAN_EVENTS") );
			$this->redirect();
			return;
		}

		$icsids = $this->_deleteICal($cid);
		$query = "DELETE FROM #__jevents_icsfile WHERE ics_id IN ($icsids)";
		$db->setQuery( $query);
		$db->execute();

		$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=icals.list", "ICal deleted" );
		$this->redirect();
	}

	function _deleteICal($cid){
		$db	= JFactory::getDbo();
		$icsids = implode(",",$cid);

		$query = "SELECT ev_id FROM #__jevents_vevent WHERE icsid IN ($icsids)";
		$db->setQuery( $query);
		$veventids = $db->loadColumn();
		$veventidstring = implode(",",$veventids);

		if ($veventidstring) {
			// TODO the ruccurences should take care of all of these??
			// This would fail if all recurrances have been 'adjusted'
			$query = "SELECT DISTINCT (eventdetail_id) FROM #__jevents_repetition WHERE eventid IN ($veventidstring)";
			$db->setQuery( $query);
			$detailids = $db->loadColumn();
			$detailidstring = implode(",",$detailids);

			$query = "DELETE FROM #__jevents_rrule WHERE eventid IN ($veventidstring)";
			$db->setQuery( $query);
			$db->execute();

			$query = "DELETE FROM #__jevents_repetition WHERE eventid IN ($veventidstring)";
			$db->setQuery( $query);
			$db->execute();

			if ($detailidstring) {
				$query = "DELETE FROM #__jevents_vevdetail WHERE evdet_id IN ($detailidstring)";
				$db->setQuery( $query);
				$db->execute();
			}
		}

		if ($icsids) {
			$query = "DELETE FROM #__jevents_vevent WHERE icsid IN ($icsids)";
			$db->setQuery( $query);
			$db->execute();
		}

		return $icsids;
	}


	function _checkValidCategories(){
		// TODO switch this after migration
		$component_name = "com_jevents";

		$db	= JFactory::getDbo();
		$query = "SELECT COUNT(*) AS count FROM #__categories WHERE extension = '$component_name' AND `published` = 1;";  // RSH 9/28/10 added check for valid published, J!1.6 sets deleted categoris to published = -2
		$db->setQuery($query);
		$count = intval($db->loadResult());
		if ($count<=0){
			// RSH 9/28/10 - Added check for J!1.6 to use different URL for reroute
			$redirectURL = "index.php?option=com_categories&extension=" . JEV_COM_COMPONENT;
			$this->setRedirect($redirectURL, "You must first create at least one category");
			$this->redirect();
		}
	}

}
