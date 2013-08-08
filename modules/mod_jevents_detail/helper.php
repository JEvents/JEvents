<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class modDetailHelper{
    function getDetailBody( ){
          $reg =& JRegistry::getInstance("com_jevents");
          $detail= $reg->get("dynamicmodules"); 
          return $detail;
    }
}
?>