<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function DefaultSetPageTitle($view, $ev_title){
	  //get menu item id

	$newparams = JFactory::getApplication('site')->getParams();
	// Because the application sets a default page title,
	// we need to get it from the menu item itself
	$menu = JFactory::getApplication()->getMenu()->getActive();
	if (isset($menu->query["layout"]) && $menu->query["layout"]=="detail")
	{
		$newparams->def('page_heading', $newparams->get('page_title', $menu->title));
	}
	else
	{
		JFactory::getDocument()->SetTitle( $ev_title );
	}
}
?>
