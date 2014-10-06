<?php

/**
 * JEvents Component for Joomla 3.x
 *
 * @author      Steve Binkowski
 * @link        https://github.com/routinet
 *
 * Requires CiviCRM (https://civicrm.org) and plugin IncludeCivi (https::/github.com/routinet/IncludeCivi)
 */
defined('_JEXEC') or die('Restricted access');

class jIcalEventRepeatCivi extends jIcalEventRepeat
{
  /*
    Replace the default link with a CiviCRM link.  The links can use:
      event_id: the unique id for the event in CiviCRM
      Itemid:   (Optional) the id of the menu item, used for breadcrumb, et al.

    If event_id is not available, or points to an invalid event, the link will generate
    a CiviCRM not found page.
  */
	function viewDetailLink($year = 0, $month = 0, $day = 0, $sef = true, $Itemid = 0)
	{
	  // make sure this item came from CiviCRM
	  if (property_exists($this, '_civicrm')) {
	    // an example link, pointing to event 1 through menu item 117:
	    // index.php?option=com_civicrm&task=civicrm/event/info&Itemid=117&reset=1&id=1
	    $link = JArrayHelper::getValue($this->civicrm,'url','','string');
	    if (!$link) {
	      $link = "index.php?option=com_civicrm&task=civicrm/event/info&reset=1&id=" .
	              JArrayHelper::getValue($this->civicrm,'event_id','','integer');
	    }
	  }
	  else {
	    $link = parent::viewDetailLink($year, $month, $day, $set, $Itemid);
	  }

		// SEF is applied later
		$link = $sef ? JRoute::_($link, true) : $link;

	  return $link;

	}

}
