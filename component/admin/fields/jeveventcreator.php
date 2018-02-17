<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJeveventcreator extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected
			$type = 'Jeveventcreator';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected
			function getInput()
	{
		$maxDirectNumber = 50;

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		$creator = intval($this->value) > 0 ? intval($this->value) : (isset($user) ? $user->id : 0);

		// If user is jevents can deleteall or has backend access then allow them to specify the creator
		$jevuser = JEVHelper::getAuthorisedUser();
		$user = JFactory::getUser();
		//$access = JAccess::check($user->id, "core.deleteall", "com_jevents");
		$access = $user->authorise('core.admin', 'com_jevents') || $user->authorise('core.deleteall', 'com_jevents');

		$db = JFactory::getDbo();
		if (($jevuser && $jevuser->candeleteall) || $access)
		{
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$authorisedonly = $params->get("authorisedonly", 0);
			// if authorised only then load from database
			if ($authorisedonly)
			{
				$sql = "SELECT count(tl.id) FROM #__jev_users AS tl ";
				$sql .= " LEFT JOIN #__users as ju ON tl.user_id=ju.id ";
				$sql .= " WHERE tl.cancreate=1";
				$sql .= " ORDER BY ju.name ASC";
				$db->setQuery($sql);
				$userCount = $db->loadResult();

				if ($userCount<=$maxDirectNumber) {
					$sql = "SELECT tl.*, ju.*  FROM #__jev_users AS tl ";
					$sql .= " LEFT JOIN #__users as ju ON tl.user_id=ju.id ";
					$sql .= " WHERE tl.cancreate=1";
					$sql .= " ORDER BY ju.name ASC";
					$db->setQuery($sql);
					$users = $db->loadObjectList();
				}
			}
			else
			{
				$rules = JAccess::getAssetRules("com_jevents", true);
				$creatorgroups = $rules->getData();
				// need to merge the arrays because of stupid way Joomla checks super user permissions
				//$creatorgroups = array_merge($creatorgroups["core.admin"]->getData(), $creatorgroups["core.create"]->getData());
				// use union orf arrays sincee getData no longer has string keys in the resultant array
				//$creatorgroups = $creatorgroups["core.admin"]->getData()+ $creatorgroups["core.create"]->getData();
				// use union orf arrays sincee getData no longer has string keys in the resultant array
				$creatorgroupsdata = $creatorgroups["core.admin"]->getData();
				// take the higher permission setting
				foreach ($creatorgroups["core.create"]->getData() as $creatorgroup => $permission)
				{
					if ($permission)
					{
						$creatorgroupsdata[$creatorgroup] = $permission;
					}
				}

				$userids = array(0);
				foreach ($creatorgroupsdata as $creatorgroup => $permission)
				{
					if ($permission == 1)
					{
						$userids = array_merge(JAccess::getUsersByGroup($creatorgroup, true), $userids);
					}
				}
				$sql = "SELECT count(id) FROM #__users where id IN (" . implode(",", array_values($userids)) . ") and block=0 ORDER BY name asc";
				$db->setQuery($sql);
				$userCount = $db->loadResult();

				if ($userCount<=$maxDirectNumber) {
					$sql = "SELECT * FROM #__users where id IN (" . implode(",", array_values($userids)) . ") and block=0 ORDER BY name asc";
					$db->setQuery($sql);
					$users = $db->loadObjectList();
				}
			}

			// get list of creators - if fewer than 200
			if (!isset($users))
			{
				// Use Typeahead instead
				if ($userCount>$maxDirectNumber) {
					$creatorname = "";
					if ($creator>0){
						$sql = "SELECT * FROM #__users where id  = $creator";
						$db->setQuery($sql);
						$creatorData = $db->loadObject();
						if ($creatorData) {
							$creatorname = $creatorData->name ." (".$creatorData->username.")";
						}
					}

					ob_start();
					?>
					<input type="hidden" name='jev_creatorid' id='jev_creatorid' value="<?php echo $creator;?>"/>
					<div id="scrollable-dropdown-menu" style="float:left">
						<input name="creatorid_notused"  id="ta_creatorid" class="jevtypeahead" placeholder="<?php echo $creatorname;?>"  type="text" autocomplete="off" size="50">
					</div>
					<?php
					JLoader::register('JevTypeahead', JPATH_LIBRARIES . "/jevents/jevtypeahead/jevtypeahead.php");
					$datapath = JRoute::_("index.php?option=com_jevents&ttoption=com_jevents&typeaheadtask=gwejson&file=findcreator", false);
					//$prefetchdatapath = JRoute::_("index.php?option=com_jevents&ttoption=com_jevents&typeaheadtask=gwejson&file=findcreator&prefetch=1", false);
					JevTypeahead::typeahead('#ta_creatorid', array('remote' => $datapath,
						//'prefetch'=>  $prefetchdatapath,
						'data_value' => 'title',
						'data_id' => 'creator_id',
						'field_selector' => '#jev_creatorid',
						'minLength' => 2,
						'limit' => 10,
						'scrollable' => 1,));
					return ob_get_clean();
				}

				return "";
			}

			$userOptions[] = JHTML::_('select.option', '-1', JText::_('SELECT_USER'));
			foreach ($users as $user)
			{
				if ($user->id==0){
					continue;
				}
				$userOptions[] = JHTML::_('select.option', $user->id, $user->name . " ( " . $user->username . " )");
			}
			$userlist = JHTML::_('select.genericlist', $userOptions, 'jev_creatorid', 'class="inputbox" size="1" ', 'value', 'text', $creator);

			return $userlist;
		}

		return "";		
	}

}
