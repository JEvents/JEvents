<?php

/**
 * @package     GWE Systems
 * @subpackage  Installer.JEventsInstaller
 *
 * @copyright   Copyright (C)  2016 GWE Systems Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_BASE') or die;


class PlgInstallerJeventsinstaller extends JPlugin
{
           
        /*
         * Download Package URL checking - called from JInstallerHelper::downloadPackage
         */
        public function onInstallerBeforePackageDownload (&$url, &$headers) {
            // Fix the update URL!
            $pos = strpos($url, "www.jevents.net/updates/download");
            if ($pos>0) {
               // echo "Matched JEvents sourced update<br/>";
              //  echo $url."<br/>";
                // split into parts
                $downloadroot = "https://www.jevents.net/updates/download/";
                $updatesroot = "https://www.jevents.net/updates/";
                
                $tempurl = str_replace("www.jevents.net/updates/download/", "", substr($url, $pos ));
                $parts = explode("/", $tempurl);
                if (count($parts)==2){
                    list($codepart, $filepart) = $parts;
                    $filename = substr($filepart, 0, strpos($filepart, "-update-"));
                    //echo $filename."<Br/>";
                    $db = JFactory::getDbo();
                    $db->setQuery("SELECT * FROM #__update_sites WHERE location LIKE ".$db->quote("%".$filename."-update-%"));
                    $updatesite = $db->loadObject();
                    if ($updatesite){
                        $newurl = str_replace(array($updatesroot, ".xml"), array($downloadroot, ".zip") , $updatesite->location);
                        $url = $newurl;
                     //   echo "new url = ".$newurl;
                    }
                }
            }
            
        }
        
}
