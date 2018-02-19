<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: rss.php 3575 2012-05-01 14:06:28Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\String\StringHelper;

// setup document
$doc = JFactory::getDocument();

$doc->setLink($this->info['link']);
$doc->setBase($this->info['base']);
$doc->setTitle($this->info['title']);
$doc->setDescription($this->info['description']);

$docimage =new JFeedImage();
$docimage->description= $this->info['description'];
$docimage->title=$this->info['title'];
$docimage->url= $this->info['image_url'];
$docimage->link= $this->info['imagelink'];
$doc->image =  $docimage;

foreach ($this->eventsByRelDay as $relDay => $ebrd) {
	foreach ($ebrd as $row) {
		// title for particular item
		$item_title = htmlspecialchars( $row->title() );
		$item_title = html_entity_decode( $item_title );

		// url link to article
		$startDate = $row->publish_up();
		//$eventDate = JevDate::mktime(JString::substr($startDate,11,2),JString::substr($startDate,14,2), JString::substr($startDate,17,2),$this->jeventCalObject->now_m,$this->jeventCalObject->now_d + $relDay,$this->jeventCalObject->now_Y);
		$eventDate = JevDate::strtotime($startDate);
		$datenow = JEVHelper::getNow();
		if ($relDay > 0)
		{
			$eventDate = JevDate::strtotime($datenow->toFormat('%Y-%m-%d ') . JevDate::strftime('%H:%M', $eventDate) . " +$relDay days");
		}
		else
		{
			$eventDate = JevDate::strtotime($datenow->toFormat('%Y-%m-%d ') . JevDate::strftime('%H:%M', $eventDate) . " $relDay days");
		}


		$targetid = $this->modparams->get("target_itemid",0);
		$link = $row->viewDetailLink(date("Y", $eventDate),date("m", $eventDate),date("d", $eventDate),false,$targetid);
		$link = str_replace("&tmpl=component","",$link );
		$item_link  = JRoute::_($link.$this->jeventCalObject->datamodel->getCatidsOutLink());

		// removes all formating from the intro text for the description text
		$item_description = $row->content();

		// remove dodgy border e.g. "diamond/question mark"
		$item_description = preg_replace('#border=[\"\'][^0-9]*[\"\']#i', '', $item_description);

		if ( $this->info[ 'limit_text' ] ) {
			if ( $this->info[ 'text_length' ] ) {
				$item_description = JFilterOutput::cleanText( $item_description );
				// limits description text to x words
				$item_description_array = explode( ' ', $item_description );
				$count = count( $item_description_array );
				if ( $count > $this->info[ 'text_length' ] ) {
					$item_description = '';
					for ( $a = 0; $a < $this->info[ 'text_length' ]; $a++ ) {
						$item_description .= $item_description_array[$a]. ' ';
					}
					$item_description = trim( $item_description );
					$item_description .= '...';
				}
			} else  {
				// do not include description when text_length = 0
				$item_description = NULL;
			}
		}
		else {
			// this can lead to double CDATA wrapping which is a problem in Firefox 13+
			//$item_description = "<![CDATA[$item_description]]>"  ;
		}

		// type for particular item - category name
		$item_type = $row->getCategoryName();
		/*
		// You could incorporate these fields into the description for the RSS output
		// organizer for particular item
		$item_organizer = htmlspecialchars( $row->contact_info() );
		$item_organizer = html_entity_decode( $item_organizer );
		// location for particular item
		$item_location = htmlspecialchars( $row->location() );
		$item_location = html_entity_decode( $item_location );
		// start date for particular item
		$item_startdate = htmlspecialchars( $row->publish_up());
		// end date for particular item
		$item_enddate = htmlspecialchars( $row->publish_down() );
		if (isset($row->_thumbimg1) && $row->_thumbimg1!=""){
			$item_description = $row->_thumbimg1. "<br/>".$item_description;
		}
		*/

		// load individual item creator class
		$item = new JFeedItem();
		// item info
		if ($row->alldayevent()) {
			$temptime = new JevDate($eventDate);
			$item->title =  $temptime->toFormat(JText::_('JEV_RSS_DATE')) ." : " .$item_title;
		} else {
			$temptime = new JevDate($eventDate);
			$item->title = $temptime->toFormat(JText::_('JEV_RSS_DATETIME')) ." : " .$item_title;
		}
		$item->link = $item_link;
		$item->description = $item_description;
		$item->category = $item_type;
		
		$eventcreated = new JevDate($row->created());
		$item->date= $eventcreated->toUnix(true);

		// add item info to RSS document
		$doc->addItem( $item );
	}
}
